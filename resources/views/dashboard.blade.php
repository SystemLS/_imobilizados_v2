@extends('layouts.app')

@section('content')
<div class="px-6 py-6 min-h-screen bg-gray-100 text-gray-800 space-y-8">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-gray-700">Dashboard de Gestão de Ativos</h1>
    </div>

    {{-- FILTROS --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-700 flex items-center">
            <i data-feather="filter" class="w-5 h-5 mr-2 text-blue-600"></i> Filtros
        </h2>

        <form id="filtroForm" method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap gap-4 items-end">
            @php
                $filtros = [
                    ['name'=>'provincia_id','label'=>'Província','options'=>$provincias,'key'=>'ProvinciaId','value'=>'Nome'],
                    ['name'=>'edificio_id','label'=>'Edifício','options'=>$edificios,'key'=>'EdificioId','value'=>'Nome'],
                    ['name'=>'grupo_id','label'=>'Grupo','options'=>$grupos,'key'=>'GrupoId','value'=>'Nome'],
                ];
            @endphp

            @foreach ($filtros as $f)
            @php
                $selecionado = request($f['name']);
                $opcaoSelecionada = $selecionado ? $f['options']->firstWhere($f['key'], $selecionado) : null;
                $displayInicial = $opcaoSelecionada ? $opcaoSelecionada->{$f['value']} : $f['label'];
            @endphp
            <div x-data="{ open:false, selected:@js((string) ($selecionado ?? '')), display:@js($displayInicial) }" class="relative w-64">
                <button type="button" @click="open=!open"
                    class="w-full bg-gray-50 border border-gray-300 rounded-xl shadow-sm px-4 py-2 text-left flex justify-between items-center hover:bg-white focus:ring-2 focus:ring-blue-400 transition-all">
                    <span x-text="display" class="truncate"></span>
                    <svg :class="{'rotate-180':open}" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <ul x-cloak x-show="open" @click.outside="open=false" x-transition
                    class="absolute z-10 mt-1 w-full bg-white border rounded-xl shadow-lg max-h-60 overflow-auto">
                    <li @click="selected=''; display='{{ $f['label'] }}'; open=false"
                        class="px-4 py-2 hover:bg-blue-100 cursor-pointer">Todos</li>
                    @foreach ($f['options'] as $opt)
                        <li @click="selected='{{ $opt->{$f['key']} }}'; display='{{ $opt->{$f['value']} }}'; open=false"
                            class="px-4 py-2 hover:bg-blue-100 cursor-pointer">
                            {{ $opt->{$f['value']} }}
                        </li>
                    @endforeach
                </ul>
                <input type="hidden" name="{{ $f['name'] }}" :value="selected">
            </div>
            @endforeach

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl px-5 py-2">
                    Aplicar
                </button>
                <button type="button" onclick="window.location='{{ route('dashboard') }}'"
                    class="bg-gray-300 hover:bg-gray-400 rounded-xl px-5 py-2">
                    Limpar
                </button>
            </div>
        </form>
    </div>

    {{-- CARDS --}}
    <div class="bg-white rounded-2xl shadow-lg border p-6">
        <h2 class="text-lg font-semibold mb-4 flex items-center">
            <i data-feather="layout" class="w-5 h-5 mr-2 text-blue-600"></i> Visão Geral
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gray-50 shadow-md rounded-2xl p-5 border-l-4 border-blue-500">
                <h2>Total de Ativos</h2>
                <p class="text-3xl font-bold">{{ $totalAtivos }}</p>
            </div>
            <div class="bg-gray-50 shadow-md rounded-2xl p-5 border-l-4 border-yellow-500">
                <h2>Em Manutenção</h2>
                <p class="text-3xl font-bold">{{ $ativosManutencao }}</p>
            </div>
            <div class="bg-gray-50 shadow-md rounded-2xl p-5 border-l-4 border-green-500">
                <h2>Manutenções no Mês</h2>
                <p class="text-3xl font-bold">{{ $manutencoesMes }}</p>
            </div>
            <div class="bg-gray-50 shadow-md rounded-2xl p-5 border-l-4 border-indigo-500">
                <h2>Valor Patrimonial</h2>
                <p class="text-3xl font-bold">{{ number_format($valorTotalPatrimonial,2,',','.') }}</p>
            </div>
        </div>
    </div>

    {{-- GRÁFICOS (verificação de dados) --}}
    @php
        $temDadosGraficos =
            collect($categoriasValues)->sum() > 0 ||
            collect($estadosValues)->sum() > 0 ||
            collect($gruposPercent)->sum() > 0 ||
            collect($evolucaoValores)->sum() > 0;
    @endphp

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 mb-12">
        <h2 class="text-lg font-semibold mb-6 text-gray-700 flex items-center">
            <i data-feather="bar-chart-2" class="w-5 h-5 mr-2 text-blue-600"></i> Análises Gráficas
        </h2>

        @if(!$temDadosGraficos)
            <div class="flex flex-col items-center justify-center py-20 text-gray-500">
                <i data-feather="alert-circle" class="w-12 h-12 mb-4"></i>
                <p class="text-xl font-semibold">Sem dados para apresentar</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @php
                    $graficos = [
                        ['id'=>'chartCategorias','title'=>'Ativos por Categoria (Grupo - Categoria)'],
                        ['id'=>'chartEstados','title'=>'Ativos por Estado'],
                        ['id'=>'chartGrupos','title'=>'Percentual por Grupo'],
                        ['id'=>'chartEvolucao','title'=>'Evolução Patrimonial']
                    ];
                @endphp

                @foreach ($graficos as $g)
                    <div class="bg-gray-50 p-5 rounded-xl shadow-inner chart-container mb-12">
                        <h3 class="text-lg font-semibold mb-3">{{ $g['title'] }}</h3>
                        <canvas id="{{ $g['id'] }}" class="w-full h-[350px]"></canvas>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

{{-- SCRIPTS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const baseOptions = {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 1000, easing: 'easeOutQuart' },
        plugins: { legend: { display: true, position: 'bottom' }, tooltip: { mode: 'index', intersect: false } },
        scales: { y: { beginAtZero: true } }
    };

    const categoriasColors = @json($categoriasLabels).map((_, i) => {
        const colors = ['#3B82F6','#FBBF24','#10B981','#EF4444','#8B5CF6','#F472B6','#F59E0B','#6366F1','#14B8A6','#E11D48'];
        return colors[i % colors.length];
    });

    if(document.getElementById('chartCategorias')) {
        // Ativos por Categoria
        new Chart(document.getElementById('chartCategorias'), {
            type: 'bar',
            data: {
                labels: @json($categoriasLabels),
                datasets:[{ label:'Ativos', data: @json($categoriasValues), backgroundColor: categoriasColors, borderRadius:6, borderSkipped:false }]
            },
            options: baseOptions
        });

        // Ativos por Estado
        new Chart(document.getElementById('chartEstados'), {
            type: 'bar',
            data: {
                labels: @json($estadosLabels),
                datasets:[{
                    label:'Ativos',
                    data:@json($estadosValues),
                    backgroundColor:['#10B981','#FBBF24','#EF4444','#6366F1','#3B82F6'],
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) { return Number(value); }
                        }
                    }
                }
            }
        });

        // Percentual por Grupo
        new Chart(document.getElementById('chartGrupos'), {
            type: 'doughnut',
            data: {
                labels: @json($gruposLabels),
                datasets: [{ data: @json($gruposPercent), backgroundColor: ['#3B82F6','#FBBF24','#10B981','#EF4444','#8B5CF6','#F472B6','#F59E0B','#6366F1','#14B8A6'], hoverOffset: 15 }]
            },
            options: {
                cutout: '50%',
                rotation: -90,
                plugins: {
                    tooltip: { callbacks: { label: c => c.label + ': ' + c.parsed + '%' } },
                    legend: { display:true, position:'bottom' }
                }
            }
        });

        // Evolução Patrimonial
        new Chart(document.getElementById('chartEvolucao'), {
            type: 'line',
            data: {
                labels: @json($evolucaoLabels),
                datasets:[{
                    label:'Valor Patrimonial',
                    data:@json($evolucaoValores),
                    borderColor:'#3B82F6',
                    backgroundColor:'rgba(59,130,246,0.2)',
                    fill:true,
                    tension:0.3
                }]
            },
            options: baseOptions
        });
    }

    feather.replace();
});
</script>

<style>
.chart-container { width: 100%; max-width: 800px; height: 400px; margin:auto; }
[x-cloak] { display: none !important; }
</style>
@endsection
