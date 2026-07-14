@extends('layouts.app')

@section('title', 'Dados Mestres')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-8 text-center sm:text-left">🗂️ Dados Mestres</h1>

    @php
        use App\Models\EstadoConservacao;
        use App\Models\CondicaoAmbiental;
        $estados = EstadoConservacao::orderBy('Nome')->paginate(10);
        $condicoes_ambientais = CondicaoAmbiental::orderBy('Nome')->paginate(10); // ✅ Corrigido

        $secoes = [
            ['titulo' => 'Lista de Províncias', 'rota' => 'provincias', 'dados' => $provincias],
            ['titulo' => 'Lista de Edifícios', 'rota' => 'edificios', 'dados' => $edificios],
            ['titulo' => 'Lista de Pisos', 'rota' => 'pisos', 'dados' => $pisos],
            ['titulo' => 'Lista de Salas', 'rota' => 'salas', 'dados' => $salas],
            ['titulo' => 'Lista de Grupos', 'rota' => 'grupos', 'dados' => $grupos],
            ['titulo' => 'Lista de Categorias', 'rota' => 'categorias', 'dados' => $categorias],
            ['titulo' => 'Lista de Subcategorias', 'rota' => 'subcategorias', 'dados' => $subcategorias],
            ['titulo' => 'Estados de Conservação', 'rota' => 'estado_conservacao', 'dados' => $estados],
            ['titulo' => 'Condições Ambientais', 'rota' => 'condicoes_ambientais', 'dados' => $condicoes_ambientais],
            ['titulo' => 'Lista de Materiais', 'rota' => 'materiais', 'dados' => $materiais],
        ];
    @endphp

    @foreach($secoes as $secao)
        <div class="bg-white rounded-2xl shadow-lg mb-10 p-5 sm:p-6">

            {{-- Cabeçalho --}}
            <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-5 gap-3">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 text-center sm:text-left">{{ $secao['titulo'] }}</h2>

                <div class="flex flex-col sm:flex-row gap-2 justify-center sm:justify-end">
                    @auth
                        @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                            <a href="{{ route('dados_mestres.'.$secao['rota'].'.create') }}"
                               class="px-5 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition text-center">
                               Novo
                            </a>
                        @endif
                    @endauth

                    <a href="{{ route('dados_mestres.'.$secao['rota'].'.index') }}"
                       class="px-5 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition text-center">
                       Ver Todos
                    </a>
                </div>
            </div>

            {{-- Tabela Desktop --}}
            <div class="hidden sm:block overflow-x-auto">
                @if ($secao['dados']->isEmpty())
                    <p class="text-gray-600 text-center py-4">Nenhum registro encontrado.</p>
                @else
                    <table class="min-w-full border border-gray-200 rounded-xl text-sm sm:text-base">
                        <thead class="bg-gray-100 text-gray-700 uppercase">
                            <tr>
                                <th class="px-4 py-2 text-left w-12">#</th>
                                <th class="px-4 py-2 text-left">Nome</th>
                                <th class="px-6 py-2 text-right pr-8">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($secao['dados'] as $index => $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-2 text-gray-700">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 text-gray-800 font-medium truncate max-w-[300px]">{{ $item->Nome ?? '—' }}</td>
                                    <td class="px-6 py-3 text-right pr-8">
                                        <div class="inline-flex justify-end gap-3">
                                            @auth
                                                @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                                                    <a href="{{ route('dados_mestres.'.$secao['rota'].'.edit', $item->getKey()) }}"
                                                       class="px-3 py-1 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                                                       Editar
                                                    </a>

                                                    <form action="{{ route('dados_mestres.'.$secao['rota'].'.destroy', $item->getKey()) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Tem certeza que deseja eliminar este item?');"
                                                          class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                @endif
                                            @endauth
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            {{-- Cards Mobile --}}
            <div class="block sm:hidden">
                @if ($secao['dados']->isEmpty())
                    <p class="text-gray-600 text-center py-4">Nenhum registro encontrado.</p>
                @else
                    <div class="grid grid-cols-1 gap-4">
                        @foreach ($secao['dados'] as $index => $item)
                            <div class="border border-gray-200 rounded-xl shadow-sm p-4 bg-gray-50">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-600 font-medium">#{{ $index + 1 }}</span>
                                </div>
                                <p class="text-gray-800 font-semibold mb-4">{{ $item->Nome ?? '—' }}</p>

                                <div class="flex gap-2 w-full">
                                    @auth
                                        @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                                            <a href="{{ route('dados_mestres.'.$secao['rota'].'.edit', $item->getKey()) }}"
                                               class="w-1/2 py-2 bg-yellow-500 text-white rounded-lg text-center hover:bg-yellow-600 transition">
                                               Editar
                                            </a>

                                            <form action="{{ route('dados_mestres.'.$secao['rota'].'.destroy', $item->getKey()) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Tem certeza que deseja eliminar este item?');"
                                                  class="w-1/2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="w-full py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                                    Eliminar
                                                </button>
                                            </form>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection
