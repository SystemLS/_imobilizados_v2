@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-2 sm:px-4 max-w-6xl">

    {{-- Mensagem de sucesso --}}
    @if(session('success'))
        <div id="alertaSucesso" class="mb-4 p-4 rounded-lg bg-green-100 text-green-800 border border-green-300">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(() => document.getElementById('alertaSucesso')?.remove(), 4000);
        </script>
    @endif

    {{-- Cabeçalho com botões --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-gray-800">Lista de Subcategorias</h1>

        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            @auth
                @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                    <a href="{{ route('dados_mestres.subcategorias.create') }}"
                        class="px-4 sm:px-6 py-2 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 transition text-center">
                        Nova Subcategoria
                    </a>
                @endif
            @endauth

            <a href="{{ route('dados_mestres.index') }}"
                class="px-4 sm:px-6 py-2 bg-gray-400 text-white font-semibold rounded-lg shadow hover:bg-gray-500 transition text-center">
                Voltar à Lista Geral
            </a>

            @include('ativos.dados_mestres.partials.export-buttons', ['section' => 'subcategorias'])
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white p-4 rounded-xl shadow-md mb-6">
        <form id="filtroForm" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            {{-- Grupo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Grupo</label>
                <select name="GrupoId" id="GrupoId" class="w-full border-gray-300 rounded-lg p-2 text-gray-700">
                    <option value="">Todos os grupos</option>
                    @foreach($grupos as $grupo)
                        <option value="{{ $grupo->GrupoId }}" {{ request('GrupoId') == $grupo->GrupoId ? 'selected' : '' }}>
                            {{ $grupo->Nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Categoria --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                <select name="CategoriaId" id="CategoriaId" class="w-full border-gray-300 rounded-lg p-2 text-gray-700">
                    <option value="">Todas as categorias</option>
                </select>
            </div>

            {{-- Pesquisa --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pesquisa</label>
                <input type="text" name="q" id="q" placeholder="Pesquisar subcategoria..."
                       value="{{ request('q') }}"
                       class="w-full border-gray-300 rounded-lg p-2 text-gray-700">
            </div>

            {{-- Botões --}}
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                    Pesquisar
                </button>
                <button type="button" id="limparFiltros"
                        class="w-full px-4 py-2 bg-gray-200 text-gray-800 rounded-lg shadow hover:bg-gray-300 transition">
                    Limpar
                </button>
            </div>
        </form>
    </div>

    {{-- Tabela / Cards --}}
    <div id="tabelaContainer" class="bg-white p-4 sm:p-6 rounded-xl shadow-lg">

        @if ($subcategorias->isEmpty())
            <p class="text-gray-600 text-center py-4">Nenhuma subcategoria encontrada.</p>
        @else

            {{-- Desktop --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-xl text-sm sm:text-base">
                    <thead class="bg-gray-100 text-gray-700 uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left w-1/12">#</th>
                            <th class="px-4 py-3 text-left w-3/12">Grupo</th>
                            <th class="px-4 py-3 text-left w-3/12">Categoria</th>
                            <th class="px-4 py-3 text-left w-3/12">Subcategoria</th>
                            <th class="px-4 py-3 text-right w-2/12">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($subcategorias as $index => $sub)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $index + 1 + ($subcategorias->currentPage()-1)*$subcategorias->perPage() }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $sub->categoria->grupo->Nome ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $sub->categoria->Nome ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-800 font-medium">{{ $sub->Nome }}</td>
                                <td class="px-4 py-3">
                                    @auth
                                        @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                                            <div class="flex justify-end items-center gap-3 flex-nowrap">
                                                <a href="{{ route('dados_mestres.subcategorias.edit', $sub->SubcategoriaId) }}"
                                                   class="px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 transition whitespace-nowrap">
                                                   Editar
                                                </a>
                                                <form action="{{ route('dados_mestres.subcategorias.destroy', $sub->SubcategoriaId) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Tem certeza que deseja eliminar esta subcategoria?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="px-3 py-1 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 transition whitespace-nowrap">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @endauth
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile --}}
            <div class="sm:hidden grid grid-cols-1 gap-3">
                @foreach ($subcategorias as $index => $sub)
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 shadow-sm">
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600 font-medium text-sm">#{{ $index + 1 + ($subcategorias->currentPage()-1)*$subcategorias->perPage() }}</span>
                        </div>
                        <p class="text-gray-700 text-sm mb-1"><strong>Grupo:</strong> {{ $sub->categoria->grupo->Nome ?? '—' }}</p>
                        <p class="text-gray-700 text-sm mb-1"><strong>Categoria:</strong> {{ $sub->categoria->Nome ?? '—' }}</p>
                        <p class="text-gray-800 font-semibold text-sm mb-2"><strong>Subcategoria:</strong> {{ $sub->Nome }}</p>

                        <div class="flex gap-2">
                            @auth
                                @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                                    <a href="{{ route('dados_mestres.subcategorias.edit', $sub->SubcategoriaId) }}"
                                       class="w-1/2 py-2 bg-yellow-500 text-white text-center rounded-lg text-sm hover:bg-yellow-600 transition">
                                        Editar
                                    </a>
                                    <form action="{{ route('dados_mestres.subcategorias.destroy', $sub->SubcategoriaId) }}"
                                          method="POST"
                                          onsubmit="return confirm('Tem certeza que deseja eliminar esta subcategoria?');"
                                          class="w-1/2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 transition">
                                            Eliminar
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Paginação --}}
            <div class="mt-4" id="paginacao">
                {{ $subcategorias->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Script AJAX + encadeamento --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('filtroForm');
    const limparBtn = document.getElementById('limparFiltros');
    const tabelaContainer = document.getElementById('tabelaContainer');
    const q = document.getElementById('q');
    const grupo = document.getElementById('GrupoId');
    const categoria = document.getElementById('CategoriaId');

    grupo.addEventListener('change', async () => {
        const grupoId = grupo.value;
        categoria.innerHTML = '<option>Carregando...</option>';
        try {
            const response = await fetch(`/dados-mestres/categorias/por-grupo/${grupoId}`);
            const data = await response.json();
            categoria.innerHTML = '<option value="">Todas as categorias</option>';
            data.forEach(cat => {
                const opt = document.createElement('option');
                opt.value = cat.CategoriaId;
                opt.textContent = cat.Nome;
                categoria.appendChild(opt);
            });
        } catch {
            categoria.innerHTML = '<option>Erro ao carregar</option>';
        }
        atualizarTabela();
    });

    async function atualizarTabela(url = null) {
        const params = new URLSearchParams({
            q: q.value,
            GrupoId: grupo.value,
            CategoriaId: categoria.value
        });

        const fetchUrl = url || ("{{ route('dados_mestres.subcategorias.index') }}?" + params.toString());

        try {
            const response = await fetch(fetchUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error("Erro ao carregar dados");

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const novaTabela = doc.querySelector('#tabelaContainer').innerHTML;
            tabelaContainer.innerHTML = novaTabela;
            aplicarEventosPaginacao();

        } catch (error) {
            console.error("Falha ao atualizar tabela:", error);
        }
    }

    function aplicarEventosPaginacao() {
        document.querySelectorAll('#paginacao a').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                const url = e.target.closest('a').getAttribute('href');
                atualizarTabela(url);
            });
        });
    }

    form.addEventListener('submit', e => {
        e.preventDefault();
        atualizarTabela();
    });

    let timeout;
    q.addEventListener('input', () => {
        clearTimeout(timeout);
        timeout = setTimeout(atualizarTabela, 500);
    });

    categoria.addEventListener('change', atualizarTabela);

    limparBtn.addEventListener('click', () => {
        q.value = '';
        grupo.value = '';
        categoria.innerHTML = '<option value="">Todas as categorias</option>';
        atualizarTabela();
    });

    aplicarEventosPaginacao();
});
</script>
@endsection
