@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 max-w-3xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Editar Material</h1>
        <a href="{{ route('dados_mestres.materiais.index') }}" class="px-5 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">⬅ Voltar</a>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-6 rounded-xl shadow-lg">
        <form action="{{ route('dados_mestres.materiais.update', $material->MaterialId) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="Nome" class="block text-gray-700 font-semibold mb-2">Nome</label>
                <input type="text" id="Nome" name="Nome"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       value="{{ old('Nome', $material->Nome) }}" required>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('dados_mestres.materiais.index') }}" class="px-5 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Cancelar</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Atualizar</button>
            </div>
        </form>
    </div>
</div>
@endsection
