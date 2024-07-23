<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserDataController extends Controller
{
    public function index()
    {
        $UsersData = User::all();
        return view('content.Dashboard.UserData.index', compact('Usersata'));
    }
}
