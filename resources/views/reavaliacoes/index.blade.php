@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 px-2 sm:px-4">

@php $perfil = auth()->user()->perfil; @endphp

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-gray-800">Reavaliações de Activos</h1>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('reavaliacoes.create') }}"
               class="px-4 sm:px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 transition whitespace-nowrap">
               Nova Reavaliacao
            </a>

            <a href="{{ route('reavaliacoes.export.excel', request()->query()) }}" class="flex items-center gap-1 px-4 py-2 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition">
                <i data-feather="file"></i> Exportar Excel
            </a>

            <a href="{{ route('reavaliacoes.export.pdf', request()->query()) }}" class="flex items-center gap-1 px-4 py-2 bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600 transition">
                <i data-feather="file-text"></i> Exportar PDF
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 overflow-hidden">
        <form method="GET" class="mb-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 bg-gray-50 p-4 rounded-lg shadow-sm">
            <input
                type="text"
                name="etiqueta"
                placeholder="Pesquisar Etiqueta"
                value="{{ request('etiqueta') }}"
                class="border rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400"
            >

            <input
                type="number"
                step="0.01"
                min="0"
                name="valor_inicial_min"
                placeholder="Valor Inicial Min"
                value="{{ request('valor_inicial_min') }}"
                class="border rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400"
            >

            <input
                type="number"
                step="0.01"
                min="0"
                name="valor_inicial_max"
                placeholder="Valor Inicial Max"
                value="{{ request('valor_inicial_max') }}"
                class="border rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400"
            >

            <input
                type="date"
                name="data_reavaliacao"
                value="{{ request('data_reavaliacao') }}"
                class="border rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400"
            >

            <div class="flex gap-2 mt-2 md:mt-0 col-span-full">
                <button type="submit"
                    class="bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 transition w-full md:w-auto">
                    Pesquisar
                </button>
                <a href="{{ route('reavaliacoes.index') }}"
                    class="bg-gray-300 text-gray-800 px-3 py-2 rounded-lg hover:bg-gray-400 transition w-full md:w-auto text-center">
                    Limpar
                </a>
            </div>
        </form>

        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full table-auto border-collapse min-w-[800px] text-sm sm:text-base text-center">
                <thead class="bg-gray-100 text-gray-700 uppercase">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-left">Activo</th>
                        <th class="border border-gray-300 px-4 py-2">Etiqueta</th>
                        <th class="border border-gray-300 px-4 py-2">Valor inicial</th>
                        <th class="border border-gray-300 px-4 py-2">Valor Atualizado</th>
                        <th class="border border-gray-300 px-4 py-2">Data de Reavaliacao</th>
                        <th class="border border-gray-300 px-4 py-2">Metodo</th>
                        <th class="border border-gray-300 px-4 py-2">Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reavaliacoes as $reavaliacao)
                        @php $bem = $reavaliacao->bem; @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="border border-gray-300 px-4 py-2 text-left text-gray-700">{{ $bem->Nome ?? 'N/D' }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $bem->Etiqueta ?? '-' }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ number_format($reavaliacao->valor_inicial ?? 0, 2, ',', '.') }} Kz</td>
                            <td class="border border-gray-300 px-4 py-2 font-semibold text-green-700">{{ number_format($reavaliacao->valor_atualizado, 2, ',', '.') }} Kz</td>
                            <td class="border border-gray-300 px-4 py-2">{{ \Carbon\Carbon::parse($reavaliacao->data_reavaliacao)->format('d/m/Y') }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $reavaliacao->metodo }}</td>
                            <td class="border border-gray-300 px-4 py-2">
                                <div class="flex justify-end gap-2 flex-nowrap">
                                    <a href="{{ route('reavaliacoes.show', $reavaliacao->id) }}"
                                       class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm whitespace-nowrap">
                                       Ver
                                    </a>

                                    @if(in_array(auth()->user()->perfil, ['administrador','gestor','tecnico_contabilidade']))
                                        <a href="{{ route('reavaliacoes.edit', $reavaliacao->id) }}"
                                           class="px-3 py-1 bg-yellow-400 text-white rounded-lg hover:bg-yellow-500 transition text-sm whitespace-nowrap">
                                           Editar
                                        </a>

                                        <form action="{{ route('reavaliacoes.destroy', $reavaliacao->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm whitespace-nowrap"
                                                onclick="return confirm('Deseja realmente excluir esta reavaliacao?');">
                                                Excluir
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="border border-gray-300 px-4 py-4 text-center text-gray-500">
                                Nenhuma reavaliacao encontrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="sm:hidden grid grid-cols-1 gap-3">
            @foreach($reavaliacoes as $reavaliacao)
                @php $bem = $reavaliacao->bem; @endphp
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 shadow-sm">
                    <p class="text-gray-800 font-semibold text-sm mb-1">Activo: {{ $bem->Nome ?? 'N/D' }}</p>
                    <p class="text-gray-700 text-sm mb-1">Etiqueta: {{ $bem->Etiqueta ?? '-' }}</p>
                    <p class="text-gray-700 text-sm mb-1">Valor inicial: {{ number_format($reavaliacao->valor_inicial ?? 0, 2, ',', '.') }} Kz</p>
                    <p class="text-gray-700 text-sm mb-1 font-semibold text-green-700">Valor Atualizado: {{ number_format($reavaliacao->valor_atualizado, 2, ',', '.') }} Kz</p>
                    <p class="text-gray-700 text-sm mb-1">Data de Reavaliacao: {{ \Carbon\Carbon::parse($reavaliacao->data_reavaliacao)->format('d/m/Y') }}</p>
                    <p class="text-gray-700 text-sm mb-2">Metodo: {{ $reavaliacao->metodo }}</p>

                    <div class="flex gap-2">
                        <a href="{{ route('reavaliacoes.show', $reavaliacao->id) }}"
                           class="w-1/2 py-2 bg-blue-600 text-white text-center rounded-lg text-sm hover:bg-blue-700 transition">
                            Ver
                        </a>

                        @if(in_array(auth()->user()->perfil, ['administrador','gestor','tecnico_contabilidade']))
                            <a href="{{ route('reavaliacoes.edit', $reavaliacao->id) }}"
                               class="w-1/2 py-2 bg-yellow-400 text-white text-center rounded-lg text-sm hover:bg-yellow-500 transition">
                               Editar
                            </a>

                            <form action="{{ route('reavaliacoes.destroy', $reavaliacao->id) }}" method="POST" class="w-1/2">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full py-2 bg-red-500 text-white rounded-lg text-sm hover:bg-red-600 transition"
                                    onclick="return confirm('Deseja realmente excluir esta reavaliacao?');">
                                    Excluir
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $reavaliacoes->links() }}
        </div>

    </div>
</div>
@endsection
