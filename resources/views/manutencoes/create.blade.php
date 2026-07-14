@extends('layouts.app')

@section('page-title', 'Nova Manutenção')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md sm:p-8 md:p-10">
    <h1 class="text-2xl font-bold mb-6 text-center sm:text-left">Nova Manutenção</h1>

    <form action="{{ route('manutencoes.store') }}" method="POST" class="space-y-5">
        @csrf

        {{-- Seleção do Bem --}}
        <div>
            <label for="bem_id" class="block font-medium mb-1">Bem</label>
            <select name="bem_id" id="bem_id" class="w-full border rounded-lg p-3 sm:p-2" required>
                <option value="">-- Selecione o bem --</option>
                @foreach($bens as $bem)
                    <option value="{{ $bem->BemId }}" data-etiqueta="{{ $bem->Etiqueta }}" {{ old('bem_id') == $bem->BemId ? 'selected' : '' }}>
                        {{ $bem->Nome }}
                    </option>
                @endforeach
            </select>
            @error('bem_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Etiqueta --}}
        <div>
            <label for="etiqueta" class="block font-medium mb-1">Etiqueta</label>
            <input type="text" id="etiqueta" readonly class="w-full border rounded-lg p-3 sm:p-2" value="{{ old('etiqueta') }}">
        </div>

        {{-- Tipo --}}
        <div>
            <label for="tipo" class="block font-medium mb-1">Tipo</label>
            <select name="tipo" id="tipo" class="w-full border rounded-lg p-3 sm:p-2" required>
                <option value="Preventiva" {{ old('tipo') == 'Preventiva' ? 'selected' : '' }}>Preventiva</option>
                <option value="Corretiva" {{ old('tipo') == 'Corretiva' ? 'selected' : '' }}>Corretiva</option>
            </select>
            @error('tipo')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Descrição --}}
        <div>
            <label for="descricao" class="block font-medium mb-1">Descrição</label>
            <textarea name="descricao" id="descricao" rows="4" class="w-full border rounded-lg p-3 sm:p-2" required>{{ old('descricao') }}</textarea>
            @error('descricao')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Datas --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="data_manutencao" class="block font-medium mb-1">Data da Manutenção</label>
                <input type="date" name="data_manutencao" id="data_manutencao" value="{{ old('data_manutencao', date('Y-m-d')) }}" class="w-full border rounded-lg p-3 sm:p-2" required>
                @error('data_manutencao')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="DataConclusao" class="block font-medium mb-1">Data da Conclusão</label>
                <input type="date" name="DataConclusao" id="DataConclusao" value="{{ old('DataConclusao') }}" class="w-full border rounded-lg p-3 sm:p-2" required>
                @error('DataConclusao')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Status e Responsável --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="status" class="block font-medium mb-1">Status</label>
                <select name="status" id="status" class="w-full border rounded-lg p-3 sm:p-2" required>
                    @php
                        $statusOptions = ['Pendente', 'Em Andamento', 'Concluída', 'Cancelada'];
                    @endphp
                    @foreach($statusOptions as $status)
                        <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="responsavel" class="block font-medium mb-1">Responsável</label>
                <select name="responsavel" id="responsavel" class="w-full border rounded-lg p-3 sm:p-2" required>
                    <option value="">-- Selecione o responsável --</option>
                    @foreach($tecnicos as $tec)
                        <option value="{{ $tec->name }}" {{ old('responsavel') == $tec->name ? 'selected' : '' }}>
                            {{ $tec->name }}
                        </option>
                    @endforeach
                </select>
                @error('responsavel')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Botões --}}
        <div class="flex flex-col sm:flex-row sm:justify-between gap-3 mt-6">
            <a href="{{ route('manutencoes.index') }}"
               onclick="return confirm('Deseja realmente cancelar esta operação?');"
               class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 text-center transition">
                Cancelar
            </a>

            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                Salvar
            </button>
        </div>
    </form>
</div>

<script>
    const bemSelect = document.getElementById('bem_id');
    const etiquetaInput = document.getElementById('etiqueta');

    function updateEtiqueta() {
        const selectedOption = bemSelect.options[bemSelect.selectedIndex];
        etiquetaInput.value = selectedOption.dataset.etiqueta || '';
    }

    bemSelect.addEventListener('change', updateEtiqueta);
    window.addEventListener('DOMContentLoaded', updateEtiqueta);
</script>
@endsection
