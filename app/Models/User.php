<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *protected $guarded = ['id', 'is_admin', 'created_at', 'updated_at'];
     * @var list<string>
     */
    /*    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'photo',

    ];
*/
    protected $guarded = [];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public static function getpermissionGroups()
    {
        $permission_groups = DB::table('permissions')->select('group_name')
            ->groupBy('group_name')->get();
        return $permission_groups;
    }
    public static function getpermissionByGroupName($group_name)
    {
        $permissions = DB::table('permissions')
            ->select('name', 'id')
            ->where('group_name', $group_name)
            ->get();
        return $permissions;
    }
    public static function roleHasPermissions($role, $permissions)
    {
        $hasPermission = true;
        foreach ($permissions as $key => $permission) {
            if (!self::roleHasPermissionSafe($role, $permission->name)) {
                $hasPermission = false;
                break;
            }
        }
        return $hasPermission;
    }

    /**
     * Rolün yetkiye sahip olup olmadığını kontrol eder. Yetki veritabanında yoksa (silinmiş vb.) hata fırlatmaz.
     */
    public static function roleHasPermissionSafe($role, string $permissionName): bool
    {
        try {
            return $role->hasPermissionTo($permissionName);
        } catch (PermissionDoesNotExist $e) {
            return false;
        }
    }
    public function birim()
    {
        return $this->belongsTo(Birim::class, 'birim_id', 'birim_id');
    }
}
