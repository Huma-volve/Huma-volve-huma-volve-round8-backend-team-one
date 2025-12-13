<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        if ($request->user()->user_type === 'admin') {
            return view('admin.dashboard');
        }

        return view('dashboard');
    }
}