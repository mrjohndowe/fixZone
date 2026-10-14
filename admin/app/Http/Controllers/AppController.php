<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class AppController extends Controller
{

    /**
     * Loads application index view
     *
     * @return View
     */
    public function index(): View
    {
        if (!File::exists(storage_path('installed')) || !File::exists(base_path('.env'))) {
            return view('install', ['case' => 'environment']);
        }

        return view('app');
    }
}
