<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\User;
use Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
class RoleController extends Controller
{
    public function AllPermission()
    {
        $title = 'Yetkiler';
        $pagetitle = 'Yetki Listesi';
        $permissions = Permission::all();
        return view('admin.backend.pages.permission.all_permission', compact('permissions', 'title', 'pagetitle'));
    }
    public function AddPermission()
    {
        $title = 'Yetki Ekleme';
        $pagetitle = 'Yetki Ekleme İşlemi';
        $permissions = Permission::all();
        return view('admin.backend.pages.permission.add_permission', compact('permissions', 'title', 'pagetitle'));
    }
    public function StorePermission(Request $request)
    {
        Permission::create([
            'name' => $request->name,
            'group_name' => $request->group_name,
        ]);
        $notification = array(
            'message' => 'Yetki Eklendi',
            'alert-type' => 'success'
        );
        return redirect()->route('all.permission')->with($notification);
    }
    public function EditPermission($id)
    {
        $permissions = Permission::find($id);
        $title = 'Yetki Düzenleme';
        $pagetitle = 'Yetki Düzenleme İşlemi';
        return view('admin.backend.pages.permission.edit_permission', compact('permissions', 'title', 'pagetitle'));
    }
    public function UpdatePermission(Request $request)
    {
        $per_id = $request->id;

        Permission::find($per_id)->update([
            'name' => $request->name,
            'group_name' => $request->group_name,
        ]);
        $notification = array(
            'message' => 'Yetki Güncellendi',
            'alert-type' => 'success'
        );
        return redirect()->route('all.permission')->with($notification);
    }
    public function DeletePermission($id)
    {
        $permission = Permission::find($id);
        if ($permission) {
            $permission->delete();
            $notification = array(
                'message' => 'Yetki Silindi',
                'alert-type' => 'success'
            );
        } else {
            $notification = array(
                'message' => 'Yetki Bulunamadı',
                'alert-type' => 'error'
            );
        }
        return redirect()->route('all.permission')->with($notification);
    }
    public function AllRoles()
    {
        $title = 'Roller';
        $pagetitle = 'Rol Listesi';
        $roles = Role::all();
        return view('admin.backend.pages.role.all_role', compact('roles', 'title', 'pagetitle'));
    }
    public function AddRoles()
    {
        $title = 'Rol Ekleme';
        $pagetitle = 'Rol Ekleme İşlemi';
        $permissions = Role::all();
        return view('admin.backend.pages.role.add_role', compact('permissions', 'title', 'pagetitle'));
    }
    public function StoreRoles(Request $request)
    {
        Role::create([
            'name' => $request->name,
        ]);
        $notification = array(
            'message' => 'Role Ekleme İşlemi Başarılı!',
            'alert-type' => 'success'
        );
        return redirect()->route('all.roles')->with($notification);
    }
    public function EditRoles($id)
    {
        $roles = Role::find($id);
        $title = 'Rol Düzenleme';
        $pagetitle = 'Rol Düzenleme İşlemi';
        return view('admin.backend.pages.role.edit_role', compact('roles', 'title', 'pagetitle'));
    }
    public function UpdateRoles(Request $request)
    {
        $per_id = $request->id;

        Role::find($per_id)->update([
            'name' => $request->name,
        ]);
        $notification = array(
            'message' => 'Rol Güncellendi',
            'alert-type' => 'success'
        );
        return redirect()->route('all.roles')->with($notification);
    }
    public function DeleteRoles($id)
    {
        $role = Role::find($id);
        if ($role) {
            $role->delete();
            $notification = array(
                'message' => 'Rol Silindi',
                'alert-type' => 'success'
            );
        } else {
            $notification = array(
                'message' => 'Rol Bulunamadı',
                'alert-type' => 'error'
            );
        }
        return redirect()->route('all.roles')->with($notification);
    }
    public function AddRolesPermission()
    {
        $title = 'Rol Yetki Ekleme';
        $pagetitle = 'Rol Yetki Ekleme İşlemi';
        $roles = Role::all();
        $permissions = Permission::all();

        $permission_groups = User::getpermissionGroups();
        return view('admin.backend.pages.rolesetup.add_roles_permission', compact('roles', 'permissions', 'permission_groups', 'title', 'pagetitle'));
    }
    public function RolePermissionStore(Request $request)
    {
        $request->validate(['role_id' => 'required|exists:roles,id']);

        $role = Role::findOrFail($request->role_id);
        $permissionIds = $request->input('permission', []);

        if (!empty($permissionIds)) {
            $permissions = Permission::whereIn('id', $permissionIds)->pluck('name');
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        $notification = array(
            'message' => 'Rol Yetkisi Başarıyla Güncellendi',
            'alert-type' => 'success'
        );
        return redirect()->route('all.roles.permission')->with($notification);
    }
    public function AllRolesPermission()
    {
        $title = 'Rol Yetkileri';
        $pagetitle = 'Rol Yetkileri Listesi';
        $roles = Role::all();
        $permissions = Permission::all();
        $permission_groups = User::getpermissionGroups();
        return view('admin.backend.pages.rolesetup.all_roles_permission', compact('roles', 'permissions', 'permission_groups', 'title', 'pagetitle'));
    }
    public function AdminEditRoles($id)
    {
        $title = 'Rol Düzenleme';
        $pagetitle = 'Rol Düzenleme İşlemi';
        $roles = Role::find($id);
        $permissions = Permission::all();
        $permission_groups = User::getpermissionGroups();
        return view('admin.backend.pages.rolesetup.edit_roles_permission', compact('roles', 'permissions', 'permission_groups', 'title', 'pagetitle'));
    }
    public function AdminRolesUpdate(Request $request, $id)
    {
        $roles = Role::find($id);
        $permissions = $request->permission;
        if (!empty($permissions)) {
            $permissionsName = Permission::whereIn('id', $permissions)
                ->pluck('name')->toArray();
            $roles->syncPermissions($permissionsName);
        } else {
            $roles->syncPermissions([]);
        }
        $notification = array(
            'message' => 'Rol Yetkisi Başarıyla Güncellendi',
            'alert-type' => 'success'
        );
        return redirect()->route('all.roles.permission')->with($notification);
    }
    public function AdminRolesDelete($id)
    {
        $roles = Role::find($id);
        if (!is_null($roles)) {
            $roles->delete();
            $notification = array(
                'message' => 'Rol Silindi',
                'alert-type' => 'success'
            );
        } else {
            $notification = array(
                'message' => 'Rol Bulunamadı',
                'alert-type' => 'error'
            );
        }
        return redirect()->back()->with($notification);
    }
    public function AllAdmin2()
    {
        if (!Auth::user()->hasPermissionTo('kullanici.menu')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }
        $title = 'Kullanıcı Listesi';
        $pagetitle = 'Kullanıcı Listesi';
        $alladmin = User::where('role', 'admin')->where('status','1')->latest()->get();
        return view('admin.backend.pages.admin.all_admin', compact('alladmin', 'title', 'pagetitle'));
    }
    public function AllAdmin_yetkisiz()
    {
    if (!Auth::user()->hasPermissionTo('kullanici.menu')) {
        abort(403, 'Yetkiniz Bulunmamakta!');
    }

    $title = 'Kullanıcı Listesi';
    $pagetitle = 'Kullanıcı Listesi';

    $alladmin = User::with('birim') // <-- ilişkiyi dahil ettik
        ->where('role', 'admin')
        ->where('status', '1')
        //->latest()
        ->orderBy('birim_id', 'asc')
        ->orderBy('yonetici', 'asc')        
        ->get();

    return view('admin.backend.pages.admin.all_admin', compact('alladmin', 'title', 'pagetitle'));
    }
    public function AllAdmin()
    {
        if (!Auth::user()->hasPermissionTo('kullanici.menu')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }

        $user = auth()->user();
        $bolge_id = $user->bolge_id;
        $kurum_id = $user->kurum_id;
        $birim_id = $user->birim_id;
        $isYonetici = $user->yonetici == 1;

        $title = 'Kullanıcı Listesi';
        $pagetitle = 'Kullanıcı Listesi';

        $alladmin = User::with('birim')
            ->where('role', 'admin')
            ->where('status', '1')
            ->when($isYonetici, function ($query) use ($bolge_id) {
                return $query->where('bolge_id', $bolge_id);
            }, function ($query) use ($birim_id) {
                return $query->where('birim_id', $birim_id);
            })
            ->orderBy('birim_id', 'asc')
            ->orderBy('yonetici', 'asc')
            ->get();

        return view('admin.backend.pages.admin.all_admin', compact('alladmin', 'title', 'pagetitle'));
    }
    public function AllAdminLog2()
    {
        if (!Auth::user()->hasPermissionTo('kullanici.log')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }
        $title = 'Kullanıcı Log Listesi';
        $pagetitle = 'Kullanıcı Log Listesi';
        $alladmin = DB::table('login_logs')
            ->join('users', 'login_logs.user_id', '=', 'users.id')
            ->select(
                'login_logs.*',
                'users.name as user_name'
            )
            ->where('kurum_id', auth()->user()->kurum_id)
            ->latest('login_logs.logged_in_at')
            ->limit(100)
            ->get();
        return view('admin.backend.pages.admin.all_adminlog', compact('alladmin', 'title', 'pagetitle'));
    }
    public function AllAdminLog()
    {
        if (!Auth::user()->hasPermissionTo('kullanici.log')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }
    
        $user = auth()->user();
        $bolge_id = $user->bolge_id;
        $kurum_id = $user->kurum_id;
        $birim_id = $user->birim_id;
        $isYonetici = $user->yonetici == 1;
    
        $title = 'Kullanıcı Log Listesi';
        $pagetitle = 'Kullanıcı Log Listesi';
    
        $alladmin = DB::table('login_logs')
            ->join('users', 'login_logs.user_id', '=', 'users.id')
            ->leftJoin('birim', 'users.birim_id', '=', 'birim.birim_id') 
            ->select(
                'login_logs.*',
                'users.name as user_name',
                'users.birim_id',
                'users.bolge_id',
                'users.kurum_id',
                'users.yonetici',
                'birim.birim_ad'
            )
            ->where('users.status', '1')
            ->where('users.role', 'admin')
            ->where('users.kurum_id', $kurum_id)
            ->when($isYonetici, function ($query) use ($bolge_id) {
                // Yönetici ise kendi bölgesindeki kullanıcıların logları
                return $query->where('users.bolge_id', $bolge_id);
            }, function ($query) use ($birim_id) {
                // Yönetici değilse kendi birimindeki kullanıcıların logları
                return $query->where('users.birim_id', $birim_id);
            })
            ->latest('login_logs.logged_in_at')
            ->limit(100)
            ->get();
    
        return view('admin.backend.pages.admin.all_adminlog', compact('alladmin', 'title', 'pagetitle'));
    }
    public function AddAdmin()
    {
        $title = 'Admin Ekleme';
        $pagetitle = 'Admin Ekleme İşlemi';
        $roles = Role::all();
        return view('admin.backend.pages.admin.add_admin', compact('roles', 'title', 'pagetitle'));
    }
    public function StoreAdmin(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = 'admin';
        $user->bolge_id = auth()->user()->bolge_id;
        $user->kurum_id = auth()->user()->kurum_id;
        $user->birim_id = auth()->user()->birim_id;
        $user->save();

        if ($request->roles) {
            $role = Role::where('id', $request->roles)->where('guard_name', 'web')->first();
            if ($role) {
                $user->assignRole($role->name);
            }
        }
        $notification = array(
            'message' => 'Yeni Kullanıcı Ekleme İşlemi Başarılı!',
            'alert-type' => 'success'
        );
        return redirect()->route('all.admin')->with($notification);
    }
    public function EditAdmin($id)
    {
        $title = 'Admin Düzenleme';
        $pagetitle = 'Admin Düzenleme İşlemi';
        $admin = User::find($id);
        $roles = Role::all();
        return view('admin.backend.pages.admin.edit_admin', compact('admin', 'roles', 'title', 'pagetitle'));
    }
    public function UpdateAdmin(Request $request, $id)
    {
        $user = User::find($id);
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->yonetici = $request->yonetici;
        $user->role = 'admin';
        $user->save();

        $user->roles()->detach(); // Remove all roles first
        if ($request->roles) {
            $role = Role::where('id', $request->roles)->where('guard_name', 'web')->first();
            if ($role) {
                $user->assignRole($role->name);
            }
        }
        $notification = array(
            'message' => 'Kullanıcı Bilgileri Başarıyla Güncellendi',
            'alert-type' => 'success'
        );
        return redirect()->route('all.admin')->with($notification);
    }
    public function DeleteAdmin($id)
    {
        $admin = User::find($id);
        if (!is_null($admin)) {
            $admin->delete();
            $notification = array(
                'message' => 'Kullanıcı Başarıyla Silindi',
                'alert-type' => 'success'
            );
        } else {
            $notification = array(
                'message' => 'Kullanıcı Bulunamadı',
                'alert-type' => 'error'
            );
        }
        return redirect()->back()->with($notification);
    }
}