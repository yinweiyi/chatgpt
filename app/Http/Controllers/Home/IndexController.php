<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index(): string
    {
        return view('index.index');
    }

}
