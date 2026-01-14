<?php

namespace App\Http\Controllers;

use App\Services\TermService;
use Illuminate\Http\Request;

class AcademicTermController extends Controller
{
    public function __construct(
        protected TermService $termService
    ) {}

    public function index()
    {
        return view('academic-terms.index');
    }
}
