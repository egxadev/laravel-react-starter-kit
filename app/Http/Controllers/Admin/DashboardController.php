<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'href' => '/dashboard'
            ]
        ];

        return inertia('dashboard/index', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
