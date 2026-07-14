@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 max-w-6xl">

    {{-- Título --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-gray-800">Nova Subcategoria</h1>
        <a href="{{ route('dados_mestres.subcategorias.index') }}"
           class="px-6 py-2 bg-gray-200 text-gray-800 font-semibold rounded-lg shadow hover:bg-gray-300 transition w-full sm:w-auto text-center">
            Voltar
        </a>
    </div>

    {{-- Mensagem de erro --}}
    @if ($errors->any())
        <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800 border border-red-300">
            <ul class="mb-0">
                @foreach ($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulário --}}
    <div class="bg-white p-6 rounded-xl shadow-lg">
        <form action="{{ route('dados_mestres.subcategorias.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                {{-- Grupo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grupo</label>
                    <select id="grupoSelect" class="w-full border-gray-300 rounded-lg p-2 text-gray-700" required>
                        <option value="">-- Selecione --</option>
                        @foreach ($grupos as $g)
                            <option value="{{ $g->GrupoId }}">{{ $g->Nome }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Categoria --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                    <select name="CategoriaId" id="categoriaSelect" class="w-full border-gray-300 rounded-lg p-2 text-gray-700" required>
                        <option value="">-- Selecione um grupo primeiro --</option>
                    </select>
                </div>

                {{-- Nome --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Subcategoria</label>
                    <input type="text" name="Nome" placeholder="Ex: Equipamentos de TI"
                           class="w-full border-gray-300 rounded-lg p-2 text-gray-700" required>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 transition">
                    💾 Guardar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Script AJAX --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const grupoSelect = document.getElementById('grupoSelect');
    const categoriaSelect = document.getElementById('categoriaSelect');

    grupoSelect.addEventListener('change', function() {
        const grupoId = this.value;

        if (!grupoId) {
            categoriaSelect.innerHTML = '<option value="">-- Selecione um grupo primeiro --</option>';
            return;
        }

        categoriaSelect.innerHTML = '<option>Carregando...</option>';

        fetch(`/dados-mestres/categorias/por-grupo/${grupoId}`)
            .then(r => r.json())
            .then(data => {
                categoriaSelect.innerHTML = '<option value="">-- Selecione --</option>';
                data.forEach(cat => {
                    const opt = document.createElement('option');
                    opt.value = cat.CategoriaId;
                    opt.textContent = cat.Nome;
                    categoriaSelect.appendChild(opt);
                });
            })
            .catch(() => {
                categoriaSelect.innerHTML = '<option value="">Erro ao carregar categorias</option>';
            });
    });
});
</script>
@endsection
