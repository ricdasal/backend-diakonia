<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    //
    public function index()
    {
        try {
            $roles = Role::all();
            return response()->json(["roles" => $roles]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return response()->json(["message" => "Servidor fuera de servidor", 501]);
        }
    }
}
