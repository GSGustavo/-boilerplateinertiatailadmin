<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getitems() {

        $users = User::select('id', 'name')->get();

        $data = [
            'items' => $users
        ];


        return response()->json($data);
    }
}
