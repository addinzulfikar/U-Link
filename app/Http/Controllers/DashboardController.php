<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show user dashboard
     */
    public function index()
    {
        return view('dashboard.user');
    }

    /**
     * Show admin toko dashboard
     */
    public function adminToko()
    {
        return view('dashboard.admin-toko');
    }

    /**
     * Show super admin dashboard
     */
    public function superAdmin()
    {
        return view('dashboard.super-admin');
    }
}
