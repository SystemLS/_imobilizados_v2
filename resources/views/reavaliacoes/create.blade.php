@extends('layouts.app')

@section('content')
<div class="container mx-auto py-10 max-w-4xl">
    <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Nova Reavaliacao de Activo</h1>

        @if ($errors->any())
            <div class="mb-4 p-4 rounded-md bg-red-50 border border-red-200 text-red-700">
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('reavaliacoes.store') }}" method="POST" id="formReavaliacao" class="space-y-6">
            @csrf

            <div>
                <label for="bem_id" class="block text-gray-700 font-semibold mb-2">Activo</label>
                <select name="bem_id" id="bem_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
                    <option value="">Selecione um activo</option>
                    @foreach($bens as $bem)
                        <option value="{{ $bem->BemId }}"
                                data-preco="{{ $bem->preco_aquisicao ?? 0 }}"
                                data-data="{{ optional($bem->data_aquisicao ?? $bem->created_at)->toDateString() }}"
                                {{ old('bem_id') == $bem->BemId ? 'selected' : '' }}>
                            {{ $bem->Nome }} - (Etiqueta: {{ $bem->Etiqueta }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div class="bg-gray-100 p-4 rounded-lg shadow">
                    <h2 class="font-semibold text-gray-700 mb-2">Valor Inicial (Kz)</h2>
                    <p id="preco_aquisicao_card" class="text-xl font-bold text-gray-800">0,00 Kz</p>
                </div>

                <div class="bg-yellow-100 p-4 rounded-lg shadow">
                    <h2 class="font-semibold text-gray-700 mb-2">VLC</h2>
                    <p id="vlc_card" class="text-xl font-bold text-yellow-800">0,00 Kz</p>
                </div>

                <div class="bg-green-100 p-4 rounded-lg shadow">
                    <h2 class="font-semibold text-gray-700 mb-2">Valor Atualizado</h2>
                    <p id="valor_atualizado_card" class="text-xl font-bold text-green-800">0,00 Kz</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                <div class="bg-blue-100 p-4 rounded-lg shadow">
                    <h2 class="font-semibold text-gray-700 mb-2">Nova Depreciacao Anual</h2>
                    <p id="nova_depreciacao_card" class="text-xl font-bold text-blue-800">0,00 Kz/ano</p>
                </div>

                <div class="bg-gray-200 p-4 rounded-lg shadow">
                    <h2 class="font-semibold text-gray-700 mb-2">Data de Aquisicao</h2>
                    <p id="data_aquisicao_card" class="text-xl font-bold text-gray-800">--</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div>
                    <label for="taxa_depreciacao" class="block text-gray-700 font-semibold mb-2">Taxa Anual (%)</label>
                    <input type="number" name="taxa_depreciacao" id="taxa_depreciacao" min="0" max="100" step="0.01"
                           value="{{ old('taxa_depreciacao') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>

                <div>
                    <label for="vida_util" class="block text-gray-700 font-semibold mb-2">Vida Util (anos)</label>
                    <input type="number" name="vida_util" id="vida_util" min="1" step="1"
                           value="{{ old('vida_util') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>

                <div>
                    <label for="valor_justo" class="block text-gray-700 font-semibold mb-2">Valor Justo (Kz)</label>
                    <input type="number" name="valor_justo" id="valor_justo" step="0.01" min="0" required
                           value="{{ old('valor_justo') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                <div>
                    <label for="valor_residual" class="block text-gray-700 font-semibold mb-2">Valor Residual (Kz)</label>
                    <input type="number" name="valor_residual" id="valor_residual" step="0.01" min="0"
                           value="{{ old('valor_residual') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>

                <div>
                    <label for="data_reavaliacao" class="block text-gray-700 font-semibold mb-2">Data Reavaliacao</label>
                    <input type="date" name="data_reavaliacao" id="data_reavaliacao" required readonly tabindex="-1"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100 cursor-not-allowed">
                </div>
            </div>

            <div class="mt-4">
                <label for="observacoes" class="block text-gray-700 font-semibold mb-2">Observacoes</label>
                <textarea name="observacoes" id="observacoes" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2"
                          placeholder="Insira observacoes sobre a reavaliacao...">{{ old('observacoes') }}</textarea>
            </div>

            <input type="hidden" name="valor_inicial" id="valor_inicial_hidden" value="{{ old('valor_inicial') }}">
            <input type="hidden" name="data_aquisicao" id="data_aquisicao_hidden" value="{{ old('data_aquisicao') }}">
            <input type="hidden" name="vlc" id="vlc_hidden" value="{{ old('vlc') }}">
            <input type="hidden" name="valor_atualizado" id="valor_atualizado_hidden" value="{{ old('valor_atualizado') }}">
            <input type="hidden" name="nova_depreciacao_anual" id="nova_depreciacao_anual_hidden" value="{{ old('nova_depreciacao_anual') }}">

            <div class="flex justify-between items-center pt-6">
                <a href="{{ route('reavaliacoes.index') }}" class="px-6 py-2 bg-gray-500 text-white rounded-lg">Voltar</a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const bemSelect = document.getElementById('bem_id');
    const precoCard = document.getElementById('preco_aquisicao_card');
    const vlcCard = document.getElementById('vlc_card');
    const valorAtualizadoCard = document.getElementById('valor_atualizado_card');
    const novaDepreciacaoCard = document.getElementById('nova_depreciacao_card');
    const dataAquisicaoCard = document.getElementById('data_aquisicao_card');

    const valorInicialHidden = document.getElementById('valor_inicial_hidden');
    const dataAquisicaoHidden = document.getElementById('data_aquisicao_hidden');
    const vlcHidden = document.getElementById('vlc_hidden');
    const valorAtualizadoHidden = document.getElementById('valor_atualizado_hidden');
    const novaDepreciacaoAnualHidden = document.getElementById('nova_depreciacao_anual_hidden');

    const taxaInput = document.getElementById('taxa_depreciacao');
    const vidaUtilInput = document.getElementById('vida_util');
    const valorJustoInput = document.getElementById('valor_justo');
    const valorResidualInput = document.getElementById('valor_residual');
    const dataReavaliacaoInput = document.getElementById('data_reavaliacao');

    function dataHojeISO() {
        const agora = new Date();
        const ano = agora.getFullYear();
        const mes = String(agora.getMonth() + 1).padStart(2, '0');
        const dia = String(agora.getDate()).padStart(2, '0');
        return `${ano}-${mes}-${dia}`;
    }

    function definirDataReavaliacaoAtual() {
        const hoje = dataHojeISO();
        dataReavaliacaoInput.value = hoje;
        dataReavaliacaoInput.min = hoje;
        dataReavaliacaoInput.max = hoje;
    }

    function fmt(num) {
        return new Intl.NumberFormat('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num) + ' Kz';
    }

    function calcularReavaliacao() {
        const selected = bemSelect.selectedOptions[0];
        if (!selected || !selected.value) {
            precoCard.textContent = '0,00 Kz';
            vlcCard.textContent = '0,00 Kz';
            valorAtualizadoCard.textContent = '0,00 Kz';
            novaDepreciacaoCard.textContent = '0,00 Kz/ano';
            dataAquisicaoCard.textContent = '--';
            valorInicialHidden.value = '';
            dataAquisicaoHidden.value = '';
            vlcHidden.value = '';
            valorAtualizadoHidden.value = '';
            novaDepreciacaoAnualHidden.value = '';
            return;
        }

        const precoRaw = selected.dataset.preco ?? '0';
        const dataAquisicaoRaw = selected.dataset.data ?? null;

        const valorInicial = Number(precoRaw) || 0;
        const taxa = parseFloat(taxaInput.value) || 0;
        const vidaUtil = parseInt(vidaUtilInput.value, 10) || 1;
        const valorJusto = parseFloat(valorJustoInput.value);
        const valorResidual = parseFloat(valorResidualInput.value) || 0;

        const dtAq = dataAquisicaoRaw ? new Date(dataAquisicaoRaw) : new Date();
        const dtReav = dataReavaliacaoInput.value ? new Date(dataReavaliacaoInput.value) : new Date();

        let anosUso = (dtReav - dtAq) / (1000 * 60 * 60 * 24 * 365);
        if (!isFinite(anosUso) || anosUso < 0) anosUso = 0;

        const depreciacaoAcumulada = valorInicial * (taxa / 100) * anosUso;
        const vlc = valorInicial - depreciacaoAcumulada;
        const vJusto = isFinite(valorJusto) ? valorJusto : valorInicial;
        const valorAtualizado = vlc + (vJusto - vlc);
        const novaDepreciacaoAnual = vidaUtil > 0 ? (valorAtualizado - valorResidual) / vidaUtil : 0;

        precoCard.textContent = fmt(valorInicial);
        vlcCard.textContent = fmt(vlc);
        valorAtualizadoCard.textContent = fmt(valorAtualizado);
        novaDepreciacaoCard.textContent = fmt(novaDepreciacaoAnual).replace(' Kz', ' Kz/ano');
        dataAquisicaoCard.textContent = dtAq.toISOString().slice(0, 10);

        valorInicialHidden.value = valorInicial.toFixed(2);
        dataAquisicaoHidden.value = dtAq.toISOString().slice(0, 10);
        vlcHidden.value = vlc.toFixed(2);
        valorAtualizadoHidden.value = valorAtualizado.toFixed(2);
        novaDepreciacaoAnualHidden.value = novaDepreciacaoAnual.toFixed(2);
    }

    bemSelect.addEventListener('change', calcularReavaliacao);
    [taxaInput, vidaUtilInput, valorJustoInput, valorResidualInput, dataReavaliacaoInput].forEach((el) => {
        el.addEventListener('input', calcularReavaliacao);
        el.addEventListener('change', calcularReavaliacao);
    });

    definirDataReavaliacaoAtual();
    setInterval(definirDataReavaliacaoAtual, 60000);
    calcularReavaliacao();
});
</script>
@endsection
