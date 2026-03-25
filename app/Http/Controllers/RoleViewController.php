<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleViewController extends Controller
{
    public function superAdmin() {
        return view('roles.super_admin');
    }

    public function administrador() {
        return view('roles.administrador');
    }

    public function encargado() {
        return view('roles.encargado');
    }
}
