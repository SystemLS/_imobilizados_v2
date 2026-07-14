@extends('layouts.app')

@section('page-title', 'Editar Manutenção')

@section('content')
<div class="max-w-3xl mx-auto p-4 sm:p-6 bg-white rounded-lg shadow-md">

    <h1 class="text-2xl sm:text-3xl font-bold mb-6 text-center text-blue-800">
        Editar Manutenção
    </h1>

    {{-- Mensagens de erro globais --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('manutencoes.update', $manutencao->id) }}" method="POST" id="formManutencao" class="space-y-4">
        @csrf
        @method('PUT')

        {{-- Bem (não editável) --}}
        <div>
            <label class="block font-medium mb-1">Bem</label>

            <select class="w-full border rounded-lg p-2 sm:p-3 bg-gray-100" disabled>
                @foreach($bens as $bem)
                    @if($manutencao->bem_id == $bem->BemId)
                        <option selected>{{ $bem->Nome }}</option>
                    @endif
                @endforeach
            </select>

            <input type="hidden" name="bem_id" value="{{ $manutencao->bem_id }}">
        </div>

        {{-- Etiqueta --}}
        <div>
            <label class="block font-medium mb-1">Etiqueta</label>
            <input type="text"
                   class="w-full border rounded-lg p-2 sm:p-3 bg-gray-100"
                   readonly
                   value="{{ $manutencao->bem->Etiqueta ?? '' }}">
        </div>

        {{-- Tipo --}}
        <div>
            <label class="block font-medium mb-1">Tipo</label>
            <select name="tipo" class="w-full border rounded-lg p-2 sm:p-3" required>
                <option value="Preventiva" {{ $manutencao->tipo == 'Preventiva' ? 'selected' : '' }}>
                    Preventiva
                </option>
                <option value="Corretiva" {{ $manutencao->tipo == 'Corretiva' ? 'selected' : '' }}>
                    Corretiva
                </option>
            </select>
        </div>

        {{-- Descrição --}}
        <div>
            <label class="block font-medium mb-1">Descrição</label>
            <textarea name="descricao" rows="4" class="w-full border rounded-lg p-2 sm:p-3" required>{{ old('descricao', $manutencao->descricao) }}</textarea>
        </div>

        {{-- Datas --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium mb-1">Data da Manutenção</label>
                <input type="date"
                       name="data_manutencao"
                       id="data_manutencao"
                       class="w-full border rounded-lg p-2 sm:p-3"
                       value="{{ \Carbon\Carbon::parse($manutencao->data_manutencao)->format('Y-m-d') }}"
                       required>
            </div>

            <div>
                <label class="block font-medium mb-1">Data da Conclusão</label>
                <input type="date"
                       name="DataConclusao"
                       id="DataConclusao"
                       class="w-full border rounded-lg p-2 sm:p-3"
                       value="{{ optional($manutencao->DataConclusao)->format('Y-m-d') }}">
            </div>
        </div>

        {{-- Status e Responsável --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium mb-1">Status</label>
                <select name="status" id="status" class="w-full border rounded-lg p-2 sm:p-3" required>
                    @foreach(['Pendente','Em Andamento','Concluída','Cancelada'] as $status)
                        <option value="{{ $status }}" {{ $manutencao->status == $status ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium mb-1">Responsável</label>
                <select name="responsavel" class="w-full border rounded-lg p-2 sm:p-3" required>
                    <option value="">-- Selecione o responsável --</option>
                    @foreach($tecnicos as $tec)
                        <option value="{{ $tec->name }}" {{ $manutencao->responsavel == $tec->name ? 'selected' : '' }}>
                            {{ $tec->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Botões --}}
        <div class="flex flex-col sm:flex-row sm:justify-between gap-3 pt-4">
            <a href="{{ route('manutencoes.index') }}"
               onclick="return confirm('Deseja realmente cancelar esta operação?');"
               class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-center">
                Cancelar
            </a>

            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                Atualizar
            </button>
        </div>
    </form>
</div>

{{-- Validações JS --}}
<script>
document.getElementById('formManutencao').addEventListener('submit', function (e) {

    const dataManutencao = document.getElementById('data_manutencao').value;
    const dataConclusao  = document.getElementById('DataConclusao').value;
    const status         = document.getElementById('status').value;

    if (dataConclusao && dataConclusao <= dataManutencao) {
        e.preventDefault();
        alert('❌ A data de conclusão deve ser posterior à data da manutenção.');
        return false;
    }

    if (status === 'Concluída' && !dataConclusao) {
        e.preventDefault();
        alert('⚠️ Para manutenções concluídas, é obrigatório informar a data de conclusão.');
        return false;
    }
});
</script>

<style>
form {
    background: linear-gradient(145deg, #ffffff, #f4f6fa);
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

input, select, textarea {
    font-size: 0.95rem;
}

@media (max-width: 640px) {
    h1 { font-size: 1.5rem; }
    .grid { grid-template-columns: 1fr !important; }
}
</style>
@endsection
