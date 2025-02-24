<?php

namespace App\Http\Controllers;

use App\Models\Ponente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PonenteController extends Controller
{

    public function index()
    {
        /* Sería correcto si no hubiera relaciones, y si las hay, no las quisieramos mostrar.
            $ponentes = Ponente::all();
            return view('ponentes.index', compact('ponentes'));
        */

        // Obtener todos los ponentes, incluyendo los eventos asociados, pero muestra duplicados
        // $ponentes = Ponente::with('eventos')->get();
        /*
            $ponentes = Ponente::with(['eventos' => function ($query) {
                $query->distinct();
            }])->get();
        */

        // Obtener todos los ponentes, incluyendo los eventos asociados, no debería de mostrar
        // duplicados si es el "id" el que manda. Mi base de datos tiene un id diferente por cada
        // uno de los talleres y conferencias aunque el nombre del taller/conferencia sean iguales.
        // Filtro por "nombre" del evento en el foreach.


        $ponentes = Ponente::with('eventos')->get();

        foreach ($ponentes as $ponente) {
            $ponente->eventos = $ponente->eventos->unique('nombre'); // Usamos 'id' o el campo que hace único cada evento
        }

        return view('ponentesWeb.index', compact('ponentes'));
    }


    public function indexMostrar()
    {


        $ponentes = Ponente::with('eventos')->get();

        foreach ($ponentes as $ponente) {
            $ponente->eventos = $ponente->eventos->unique('nombre'); // Usamos 'id' o el campo que hace único cada evento
        }

        return view('ponentesWeb.index', compact('ponentes'));
    }

    /**
     *  Método que gestiona la recuperación de un archivo de imagen del directorio protegido "ponentes".
     *  Este método construye la ruta del archivo usando el nombre de archivo proporcionado y verifica
     *  si el archivo existe dentro del almacenamiento local de la aplicación. Si se encuentra el archivo,
     * se devuelve como respuesta. De lo contrario, el método se cancela con un error 404.
     *
     * @param string $filename The name of the file to retrieve from storage.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse The file response if the file exists.
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If the file is not found.
     */
    public function mostrarImagen($filename)
    {
        // Construir la ruta del archivo dentro de la carpeta protegida
        $path = 'ponentes/' . $filename;

        if (Storage::disk('local')->exists($path)) {
            return response()->file(storage_path('app/' . $path));
        }

        abort(404, 'Imagen no encontrada.');
    }

}
