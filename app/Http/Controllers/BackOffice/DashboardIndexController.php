<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardIndexController extends Controller
{
    public function show() {
        return Inertia::render("BackOffice/Admin/Index/Index");
    }
}
