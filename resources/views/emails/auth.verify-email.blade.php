@extends('layouts.app')

@section('title', 'Verifique seu e-mail')
@section('page-title', 'Confirme seu e-mail')

@section('content')
<div class="max-w-lg mx-auto bg-white shadow-md rounded-lg p-6 text-center">
    <h2 class="text-xl font-bold mb-4">Verifique seu e-mail</h2>
    <p>Um link de verificação foi enviado para seu e-mail. Por favor, clique nele para ativar sua conta.</p>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Reenviar e-mail de verificação
        </button>
    </form>
</div>
@endsection
