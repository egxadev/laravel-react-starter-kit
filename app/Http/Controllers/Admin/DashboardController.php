<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'href' => route('dashboard.index'),
            ],
        ];

        return inertia('dashboard/index', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
