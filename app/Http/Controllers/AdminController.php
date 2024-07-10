<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin', compact('users'));
    }

    public function getLocations(int $userId)
    {
        $currentUser = User::find($userId); // Получаем пользователя по его ID
        $marks = $currentUser->marks; // Получаем все отметки данного пользователя

        return view('locations', compact('marks'));
    }
}
