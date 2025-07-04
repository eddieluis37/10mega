<?php

namespace App\Http\Controllers;

use App\Exports\UsersStoresExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function profile()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Puedes pasar información adicional a la vista si es necesario
        return view('users.profile', compact('user'));
    }

    public function export()
    {
        return Excel::download(new UsersStoresExport, 'usuarios_bodegas.xlsx');
    }
}
