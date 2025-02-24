<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PonenteRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PonenteCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PonenteCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Ponente::class);
        //CRUD::setRoute(config('backpack.base.route_prefix') . '/ponente');
        CRUD::setRoute(config('backpack.base.route_prefix', 'admin') . '/ponentes');
        CRUD::setEntityNameStrings('ponente', 'ponentes');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        //Lo trata automáticamente, así que lo comento para tratar el campo fotografía de otra forma//
        //CRUD::setFromDb(); // set columns from db columns.

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */

        // Configuración de CRUD
        $this->crud->setModel('App\Models\Ponente');       // Modelo Ponente
        $this->crud->setRoute('admin/ponente');           // Ruta del CRUD
        $this->crud->setEntityNameStrings('ponente', 'ponentes'); // Singular/plural




        CRUD::addColumn([
        'name' => 'nombre',
        'type' => 'text',
        'label' => 'Nombre',
    ]);

    /*
    CRUD::addColumn([
        'name' => 'fotografia',
        'type' => 'image', // Muestra una vista previa de la imagen en la tabla
        'label' => 'Fotografía',
        'prefix' => 'ponentes/', // Opcional: prepender "storage/" si no está en la base de datos
    ]);
    */

        CRUD::addColumn([
            'name' => 'fotografia',
            'label' => 'Fotografía',
            'type' => 'image',
            'prefix' => '', // No necesitas dejar rutas precargadas
            'value' => function ($entry) {
                // Genera la URL permanente desde la base de datos
                return route('mostrar-imagen', ['filename' => $entry->fotografia]);
            },
        ]);

    CRUD::addColumn([
        'name' => 'area_experiencia',
        'type' => 'text',
        'label' => 'Área de experiencia',
    ]);

    CRUD::addColumn([
        'name' => 'red_social',
        'type' => 'url',
        'label' => 'Red Social',
    ]);

    CRUD::addColumn([
        'name' => 'created_at',
        'type' => 'datetime',
        'label' => 'Creado el',
    ]);

    CRUD::addColumn([
        'name' => 'updated_at',
        'type' => 'datetime',
        'label' => 'Actualizado el',
    ]);
    }


    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(PonenteRequest::class);
        //CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

         // Campos del formulario
        CRUD::addField([
            'name' => 'nombre',
            'type' => 'text',
            'label' => 'Nombre',
        ]);

        CRUD::addField([
            'name' => 'fotografia',
            'type' => 'upload',
            'label' => 'Fotografía',
            'upload' => true,
            'disk' => 'local',
            'prefix' => 'ponentes/',
        ]);

        CRUD::addField([
            'name' => 'area_experiencia',
            'type' => 'text',
            'label' => 'Área de experiencia',
        ]);

        CRUD::addField([
            'name' => 'red_social',
            'type' => 'url',
            'label' => 'Red Social',
        ]);

    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
