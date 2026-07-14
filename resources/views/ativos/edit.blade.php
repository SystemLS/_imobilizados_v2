@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6">

    {{-- Topo do formulário --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Editar Ativo: {{ $bem->Nome }}</h1>
        <a href="{{ route('ativos.index') }}"
           id="cancelBtn"
           class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
           Cancelar
        </a>
    </div>

    <form action="{{ route('ativos.update', $bem->BemId) }}" method="POST" enctype="multipart/form-data"
          class="space-y-6 bg-white p-6 rounded-xl shadow-lg" id="editBemForm">
        @csrf
        @method('PUT')

        {{-- Localização --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block font-semibold">Província</label>
                <select name="ProvinciaId" id="provincia" class="w-full border rounded px-3 py-2" required>
                    <option value="">Selecione</option>
                    @foreach($provincias as $provincia)
                        <option value="{{ $provincia->ProvinciaId }}"
                            {{ $bem->provincia()->ProvinciaId == $provincia->ProvinciaId ? 'selected' : '' }}>
                            {{ $provincia->Nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold">Edifício</label>
                <select name="EdificioId" id="edificio" class="w-full border rounded px-3 py-2" required>
                    <option value="{{ $bem->sala->piso->edificio->EdificioId }}">
                        {{ $bem->sala->piso->edificio->Nome }}
                    </option>
                </select>
            </div>
            <div>
                <label class="block font-semibold">Piso</label>
                <select name="PisoId" id="piso" class="w-full border rounded px-3 py-2" required>
                    <option value="{{ $bem->sala->piso->PisoId }}">
                        {{ $bem->sala->piso->Nome }}
                    </option>
                </select>
            </div>
        </div>

        {{-- Sala --}}
        <div>
            <label class="block font-semibold">Sala</label>
            <select name="SalaId" id="sala" class="w-full border rounded px-3 py-2" required>
                <option value="{{ $bem->sala->SalaId }}">{{ $bem->sala->Nome }}</option>
            </select>
        </div>

        {{-- Grupo, Categoria, Subcategoria --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block font-semibold">Grupo</label>
                <select name="GrupoId" id="grupo" class="w-full border rounded px-3 py-2" required>
                    <option value="">Selecione</option>
                    @foreach($grupos as $grupo)
                        <option value="{{ $grupo->GrupoId }}"
                            {{ $bem->GrupoId == $grupo->GrupoId ? 'selected' : '' }}>
                            {{ $grupo->Nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold">Categoria</label>
                <select name="CategoriaId" id="categoria" class="w-full border rounded px-3 py-2" required>
                    <option value="{{ $bem->CategoriaId }}">{{ $bem->categoria->Nome ?? '' }}</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold">Subcategoria</label>
                <select name="SubcategoriaId" id="subcategoria" class="w-full border rounded px-3 py-2">
                    <option value="{{ $bem->SubcategoriaId }}">{{ $bem->subcategoria->Nome ?? '' }}</option>
                </select>
            </div>
        </div>

        {{-- Nome e Etiqueta --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold">Nome do Bem</label>
                <input type="text" name="Nome" class="w-full border rounded px-3 py-2"
                       value="{{ $bem->Nome }}" required>
            </div>
            <div class="flex items-center gap-2">
                <div class="flex-1">
                    <label class="block font-semibold">Etiqueta</label>
                    <input type="text" name="Etiqueta" id="etiqueta" class="w-full border rounded px-3 py-2"
                           placeholder="AA000001" pattern="AA[0-9]{6}"
                           value="{{ $bem->Etiqueta }}">
                </div>
                <button type="button" class="abrirCameraBtn px-4 py-2 bg-gray-200 rounded-lg mt-6 hover:bg-gray-300 transition"
                        data-target="etiqueta">📷</button>
            </div>
        </div>

        {{-- Marca e Modelo --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold">Marca</label>
                <input type="text" name="Marca" class="w-full border rounded px-3 py-2"
                       value="{{ $bem->Marca }}" required>
            </div>
            <div>
                <label class="block font-semibold">Modelo</label>
                <input type="text" name="Modelo" class="w-full border rounded px-3 py-2"
                       value="{{ $bem->Modelo }}" required>
            </div>
        </div>

        {{-- Tipo Número de Série --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
            <div>
                <label class="block font-semibold" style="width:180px;">Tipo Número de Série</label>
                <select name="TipoNumeroSerie" id="tipoNumeroSerie" class="w-full border rounded px-3 py-2">
                    <option value="NumeroSerieManual" {{ $bem->TipoNumeroSerie == 'NumeroSerieManual' ? 'selected' : '' }}>
                        Número de série manual
                    </option>
                    <option value="NumeroScanner" {{ $bem->TipoNumeroSerie == 'NumeroScanner' ? 'selected' : '' }}>
                        Número de série por scanner
                    </option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <div id="numeroSerieManualDiv" class="flex-1">
                    <label class="block font-semibold">Número de série manual</label>
                    <input type="text" name="NumeroSerieManual" class="w-full border rounded px-3 py-2"
                           value="{{ $bem->NumeroSerieManual }}">
                </div>
                <div id="numeroScannerDiv" class="flex items-center gap-2"
                     style="{{ $bem->TipoNumeroSerie == 'NumeroScanner' ? '' : 'display:none;' }}">
                    <div class="flex-1">
                        <label class="block font-semibold">Número de série por scanner</label>
                        <input type="text" name="NumeroScanner" id="numeroScanner" class="w-full border rounded px-3 py-2"
                               value="{{ $bem->NumeroScanner }}" readonly>
                    </div>
                    <button type="button" class="abrirCameraBtn px-4 py-2 bg-gray-200 rounded-lg mt-6 hover:bg-gray-300 transition"
                            data-target="numeroScanner">📷</button>
                </div>
            </div>
        </div>

        {{-- Capacidade, Potência, Data de Aquisição e Preço de Aquisição --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block font-semibold">Capacidade</label>
                <input type="text" name="Capacidade" class="w-full border rounded px-3 py-2"
                       value="{{ $bem->Capacidade }}" required>
            </div>
            <div id="potenciaDiv"
                 class="{{ in_array(strtoupper($bem->grupo->Nome), ['EQUIPAMENTOS','MOBILIARIO']) ? '' : 'hidden' }}">
                <label class="block font-semibold">Potência</label>
                <input type="text" name="Potencia" class="w-full border rounded px-3 py-2"
                       value="{{ $bem->Potencia }}">
            </div>
            <div>
                <label class="block font-semibold">Data de Aquisição</label>
                <input type="date" name="data_aquisicao" class="w-full border rounded px-3 py-2"
                    value="{{ \Carbon\Carbon::parse($bem->data_aquisicao)->format('Y-m-d') }}" required>
            </div>
            <div>
                <label class="block font-semibold">Preço de Aquisição (Kz)</label>
                <input type="number" name="preco_aquisicao" class="w-full border rounded px-3 py-2" step="0.01" min="0"
                       value="{{ $bem->preco_aquisicao }}" required>
            </div>
        </div>

        {{-- Materiais associados --}}
        <div>
            <label class="block font-semibold mb-2">Materiais Associados</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                @foreach($materiais as $material)
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="Materiais[]" value="{{ $material->MaterialId }}"
                            {{ in_array($material->MaterialId, $bem->materiais->pluck('MaterialId')->toArray()) ? 'checked' : '' }}>
                        <span class="ml-2">{{ $material->Nome }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Estado de Conservação e Condição Ambiental --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold">Estado de Conservação</label>
                <select name="EstadoConservacaoId" class="w-full border rounded px-3 py-2" required>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->EstadoConservacaoId }}"
                            {{ $bem->EstadoConservacaoId == $estado->EstadoConservacaoId ? 'selected' : '' }}>
                            {{ $estado->Nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold">Condição Ambiental</label>
                <select name="CondicaoAmbientalId" class="w-full border rounded px-3 py-2" required>
                    @foreach($condicoes as $condicao)
                        <option value="{{ $condicao->CondicaoAmbientalId }}"
                            {{ $bem->CondicaoAmbientalId == $condicao->CondicaoAmbientalId ? 'selected' : '' }}>
                            {{ $condicao->Nome }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Fotos --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach(['Foto1','Foto2','Foto3'] as $foto)
                <div>
                    <label class="block font-semibold">{{ $foto }}</label>
                    <input type="file" name="{{ $foto }}" class="w-full">
                    @if($bem->$foto)
                        <img src="{{ asset('storage/' . $bem->$foto) }}" alt="{{ $foto }}"
                             class="h-20 w-20 object-cover mt-2 rounded-lg">
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Descrição --}}
        <div>
            <label class="block font-semibold">Descrição Completa</label>
            <textarea name="Descricao" rows="4" class="w-full border rounded px-3 py-2" required>{{ $bem->Descricao }}</textarea>
        </div>

        {{-- Botão submit --}}
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Atualizar Ativo
            </button>
        </div>
    </form>
</div>

@include('ativos.partials.camera-modal')
@include('ativos.partials.scripts')

<script>
document.addEventListener('DOMContentLoaded', function(){

    // Função para carregar selects dependentes via AJAX
    function carregarDependentes(url, selectDestino, selectedId = null){
        fetch(url)
            .then(res => res.json())
            .then(data => {
                let html = '<option value="">Selecione</option>';
                data.forEach(item => {
                    html += `<option value="${item.id}" ${selectedId == item.id ? 'selected' : ''}>${item.nome}</option>`;
                });
                document.getElementById(selectDestino).innerHTML = html;
            });
    }

    // Eventos para dependências
    document.getElementById('provincia').addEventListener('change', function(){
        const provinciaId = this.value;
        if(provinciaId) carregarDependentes(`/provincias/${provinciaId}/edificios`, 'edificio');
    });

    document.getElementById('edificio').addEventListener('change', function(){
        const edificioId = this.value;
        if(edificioId) carregarDependentes(`/edificios/${edificioId}/pisos`, 'piso');
    });

    document.getElementById('piso').addEventListener('change', function(){
        const pisoId = this.value;
        if(pisoId) carregarDependentes(`/pisos/${pisoId}/salas`, 'sala');
    });

    document.getElementById('grupo').addEventListener('change', function(){
        const grupoId = this.value;
        const selectedGrupo = this.options[this.selectedIndex].text.toUpperCase();
        const potenciaDiv = document.getElementById('potenciaDiv');

        if(['EQUIPAMENTOS','MOBILIARIO'].includes(selectedGrupo)){
            potenciaDiv.classList.remove('hidden');
            potenciaDiv.querySelector('input').required = true;
        } else {
            potenciaDiv.classList.add('hidden');
            potenciaDiv.querySelector('input').required = false;
        }

        if(grupoId) carregarDependentes(`/grupos/${grupoId}/categorias`, 'categoria');
    });

    document.getElementById('categoria').addEventListener('change', function(){
        const categoriaId = this.value;
        if(categoriaId) carregarDependentes(`/categorias/${categoriaId}/subcategorias`, 'subcategoria');
    });

    // Alternar tipo de número de série
    const tipoNumSerie = document.getElementById('tipoNumeroSerie');
    const manualDiv = document.getElementById('numeroSerieManualDiv');
    const scannerDiv = document.getElementById('numeroScannerDiv');

    function toggleNumeroSerie(){
        if(tipoNumSerie.value === 'NumeroScanner'){
            manualDiv.style.display = 'none';
            scannerDiv.style.display = 'flex';
        } else {
            manualDiv.style.display = 'flex';
            scannerDiv.style.display = 'none';
        }
    }
    toggleNumeroSerie();
    tipoNumSerie.addEventListener('change', toggleNumeroSerie);

    // Confirmação ao cancelar
    const form = document.getElementById('editBemForm');
    const cancelBtn = document.getElementById('cancelBtn');
    cancelBtn.addEventListener('click', function(e){
        e.preventDefault();
        let formChanged = [...form.elements].some(el => {
            if(el.type === 'checkbox') return el.checked !== el.defaultChecked;
            if(el.type === 'file') return el.files.length > 0;
            return el.value !== el.defaultValue;
        });
        if(formChanged){
            if(confirm("Deseja cancelar a alteração?")){
                window.location.href = cancelBtn.href;
            }
        } else {
            window.location.href = cancelBtn.href;
        }
    });

});
</script>

@endsection
