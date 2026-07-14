@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 px-4">

    {{-- Mensagens --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                @php $uniqueErrors = []; @endphp
                @foreach ($errors->all() as $error)
                    @if (!in_array($error, $uniqueErrors))
                        <li>{{ $error }}</li>
                        @php $uniqueErrors[] = $error; @endphp
                    @endif
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Cadastrar Novo Ativo</h1>
        <a href="{{ route('ativos.index') }}"
           class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">
           Voltar
        </a>
    </div>

    {{-- Formulário --}}
    <form action="{{ route('ativos.store') }}" method="POST" enctype="multipart/form-data"
          class="space-y-6 bg-white p-6 rounded-xl shadow-lg" id="formAtivo">
        @csrf

        {{-- Localização --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block font-semibold mb-1">Província</label>
                <select name="ProvinciaId" id="provincia" class="w-full border rounded px-3 py-2" required>
                    <option value="">Selecione</option>
                    @foreach($provincias as $provincia)
                        <option value="{{ $provincia->ProvinciaId }}" {{ old('ProvinciaId') == $provincia->ProvinciaId ? 'selected' : '' }}>
                            {{ $provincia->Nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold mb-1">Edifício</label>
                <select name="EdificioId" id="edificio" class="w-full border rounded px-3 py-2" required>
                    <option value="">Selecione</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold mb-1">Piso</label>
                <select name="PisoId" id="piso" class="w-full border rounded px-3 py-2" required>
                    <option value="">Selecione</option>
                </select>
            </div>
        </div>

        {{-- Sala --}}
        <div>
            <label class="block font-semibold mb-1">Sala</label>
            <select name="SalaId" id="sala" class="w-full border rounded px-3 py-2" required>
                <option value="">Selecione um piso primeiro</option>
            </select>
        </div>

        {{-- Grupo, Categoria, Subcategoria --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block font-semibold mb-1">Grupo</label>
                <select name="GrupoId" id="grupo" class="w-full border rounded px-3 py-2" required>
                    <option value="">Selecione</option>
                    @foreach($grupos as $grupo)
                        <option value="{{ $grupo->GrupoId }}" {{ old('GrupoId') == $grupo->GrupoId ? 'selected' : '' }}>
                            {{ $grupo->Nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold mb-1">Categoria</label>
                <select name="CategoriaId" id="categoria" class="w-full border rounded px-3 py-2" required>
                    <option value="">Selecione</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold mb-1">Subcategoria</label>
                <select name="SubcategoriaId" id="subcategoria" class="w-full border rounded px-3 py-2">
                    <option value="">Selecione</option>
                </select>
            </div>
        </div>

        {{-- Nome e Etiqueta --}}
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label class="block font-semibold mb-1">Nome do Bem</label>
                <input type="text" name="Nome" value="{{ old('Nome') }}" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="flex-1 flex gap-2">
                <div class="flex-1">
                    <label class="block font-semibold mb-1">Etiqueta</label>
                    <input type="text" name="Etiqueta" id="etiqueta" value="{{ old('Etiqueta') }}" class="w-full border rounded px-3 py-2" placeholder="AA000001" pattern="AA[0-9]{6}">
                </div>
                <button type="button" class="abrirCameraBtn px-4 py-2 bg-gray-200 rounded-lg mt-auto hover:bg-gray-300 transition" data-target="etiqueta">📷</button>
            </div>
        </div>

        {{-- Marca e Modelo --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold mb-1">Marca</label>
                <input type="text" name="Marca" value="{{ old('Marca') }}" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block font-semibold mb-1">Modelo</label>
                <input type="text" name="Modelo" value="{{ old('Modelo') }}" class="w-full border rounded px-3 py-2" required>
            </div>
        </div>

        {{-- Número de Série --}}
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label class="block font-semibold mb-1">Tipo Número de Série</label>
                <select name="TipoNumeroSerie" id="tipoNumeroSerie" class="w-full border rounded px-3 py-2">
                    <option value="NumeroSerieManual" {{ old('TipoNumeroSerie') == 'NumeroSerieManual' ? 'selected' : '' }}>Número de série manual</option>
                    <option value="NumeroScanner" {{ old('TipoNumeroSerie') == 'NumeroScanner' ? 'selected' : '' }}>Número de série por scanner</option>
                </select>
            </div>
            <div class="flex-1 flex gap-2">
                <div id="numeroSerieManualDiv" class="flex-1">
                    <label class="block font-semibold mb-1">Número de série manual</label>
                    <input type="text" name="NumeroSerieManual" value="{{ old('NumeroSerieManual') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div id="numeroScannerDiv" class="flex-1 flex items-center gap-2" style="display:none;">
                    <label class="block font-semibold mb-1">Número de série por scanner</label>
                    <input type="text" name="NumeroScanner" id="numeroScanner" value="{{ old('NumeroScanner') }}" class="w-full border rounded px-3 py-2" readonly>
                    <button type="button" class="abrirCameraBtn px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition" data-target="numeroScanner">📷</button>
                </div>
            </div>
        </div>

        {{-- Capacidade e Potência --}}
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label class="block font-semibold mb-1">Capacidade</label>
                <input type="text" name="Capacidade" value="{{ old('Capacidade') }}" class="w-full border rounded px-3 py-2" required>
            </div>
            <div id="potenciaDiv" class="flex-1 {{ old('Potencia') ? '' : 'hidden' }}">
                <label class="block font-semibold mb-1">Potência</label>
                <input type="text" name="Potencia" value="{{ old('Potencia') }}" class="w-full border rounded px-3 py-2">
            </div>
        </div>

        {{-- Preço e Data --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold mb-1">Preço de Aquisição (Kz)</label>
                <input type="number" name="preco_aquisicao" value="{{ old('preco_aquisicao') }}" class="w-full border rounded px-3 py-2" step="0.01" min="0" required>
            </div>
            <div>
                <label class="block font-semibold mb-1">Data de Aquisição</label>
                <input type="date" name="data_aquisicao" value="{{ old('data_aquisicao') }}" class="w-full border rounded px-3 py-2" required>
            </div>
        </div>

        {{-- Materiais --}}
        <div>
            <label class="block font-semibold mb-2">Materiais Associados</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                @foreach($materiais as $material)
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="Materiais[]" value="{{ $material->MaterialId }}" {{ is_array(old('Materiais')) && in_array($material->MaterialId, old('Materiais')) ? 'checked' : '' }}>
                        <span class="ml-2">{{ $material->Nome }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Estado e Condição --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold mb-1">Estado de Conservação</label>
                <select name="EstadoConservacaoId" class="w-full border rounded px-3 py-2" required>
                    <option value="">Selecione</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->EstadoConservacaoId }}" {{ old('EstadoConservacaoId') == $estado->EstadoConservacaoId ? 'selected' : '' }}>
                            {{ $estado->Nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold mb-1">Condição Ambiental</label>
                <select name="CondicaoAmbientalId" class="w-full border rounded px-3 py-2" required>
                    <option value="">Selecione</option>
                    @foreach($condicoes as $condicao)
                        <option value="{{ $condicao->CondicaoAmbientalId }}" {{ old('CondicaoAmbientalId') == $condicao->CondicaoAmbientalId ? 'selected' : '' }}>
                            {{ $condicao->Nome }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Fotos --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block font-semibold mb-1">Foto 1</label>
                <input type="file" name="Foto1" class="w-full" required>
            </div>
            <div>
                <label class="block font-semibold mb-1">Foto 2</label>
                <input type="file" name="Foto2" class="w-full" required>
            </div>
            <div>
                <label class="block font-semibold mb-1">Foto 3</label>
                <input type="file" name="Foto3" class="w-full">
            </div>
        </div>

        {{-- Descrição --}}
        <div>
            <label class="block font-semibold mb-1">Descrição Completa</label>
            <textarea name="Descricao" rows="4" class="w-full border rounded px-3 py-2" required>{{ old('Descricao') }}</textarea>
        </div>

        {{-- Botões --}}
        <div class="flex flex-col sm:flex-row justify-between gap-4">
            <a href="{{ route('ativos.index') }}" id="cancelarBtn"
               class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-center">
               Cancelar
            </a>

            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Cadastrar Ativo
            </button>
        </div>
    </form>
</div>

@include('ativos.partials.camera-modal')
@include('ativos.partials.scripts')
@endsection
