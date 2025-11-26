<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index()
    {
        return view('statistics.index');
    }

    public function trends()
    {
        return view('statistics.trends');
    }

    public function detailedExport()
    {
        return view('statistics.detailed-export');
    }

    public function comparison()
    {
        return view('statistics.comparison');
    }
}
