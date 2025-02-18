<?php

namespace App\Http\Controllers;

use App\Models\TipoInscripcion;
use Illuminate\Http\Request;

class TipoInscripcionController extends Controller
{
    public function index()
    {
        $tiposInscripcion = TipoInscripcion::all();
        return view('tipos-inscripcion.index', compact('tiposInscripcion'));
    }
}
