<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected static $title = 'Panel Administrativo';

    private static $message = array(
        'name.required' => 'El nombre es obligatorio.',
        'surname.required' => 'El apellido es obligatorio.',
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email' => 'El correo electrónico debe ser válido.',
        'email.unique' => 'El correo electrónico ya está en uso.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
        'role.required' => 'Debe seleccionar un rol válido.',
        'permissions.*.exists' => 'Algunos de los permisos seleccionados no son válidos.'
    );

    public function index()
    {
        $users = User::with('roles', 'permissions')->get(); // Incluye roles y permisos de cada usuario
        $roles = Role::all(); // Obtiene todos los roles
        $permissions = Permission::all(); // Obtiene todos los permisos

        return view('admin/user/index', compact('users', 'roles', 'permissions'));
    }

    public function show(User $user)
    {
        $roles = Role::all(); // Todos los roles
        $permissions = Permission::all(); // Todos los permisos
        $stockcenters = \App\Models\Stockcenter::all();

        return view('admin/user/edit', compact('user', 'roles', 'permissions', 'stockcenters'));
    }

    public function create()
    {
        $roles = Role::all(); // Todos los roles
        $permissions = Permission::all(); // Todos los permisos
        $stockcenters = \App\Models\Stockcenter::all();

        return view('admin/user/create', compact('roles', 'permissions', 'stockcenters'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
            'is_active' => 'required|boolean|in:1,0',
            'stockcenter_id' => 'nullable|exists:stockcenters,id',
        ];

        $this->validate($request, $rules, static::$message);

        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'is_active' => $request->is_active,
            'stockcenter_id' => $request->stockcenter_id,
        ]);

        $user->syncRoles([$request->role]);
        $user->syncPermissions($request->input('permissions', []));

        return redirect()->route('admin.users.index')
            ->with('mensaje', 'Usuario creado con éxito');
    }

    public function edit(User $user)
    {
        $roles = Role::all(); // Todos los roles
        $permissions = Permission::all(); // Todos los permisos
        $stockcenters = \App\Models\Stockcenter::all();

        return view('admin/user/edit', compact('user', 'roles', 'permissions', 'stockcenters'));
    }

    public function update(Request $request, $id)
    {
        // Definir reglas con el ID dinámico
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id, // Excluir ID actual
            'password' => 'nullable|string|min:8',
            'role' => 'required|exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
            'is_active' => 'required|boolean|in:1,0',
            'stockcenter_id' => 'nullable|exists:stockcenters,id',
        ];
        // Validaciones personalizadas
        $request->merge([
            'password' => $request->password ? bcrypt($request->password) : null,
        ]);

        $this->validate($request, $rules, static::$message);

        // Actualizar datos del usuario
        $dataUser = $request->except(['_token', '_method', 'permissions', 'role', 'password_confirmation']);
        if (!$dataUser['password']) {
            unset($dataUser['password']); // No actualizar la contraseña si está vacía
        }

        $user = User::findOrFail($id);
        $user->update($dataUser);
        // dd($dataUser);
        // Actualizar roles y permisos
        $user->syncRoles([$request->role]);
        $user->syncPermissions($request->permissions);

        return redirect('admin/users/' . $id . '/edit')
            ->with('mensaje', 'Perfil del usuario editado con éxito')
            ->with('title', 'Editar Usuario');
    }
}
