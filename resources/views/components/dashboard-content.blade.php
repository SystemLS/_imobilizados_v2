@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 space-y-6" data-aos="fade-up">

    {{-- Cards de resumo --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        @php
            $cards = [
                ['title'=>'Ativos Cadastrados','value'=>$totalAtivos ?? 'Sem Dados','icon'=>'box','bg'=>'blue'],
                ['title'=>'Manutenções Pendentes','value'=>$manutencoesPendentes ?? 'Sem Dados','icon'=>'tool','bg'=>'yellow'],
                ['title'=>'Reavaliações','value'=>$reavaliacoesCount ?? 'Sem Dados','icon'=>'refresh-cw','bg'=>'green'],
                ['title'=>'Ativos Inativos','value'=>$ativosInativos ?? 'Sem Dados','icon'=>'alert-circle','bg'=>'red'],
            ];
        @endphp

        @foreach($cards as $card)
        <div class="bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition duration-500 ease-in-out transform hover:-translate-y-1">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">{{ $card['title'] }}</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $card['value'] }}</h3>
                </div>
                <div class="p-3 rounded-lg {{ 'bg-'.$card['bg'].'-100' }}">
                    <i data-feather="{{ $card['icon'] }}" class="{{ 'text-'.$card['bg'].'-600' }}"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Gráficos interativos --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-md p-5" data-aos="fade-right">
            <h4 class="text-gray-700 font-semibold mb-4">Distribuição de Ativos por Categoria</h4>
            <canvas id="categoriaChart" class="w-full h-80"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow-md p-5" data-aos="fade-left">
            <h4 class="text-gray-700 font-semibold mb-4">Ativos por Edifício</h4>
            <canvas id="edificioChart" class="w-full h-80"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-md p-5" data-aos="fade-up-right">
            <h4 class="text-gray-700 font-semibold mb-4">Ativos por Piso</h4>
            <canvas id="pisoChart" class="w-full h-80"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow-md p-5" data-aos="fade-up-left">
            <h4 class="text-gray-700 font-semibold mb-4">Ativos por Sala</h4>
            <canvas id="salaChart" class="w-full h-80"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-5" data-aos="zoom-in">
        <h4 class="text-gray-700 font-semibold mb-4">Valores de Aquisição por Categoria</h4>
        <canvas id="financeiroChart" class="w-full h-80"></canvas>
    </div>

    {{-- Tabela dos últimos ativos --}}
    <div class="bg-white rounded-xl shadow-md p-5" data-aos="fade-up">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-gray-700 font-semibold">Últimos Ativos Cadastrados</h4>
            <a href="{{ route('ativos.index') }}" class="text-blue-600 text-sm hover:underline">Ver todos</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-left text-sm uppercase">
                        <th class="py-3 px-4 border-b">Código</th>
                        <th class="py-3 px-4 border-b">Nome</th>
                        <th class="py-3 px-4 border-b">Categoria</th>
                        <th class="py-3 px-4 border-b">Localização</th>
                        <th class="py-3 px-4 border-b">Estado</th>
                        <th class="py-3 px-4 border-b text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ultimosAtivos as $ativo)
                    <tr class="hover:bg-gray-50 transition duration-300 ease-in-out">
                        <td class="py-3 px-4 border-b text-sm">{{ $ativo->BemId ?? 'Sem Dados' }}</td>
                        <td class="py-3 px-4 border-b text-sm">{{ $ativo->Nome ?? 'Sem Dados' }}</td>
                        <td class="py-3 px-4 border-b text-sm">{{ $ativo->subcategoria->Nome ?? 'Sem Dados' }}</td>
                        <td class="py-3 px-4 border-b text-sm">{{ $ativo->sala->Nome ?? 'Sem Dados' }}</td>
                        <td class="py-3 px-4 border-b text-sm">
                            @php
                                $estado = $ativo->estadoConservacao->Nome ?? 'Sem Dados';
                                $color = match($estado) {
                                    'Ativo' => 'bg-green-100 text-green-700',
                                    'Inativo' => 'bg-gray-100 text-gray-700',
                                    'Em manutenção' => 'bg-yellow-100 text-yellow-700',
                                    default => 'bg-blue-100 text-blue-700',
                                };
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs {{ $color }}">{{ $estado }}</span>
                        </td>
                        <td class="py-3 px-4 border-b text-center">
                            <a href="{{ route('ativos.show', $ativo->BemId) }}" class="text-blue-600 hover:underline text-sm">Ver</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-gray-500">Sem Dados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Scripts Chart.js com animações e cores modernas --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
            duration: 800,
            easing: 'easeOutQuart'
        },
        plugins: {
            legend: { labels: { font: { size: 14 } } },
            tooltip: { enabled: true, mode: 'index', intersect: false }
        },
        layout: { padding: { top: 10, bottom: 10, left: 10, right: 10 } }
    };

    // Distribuição por Categoria (Doughnut)
    new Chart(document.getElementById('categoriaChart'), {
        type: 'doughnut',
        data: {
            labels: @json($categoriasLabels ?? []),
            datasets: [{
                label: 'Ativos por Categoria',
                data: @json($categoriasValues ?? []),
                backgroundColor: ['#3B82F6','#FBBF24','#10B981','#EF4444','#8B5CF6','#F472B6','#F59E0B','#6366F1','#14B8A6'],
                hoverOffset: 10
            }]
        },
        options: {...commonOptions, cutout: '50%'}
    });

    // Gráficos de barras: Edifício, Piso e Sala
    function createBarChart(canvasId, labels, data, color, labelText) {
        return new Chart(document.getElementById(canvasId), {
            type: 'bar',
            data: { labels: labels, datasets: [{ label: labelText, data: data, backgroundColor: color, borderRadius: 8, barPercentage: 0.6 }] },
            options: {
                ...commonOptions,
                scales: { y: { beginAtZero: true, grace: '10%' }, x: { ticks: { autoSkip: false } } }
            }
        });
    }

    createBarChart('edificioChart', @json($edificiosLabels ?? []), @json($edificiosValues ?? []), '#3B82F6', 'Ativos por Edifício');
    createBarChart('pisoChart', @json($pisosLabels ?? []), @json($pisosValues ?? []), '#10B981', 'Ativos por Piso');
    createBarChart('salaChart', @json($salasLabels ?? []), @json($salasValues ?? []), '#FBBF24', 'Ativos por Sala');

    // Valores de Aquisição por Categoria (Pie)
    new Chart(document.getElementById('financeiroChart'), {
        type: 'pie',
        data: {
            labels: @json($categoriasLabels ?? []),
            datasets: [{
                label: 'Valores por Categoria',
                data: @json($valoresCategoria ?? []),
                backgroundColor: ['#3B82F6','#FBBF24','#10B981','#EF4444','#8B5CF6','#F472B6','#F59E0B','#6366F1','#14B8A6'],
                hoverOffset: 10
            }]
        },
        options: commonOptions
    });

    feather.replace({ width: 20, height: 20 });
</script>


@endsection
