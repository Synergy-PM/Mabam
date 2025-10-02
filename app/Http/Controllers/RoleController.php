<?php

namespace App\Http\Controllers;

use App\Models\Role; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('role_view')) {
            abort(403, 'Unauthorized');
        }

        $roles = Role::orderBy('created_at', 'desc')->get();
        return view('admin.role.index', compact('roles'));
    }

    public function create()
    {
        if (!auth()->user()->can('role_create')) {
            abort(403, 'Unauthorized');
        }

        $permissions = Permission::get();
        return view('admin.role.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'web',
            ]);

            logUserActivity('Role', 'Created Role ' . $role->name, $role->id, 'Role');

            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            }

            DB::commit();
            return redirect()->route('role.index')->with('success', 'Role created successfully.');
        } catch (\Exception $ex) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error creating role: ' . $ex->getMessage());
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->can('role_edit')) {
            abort(403, 'Unauthorized');
        }

        $role = Role::findOrFail($id);
        $permissions = Permission::get();
        return view('admin.role.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $role = Role::findOrFail($id);
            $role->name = $request->name;
            $role->save();

            logUserActivity('Role', 'Updated Role ' . $role->name, $role->id, 'Role');

            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            }

            DB::commit();
            return redirect()->route('role.index')->with('success', 'Role updated successfully.');
        } catch (\Exception $ex) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating role: ' . $ex->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $role = Role::findOrFail($id);

            logUserActivity('Role', 'Deleted Role ' . $role->name, $role->id, 'Role');

            $role->delete();

            DB::commit();
            return redirect()->route('role.index')->with('success', 'Role deleted successfully.');
        } catch (\Exception $ex) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error deleting role: ' . $ex->getMessage());
        }
    }
        public function trash()
    {
        $roles = Role::onlyTrashed()->get();
        return view('admin.role.trash', compact('roles'));
    }

    public function restore($id)
    {
        $role = Role::onlyTrashed()->findOrFail($id);
        $role->restore();

        return redirect()->route('role.trash')->with('success', 'Role restored successfully.');
    }
}
