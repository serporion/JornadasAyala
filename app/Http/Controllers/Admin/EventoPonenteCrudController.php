<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EventoPonenteRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class EventoPonenteCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class EventoPonenteCrudController extends CrudController
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
        CRUD::setModel(\App\Models\EventoPonente::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/evento-ponente');
        CRUD::setEntityNameStrings('evento ponente', 'evento ponentes');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */

    protected function setupListOperation()
    {
        //CRUD::setFromDb(); // Uso por defecto, por BackPack pero no mostraria
                            // el nombre o descripcion de un codigo en la vista

        CRUD::column('evento.nombre'); // Muestra el nombre del Evento
        CRUD::column('ponente.nombre'); // Muestra el nombre del Ponente
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    /*
    protected function setupCreateOperation()
    {
        CRUD::setValidation(EventoPonenteRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');

    }
    */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(EventoPonenteRequest::class);

        CRUD::field('event_id')
            ->type('select')
            ->model('App\Models\Evento')
            ->attribute('nombre');

        CRUD::field('speaker_id')
            ->type('select')
            ->model('App\Models\Ponente')
            ->attribute('nombre');
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
