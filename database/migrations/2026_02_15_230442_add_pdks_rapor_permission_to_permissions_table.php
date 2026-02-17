<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('permissions', 'group_name')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->string('group_name')->nullable()->after('guard_name');
            });
        }

        $exists = DB::table('permissions')
            ->where('name', 'pdks.rapor')
            ->where('guard_name', 'web')
            ->exists();

        if (!$exists) {
            $maxId = (int) DB::table('permissions')->max('id');
            $nextId = $maxId + 1;
            DB::table('permissions')->insert([
                'id' => $nextId,
                'name' => 'pdks.rapor',
                'guard_name' => 'web',
                'group_name' => 'PDKS',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')
            ->where('name', 'pdks.rapor')
            ->where('guard_name', 'web')
            ->delete();
    }
};
