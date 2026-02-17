<?php

if (!function_exists('decrypt_env_value')) {
    /**
     * ENC: prefix'i ile şifrelenmiş env değerlerini çözer.
     * Şifreleme anahtarı sırasıyla şu kaynaklardan okunur:
     *  1. PHP ortam değişkenleri (getenv, $_SERVER, $_ENV)
     *  2. Windows Registry (Machine ve User seviyesi)
     */
    function decrypt_env_value(?string $value, string $default = ''): string
    {
        if (empty($value) || !str_starts_with($value, 'ENC:')) {
            return $value ?? $default;
        }

        $encoded = substr($value, 4);

        $key = get_env_encryption_key();

        if (!$key) {
            error_log('LARAVEL_ENV_KEY ortam değişkeni bulunamadı. Şifrelenmiş değer çözülemedi.');
            return $default;
        }

        $data = base64_decode($encoded, true);

        if ($data === false) {
            error_log('Şifrelenmiş env değeri geçerli base64 formatında değil.');
            return $default;
        }

        $cipher = 'aes-256-cbc';
        $ivLength = openssl_cipher_iv_length($cipher);

        if (strlen($data) <= $ivLength) {
            error_log('Şifrelenmiş env değeri çok kısa, geçersiz format.');
            return $default;
        }

        $iv = substr($data, 0, $ivLength);
        $ciphertext = substr($data, $ivLength);

        $decrypted = openssl_decrypt(
            $ciphertext,
            $cipher,
            hash('sha256', $key, true),
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($decrypted === false) {
            error_log('Env değeri şifre çözme başarısız. LARAVEL_ENV_KEY doğru olmayabilir.');
            return $default;
        }

        return $decrypted;
    }
}

if (!function_exists('get_env_encryption_key')) {
    /**
     * Şifreleme anahtarını çeşitli kaynaklardan okur.
     * PHP ortam değişkenlerinden bulamazsa Windows Registry'den dener.
     */
    function get_env_encryption_key(): ?string
    {
        // 1. Standart PHP ortam değişkenlerinden oku
        $key = getenv('LARAVEL_ENV_KEY');

        if (!$key && isset($_SERVER['LARAVEL_ENV_KEY'])) {
            $key = $_SERVER['LARAVEL_ENV_KEY'];
        }

        if (!$key && isset($_ENV['LARAVEL_ENV_KEY'])) {
            $key = $_ENV['LARAVEL_ENV_KEY'];
        }

        if ($key) {
            return $key;
        }

        // 2. Windows'ta Registry üzerinden sistem ortam değişkenini oku
        if (PHP_OS_FAMILY === 'Windows') {
            $key = read_windows_env_variable('LARAVEL_ENV_KEY');
            if ($key) {
                return $key;
            }
        }

        return null;
    }
}

if (!function_exists('read_windows_env_variable')) {
    /**
     * Windows Registry'den ortam değişkenini doğrudan okur.
     * Apache/WAMP ortam değişkenlerini göremese bile Registry'den okuyabilir.
     */
    function read_windows_env_variable(string $name): ?string
    {
        // Önce Machine (Sistem) seviyesini dene
        $regPaths = [
            'HKLM\\SYSTEM\\CurrentControlSet\\Control\\Session Manager\\Environment',
            'HKCU\\Environment',
        ];

        foreach ($regPaths as $regPath) {
            $output = [];
            $exitCode = 0;
            @exec(
                'reg query "' . $regPath . '" /v ' . escapeshellarg($name) . ' 2>nul',
                $output,
                $exitCode
            );

            if ($exitCode === 0 && !empty($output)) {
                foreach ($output as $line) {
                    if (stripos($line, $name) !== false && stripos($line, 'REG_') !== false) {
                        $parts = preg_split('/\s{2,}/', trim($line));
                        if (isset($parts[2])) {
                            return $parts[2];
                        }
                    }
                }
            }
        }

        return null;
    }
}

if (!function_exists('encrypt_env_value')) {
    /**
     * Verilen değeri AES-256-CBC ile şifreler ve ENC: prefix'i ile döndürür.
     */
    function encrypt_env_value(string $value, string $key): string
    {
        $cipher = 'aes-256-cbc';
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivLength);

        $encrypted = openssl_encrypt(
            $value,
            $cipher,
            hash('sha256', $key, true),
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($encrypted === false) {
            throw new RuntimeException('Şifreleme işlemi başarısız oldu.');
        }

        return 'ENC:' . base64_encode($iv . $encrypted);
    }
}
