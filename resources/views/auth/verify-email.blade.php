@extends('layouts.app')

@section('title', 'Verifica Tu Email Jornadas Ayala')

@section('content')

<div class="container mx-auto py-12">
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold text-center mb-6">{{ __('Verifica tu email') }}</h1>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Ahora mismo está logueado. Debes estarlo antes de confirmar tu password mediante el enlace que se te ha enviado a tu correo electrónico. Una vez hecho esto, ya podrás acceder a tu area personal.') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between">
            <!-- Resend Verification Email -->
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
                    {{ __('Reenviar Email de Confirmación') }}
                </button>
            </form>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>
</div>

@endsection <!-- Fin de la sección "content" -->
