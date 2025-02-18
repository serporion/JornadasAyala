{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<x-backpack::menu-item title="Roles" icon="la la-question" :link="backpack_url('role')" />
<x-backpack::menu-item title="Users" icon="la la-question" :link="backpack_url('user')" />
<x-backpack::menu-item title="Eventos" icon="la la-question" :link="backpack_url('evento')" />
<x-backpack::menu-item title="Ponentes" icon="la la-question" :link="backpack_url('ponente')" />
<x-backpack::menu-item title="Tipo inscripcions" icon="la la-question" :link="backpack_url('tipo-inscripcion')" />
<x-backpack::menu-item title="Inscripcions" icon="la la-question" :link="backpack_url('inscripcion')" />
<x-backpack::menu-item title="Evento ponentes" icon="la la-question" :link="backpack_url('evento-ponente')" />
<x-backpack::menu-item title="Asistente eventos" icon="la la-question" :link="backpack_url('asistente-evento')" />