<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EncryptEnvValue extends Command
{
    protected $signature = 'env:encrypt-value
                            {value? : Şifrelenecek değer (boş bırakılırsa sorulur)}
                            {--env-key= : .env dosyasındaki anahtar adı (ör: DB_PASSWORD)}
                            {--update : .env dosyasını otomatik güncelle}';

    protected $description = '.env değerlerini şifreler. Şifreleme anahtarı Windows ortam değişkeninden (LARAVEL_ENV_KEY) okunur.';

    public function handle(): int
    {
        $encryptionKey = getenv('LARAVEL_ENV_KEY')
            ?: ($_SERVER['LARAVEL_ENV_KEY'] ?? $_ENV['LARAVEL_ENV_KEY'] ?? null);

        if (!$encryptionKey) {
            $this->error('LARAVEL_ENV_KEY ortam değişkeni bulunamadı!');
            $this->newLine();
            $this->warn('Önce PowerShell\'de şu komutu çalıştırın:');
            $this->line('  [System.Environment]::SetEnvironmentVariable(\'LARAVEL_ENV_KEY\', \'ANAHTAR_DEGER\', \'User\')');
            $this->line('  Ardından terminali yeniden başlatın.');
            return Command::FAILURE;
        }

        $value = $this->argument('value');

        if (empty($value)) {
            $value = $this->secret('Şifrelenecek değeri girin');
        }

        if (empty($value)) {
            $this->error('Boş değer şifrelenemez.');
            return Command::FAILURE;
        }

        $encrypted = encrypt_env_value($value, $encryptionKey);

        $this->newLine();
        $this->info('Şifrelenmiş değer:');
        $this->line($encrypted);

        // Doğrulama
        $decrypted = decrypt_env_value($encrypted);
        if ($decrypted === $value) {
            $this->newLine();
            $this->info('Doğrulama: Şifre çözme başarılı.');
        } else {
            $this->error('Doğrulama başarısız! Şifreleme/çözme uyuşmuyor.');
            return Command::FAILURE;
        }

        // .env dosyasını otomatik güncelle
        $envKeyName = $this->option('env-key');

        if ($this->option('update') && $envKeyName) {
            $this->updateEnvFile($envKeyName, $encrypted);
            $this->newLine();
            $this->info(".env dosyasında {$envKeyName} güncellendi.");
        } elseif ($envKeyName) {
            $this->newLine();
            $this->warn(".env dosyanızda şu şekilde kullanın:");
            $this->line("  {$envKeyName}=\"{$encrypted}\"");
        } else {
            $this->newLine();
            $this->warn('.env dosyanıza bu değeri yapıştırın (çift tırnak içinde).');
        }

        return Command::SUCCESS;
    }

    private function updateEnvFile(string $key, string $value): void
    {
        $envPath = base_path('.env');
        $content = file_get_contents($envPath);

        $pattern = "/^" . preg_quote($key, '/') . "=.*/m";

        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, "{$key}=\"{$value}\"", $content);
        } else {
            $content .= "\n{$key}=\"{$value}\"\n";
        }

        file_put_contents($envPath, $content);
    }
}
