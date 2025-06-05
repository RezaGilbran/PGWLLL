<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Home',
        ];
        return view('home', $data);
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function table()
    {
        return view('table');
    }

    public function map()
    {
        return view('map');
    }
}
