<?php

namespace App\Http\Controllers;

use App\Models\Ponente;
use Illuminate\Http\Request;

class PonenteController extends Controller
{
    public function index()
    {
        $ponentes = Ponente::all();
        return view('ponentes.index', compact('ponentes'));
    }

}
