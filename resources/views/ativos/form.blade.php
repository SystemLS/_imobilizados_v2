{{-- Partial usado em create e edit --}}
{{-- Variável $bem opcional, usada para edição --}}
@php
    $bem = $bem ?? null;
@endphp

{{-- Localização --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
        <label class="block font-semibold">Província</label>
        <select name="ProvinciaId" class="w-full border rounded px-3 py-2" required>
            <option value="">Selecione</option>
            @foreach($provincias as $prov)
                <option value="{{ $prov->id }}" @if($bem && $bem->sala?->piso?->edificio?->provincia_id == $prov->id) selected @endif>{{ $prov->Nome }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block font-semibold">Edifício</label>
        <select name="EdificioId" class="w-full border rounded px-3 py-2" required>
            <option value="">Selecione</option>
            @foreach($edificios as $ed)
                <option value="{{ $ed->id }}" @if($bem && $bem->sala?->piso?->edificio_id == $ed->id) selected @endif>{{ $ed->Nome }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block font-semibold">Piso</label>
        <select name="PisoId" class="w-full border rounded px-3 py-2" required>
            <option value="">Selecione</option>
            @foreach($pisos as $piso)
                <option value="{{ $piso->id }}" @if($bem && $bem->sala?->piso_id == $piso->id) selected @endif>{{ $piso->Nome }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mt-4">
    <label class="block font-semibold">Sala</label>
    <input type="text" name="SalaId" maxlength="8" placeholder="Ex: SALA0001"
           class="w-full border rounded px-3 py-2"
           pattern="SALA[0-9]{4}" title="Formato SALA0001"
           value="{{ $bem?->SalaId ?? '' }}" required>
    <small class="text-gray-500">Digite manualmente ou use o QR Code para preencher</small>
</div>

{{-- Grupo, Categoria, Subcategoria --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
    <div>
        <label class="block font-semibold">Grupo</label>
        <select name="GrupoId" class="w-full border rounded px-3 py-2" required>
            <option value="">Selecione</option>
            @foreach($grupos as $grupo)
                <option value="{{ $grupo->id }}" @if($bem && $bem->subcategoria?->categoria?->grupo_id == $grupo->id) selected @endif>{{ $grupo->Nome }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block font-semibold">Categoria</label>
        <select name="CategoriaId" class="w-full border rounded px-3 py-2" required>
            <option value="">Selecione</option>
            @foreach($categorias as $cat)
                <option value="{{ $cat->id }}" @if($bem && $bem->subcategoria?->categoria_id == $cat->id) selected @endif>{{ $cat->Nome }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block font-semibold">Subcategoria</label>
        <select name="SubcategoriaId" class="w-full border rounded px-3 py-2">
            <option value="">Selecione</option>
            @foreach($subcategorias as $sub)
                <option value="{{ $sub->id }}" @if($bem && $bem->SubcategoriaId == $sub->id) selected @endif>{{ $sub->Nome }}</option>
            @endforeach
        </select>
    </div>
</div>

{{-- Informações do ativo --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
    <div>
        <label class="block font-semibold">Nome do Bem</label>
        <input type="text" name="Nome" class="w-full border rounded px-3 py-2" value="{{ $bem?->Nome ?? '' }}" required>
    </div>
    <div>
        <label class="block font-semibold">Etiqueta</label>
        <input type="text" name="Etiqueta" class="w-full border rounded px-3 py-2" pattern="AA[0-9]{6}" placeholder="AA000001"
               value="{{ $bem?->Etiqueta ?? '' }}">
        <small class="text-gray-500">Use QR Code ou digite manualmente</small>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
    <div>
        <label class="block font-semibold">Marca</label>
        <input type="text" name="Marca" class="w-full border rounded px-3 py-2" value="{{ $bem?->Marca ?? '' }}">
    </div>
    <div>
        <label class="block font-semibold">Modelo</label>
        <input type="text" name="Modelo" class="w-full border rounded px-3 py-2" value="{{ $bem?->Modelo ?? '' }}">
    </div>
</div>

{{-- Tipo de Número de Série --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
    <div>
        <label class="block font-semibold">Tipo Número de Série</label>
        <select name="TipoNumeroSerie" id="tipoNumeroSerie" class="w-full border rounded px-3 py-2">
            <option value="NumeroSerieManual" @if(($bem?->TipoNumeroSerie ?? '') == 'NumeroSerieManual') selected @endif>Número Manual</option>
            <option value="NumeroScanner" @if(($bem?->TipoNumeroSerie ?? '') == 'NumeroScanner') selected @endif>Número Scanner</option>
        </select>
    </div>
    <div id="numeroSerieManualDiv">
        <label class="block font-semibold">Número Manual</label>
        <input type="text" name="NumeroSerieManual" class="w-full border rounded px-3 py-2" value="{{ $bem?->NumeroSerieManual ?? '' }}">
    </div>
    <div id="numeroScannerDiv" style="display:none;">
        <label class="block font-semibold">Número Scanner</label>
        <input type="text" name="NumeroScanner" class="w-full border rounded px-3 py-2" value="{{ $bem?->NumeroScanner ?? '' }}">
    </div>
</div>

{{-- Capacidade e Potência --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
    <div>
        <label class="block font-semibold">Capacidade</label>
        <input type="text" name="Capacidade" class="w-full border rounded px-3 py-2" value="{{ $bem?->Capacidade ?? '' }}">
    </div>
    <div>
        <label class="block font-semibold">Potência</label>
        <input type="text" name="Potencia" class="w-full border rounded px-3 py-2" value="{{ $bem?->Potencia ?? '' }}">
    </div>
</div>

{{-- Estado e Condição --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
    <div>
        <label class="block font-semibold">Estado de Conservação</label>
        <select name="EstadoConservacaoId" class="w-full border rounded px-3 py-2">
            <option value="">Selecione</option>
            @foreach($estados as $estado)
                <option value="{{ $estado->id }}" @if($bem?->EstadoConservacaoId == $estado->id) selected @endif>{{ $estado->Nome }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block font-semibold">Condição Ambiental</label>
        <select name="CondicaoAmbientalId" class="w-full border rounded px-3 py-2">
            <option value="">Selecione</option>
            @foreach($condicoes as $cond)
                <option value="{{ $cond->id }}" @if($bem?->CondicaoAmbientalId == $cond->id) selected @endif>{{ $cond->Nome }}</option>
            @endforeach
        </select>
    </div>
</div>

{{-- Preço de aquisição --}}
<div class="mt-4">
    <label class="block font-semibold">Preço de Aquisição (Kz)</label>
    <input type="number" name="preco_aquisicao" step="0.01" min="0" class="w-full border rounded px-3 py-2" value="{{ $bem?->preco_aquisicao ?? '' }}">
</div>

{{-- Fotos --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
    @foreach(['Foto1','Foto2','Foto3'] as $foto)
        <div>
            <label class="block font-semibold">{{ $foto }}</label>
            <input type="file" name="{{ $foto }}" class="w-full">
            @if($bem?->$foto)
                <img src="{{ asset('storage/'.$bem->$foto) }}" class="mt-2 h-24 w-24 object-cover rounded-lg shadow-sm">
            @endif
        </div>
    @endforeach
</div>

{{-- Descrição --}}
<div class="mt-4">
    <label class="block font-semibold">Descrição Completa</label>
    <textarea name="Descricao" rows="4" class="w-full border rounded px-3 py-2">{{ $bem?->Descricao ?? '' }}</textarea>
</div>

{{-- Scripts --}}
<script>
    // Alterna campos de número de série
    const tipoSelect = document.getElementById('tipoNumeroSerie');
    const manualDiv = document.getElementById('numeroSerieManualDiv');
    const scannerDiv = document.getElementById('numeroScannerDiv');

    function toggleSerie() {
        if(tipoSelect.value === 'NumeroSerieManual'){
            manualDiv.style.display = 'block';
            scannerDiv.style.display = 'none';
        } else {
            manualDiv.style.display = 'none';
            scannerDiv.style.display = 'block';
        }
    }

    tipoSelect.addEventListener('change', toggleSerie);
    window.addEventListener('load', toggleSerie);
</script>
