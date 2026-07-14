@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 max-w-4xl">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Editar Reavaliacao</h1>

    @if ($errors->any())
        <div class="mb-4 p-4 rounded-md bg-red-50 border border-red-200 text-red-700">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $bem = $reavaliacao->bem;
        $dataAquisicaoInicial = $reavaliacao->data_aquisicao
            ? \Carbon\Carbon::parse($reavaliacao->data_aquisicao)->format('Y-m-d')
            : optional($bem?->data_aquisicao ?? $bem?->created_at)->toDateString();
    @endphp

    <form action="{{ route('reavaliacoes.update', $reavaliacao->id) }}" method="POST" class="bg-white p-8 rounded-2xl shadow-lg space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Activo</label>
                <input type="text" value="{{ $bem->Nome ?? 'N/D' }}" readonly
                       class="w-full border rounded-lg px-3 py-2 bg-gray-100 text-gray-600">
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Etiqueta</label>
                <input type="text" value="{{ $bem->Etiqueta ?? '-' }}" readonly
                       class="w-full border rounded-lg px-3 py-2 bg-gray-100 text-gray-600">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
            <div class="bg-gray-100 p-4 rounded-lg shadow">
                <h2 class="font-semibold text-gray-700 mb-2">Valor Inicial (Kz)</h2>
                <p id="valor_inicial_card" class="text-xl font-bold text-gray-800">
                    {{ number_format(old('valor_inicial', $reavaliacao->valor_inicial ?? 0), 2, ',', '.') }} Kz
                </p>
            </div>
            <div class="bg-yellow-100 p-4 rounded-lg shadow">
                <h2 class="font-semibold text-gray-700 mb-2">VLC</h2>
                <p id="vlc_card" class="text-xl font-bold text-yellow-800">
                    {{ number_format(old('vlc', $reavaliacao->vlc ?? 0), 2, ',', '.') }} Kz
                </p>
            </div>
            <div class="bg-green-100 p-4 rounded-lg shadow">
                <h2 class="font-semibold text-gray-700 mb-2">Valor Atualizado</h2>
                <p id="valor_atualizado_card" class="text-xl font-bold text-green-800">
                    {{ number_format(old('valor_atualizado', $reavaliacao->valor_atualizado ?? 0), 2, ',', '.') }} Kz
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
            <div class="bg-blue-100 p-4 rounded-lg shadow">
                <h2 class="font-semibold text-gray-700 mb-2">Nova Depreciacao Anual</h2>
                <p id="nova_depreciacao_card" class="text-xl font-bold text-blue-800">
                    {{ number_format(old('nova_depreciacao_anual', $reavaliacao->nova_depreciacao_anual ?? 0), 2, ',', '.') }} Kz/ano
                </p>
            </div>
            <div class="bg-gray-200 p-4 rounded-lg shadow">
                <h2 class="font-semibold text-gray-700 mb-2">Data de Aquisicao</h2>
                <p id="data_aquisicao_card" class="text-xl font-bold text-gray-800">{{ $dataAquisicaoInicial ?? '--' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div>
                <label for="valor_inicial" class="block text-gray-700 font-semibold mb-2">Valor Inicial (Kz)</label>
                <input type="number" name="valor_inicial" id="valor_inicial" min="0" step="0.01" required
                       value="{{ old('valor_inicial', $reavaliacao->valor_inicial) }}"
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label for="taxa_depreciacao" class="block text-gray-700 font-semibold mb-2">Taxa Anual (%)</label>
                <input type="number" name="taxa_depreciacao" id="taxa_depreciacao" min="0" max="100" step="0.01"
                       value="{{ old('taxa_depreciacao', $reavaliacao->taxa_depreciacao) }}"
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label for="vida_util" class="block text-gray-700 font-semibold mb-2">Vida Util (anos)</label>
                <input type="number" name="vida_util" id="vida_util" min="1" step="1"
                       value="{{ old('vida_util', $reavaliacao->vida_util) }}"
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
            <div>
                <label for="valor_justo" class="block text-gray-700 font-semibold mb-2">Valor Justo (Kz)</label>
                <input type="number" id="valor_justo" min="0" step="0.01"
                       value="{{ old('valor_justo', $reavaliacao->valor_atualizado ?? $reavaliacao->valor_inicial ?? 0) }}"
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label for="valor_residual" class="block text-gray-700 font-semibold mb-2">Valor Residual (Kz)</label>
                <input type="number" name="valor_residual" id="valor_residual" min="0" step="0.01"
                       value="{{ old('valor_residual', $reavaliacao->valor_residual) }}"
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label for="data_reavaliacao" class="block text-gray-700 font-semibold mb-2">Data Reavaliacao</label>
                <input type="date" name="data_reavaliacao" id="data_reavaliacao" required
                       value="{{ old('data_reavaliacao', $reavaliacao->data_reavaliacao ? \Carbon\Carbon::parse($reavaliacao->data_reavaliacao)->format('Y-m-d') : now()->format('Y-m-d')) }}"
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
            </div>
        </div>

        <div>
            <label for="observacoes" class="block text-gray-700 font-semibold mb-2">Observacoes</label>
            <textarea name="observacoes" id="observacoes" rows="3"
                      class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">{{ old('observacoes', $reavaliacao->observacoes) }}</textarea>
        </div>

        <input type="hidden" name="data_aquisicao" id="data_aquisicao_hidden" value="{{ old('data_aquisicao', $dataAquisicaoInicial) }}">
        <input type="hidden" name="vlc" id="vlc_hidden" value="{{ old('vlc', $reavaliacao->vlc) }}">
        <input type="hidden" name="valor_atualizado" id="valor_atualizado_hidden" value="{{ old('valor_atualizado', $reavaliacao->valor_atualizado) }}">
        <input type="hidden" name="nova_depreciacao_anual" id="nova_depreciacao_anual_hidden" value="{{ old('nova_depreciacao_anual', $reavaliacao->nova_depreciacao_anual) }}">

        <div class="flex justify-between mt-6">
            <button type="submit"
                    class="px-6 py-2 bg-yellow-500 text-white font-semibold rounded-lg shadow hover:bg-yellow-600 transition">
                Atualizar
            </button>
            <a href="{{ route('reavaliacoes.index') }}"
               class="px-6 py-2 bg-gray-400 text-white rounded-lg shadow hover:bg-gray-500 transition">
                Cancelar
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const valorInicialInput = document.getElementById('valor_inicial');
    const taxaInput = document.getElementById('taxa_depreciacao');
    const vidaUtilInput = document.getElementById('vida_util');
    const valorJustoInput = document.getElementById('valor_justo');
    const valorResidualInput = document.getElementById('valor_residual');
    const dataReavaliacaoInput = document.getElementById('data_reavaliacao');

    const valorInicialCard = document.getElementById('valor_inicial_card');
    const vlcCard = document.getElementById('vlc_card');
    const valorAtualizadoCard = document.getElementById('valor_atualizado_card');
    const novaDepreciacaoCard = document.getElementById('nova_depreciacao_card');
    const dataAquisicaoCard = document.getElementById('data_aquisicao_card');

    const dataAquisicaoHidden = document.getElementById('data_aquisicao_hidden');
    const vlcHidden = document.getElementById('vlc_hidden');
    const valorAtualizadoHidden = document.getElementById('valor_atualizado_hidden');
    const novaDepreciacaoAnualHidden = document.getElementById('nova_depreciacao_anual_hidden');

    function fmt(num) {
        return new Intl.NumberFormat('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num) + ' Kz';
    }

    function recalcular() {
        const valorInicial = parseFloat(valorInicialInput.value) || 0;
        const taxa = parseFloat(taxaInput.value) || 0;
        const vidaUtil = parseInt(vidaUtilInput.value, 10) || 1;
        const valorJusto = parseFloat(valorJustoInput.value);
        const valorResidual = parseFloat(valorResidualInput.value) || 0;

        const dataAquisicaoRaw = dataAquisicaoHidden.value || dataAquisicaoCard.textContent;
        const dtAq = dataAquisicaoRaw ? new Date(dataAquisicaoRaw) : new Date();
        const dtReav = dataReavaliacaoInput.value ? new Date(dataReavaliacaoInput.value) : new Date();

        let anosUso = (dtReav - dtAq) / (1000 * 60 * 60 * 24 * 365);
        if (!isFinite(anosUso) || anosUso < 0) anosUso = 0;

        const depreciacaoAcumulada = valorInicial * (taxa / 100) * anosUso;
        const vlc = valorInicial - depreciacaoAcumulada;
        const vJusto = isFinite(valorJusto) ? valorJusto : valorInicial;
        const valorAtualizado = vlc + (vJusto - vlc);
        const novaDepreciacaoAnual = vidaUtil > 0 ? (valorAtualizado - valorResidual) / vidaUtil : 0;

        valorInicialCard.textContent = fmt(valorInicial);
        vlcCard.textContent = fmt(vlc);
        valorAtualizadoCard.textContent = fmt(valorAtualizado);
        novaDepreciacaoCard.textContent = fmt(novaDepreciacaoAnual).replace(' Kz', ' Kz/ano');
        dataAquisicaoCard.textContent = dtAq.toISOString().slice(0, 10);

        dataAquisicaoHidden.value = dtAq.toISOString().slice(0, 10);
        vlcHidden.value = vlc.toFixed(2);
        valorAtualizadoHidden.value = valorAtualizado.toFixed(2);
        novaDepreciacaoAnualHidden.value = novaDepreciacaoAnual.toFixed(2);
    }

    [valorInicialInput, taxaInput, vidaUtilInput, valorJustoInput, valorResidualInput, dataReavaliacaoInput].forEach((el) => {
        el.addEventListener('input', recalcular);
        el.addEventListener('change', recalcular);
    });

    recalcular();
});
</script>
@endsection
