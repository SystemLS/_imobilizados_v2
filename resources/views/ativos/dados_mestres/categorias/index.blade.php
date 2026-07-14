@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-2 sm:px-4 max-w-6xl">

    {{-- ✅ Mensagem de sucesso --}}
    @if(session('success'))
        <div id="alertaSucesso" class="mb-4 p-4 rounded-lg bg-green-100 text-green-800 border border-green-300">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(() => document.getElementById('alertaSucesso')?.remove(), 4000);
        </script>
    @endif

    {{-- ✅ Cabeçalho --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-gray-800">Lista de Categorias</h1>

        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            @auth
                @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                    <a href="{{ route('dados_mestres.categorias.create') }}"
                       class="px-4 sm:px-6 py-2 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 transition text-center">
                        Nova Categoria
                    </a>
                @endif
            @endauth

            <a href="{{ route('dados_mestres.index') }}"
               class="px-4 sm:px-6 py-2 bg-gray-500 text-white font-semibold rounded-lg shadow hover:bg-gray-600 transition text-center">
                Voltar à Lista Geral
            </a>

            @include('ativos.dados_mestres.partials.export-buttons', ['section' => 'categorias'])
        </div>
    </div>

    {{-- 🔍 Filtros --}}
    <div class="bg-white p-4 rounded-xl shadow-md mb-6">
        <form id="filtroForm" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Grupo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Grupo</label>
                <select name="GrupoId" id="GrupoId" class="w-full border-gray-300 rounded-lg p-2 text-gray-700 text-sm">
                    <option value="">Todos os grupos</option>
                    @foreach($grupos as $grupo)
                        <option value="{{ $grupo->GrupoId }}" {{ request('GrupoId') == $grupo->GrupoId ? 'selected' : '' }}>
                            {{ $grupo->Nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Pesquisa --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pesquisa</label>
                <input type="text" name="q" id="q" placeholder="Pesquisar categoria ou grupo..."
                       value="{{ request('q') }}"
                       class="w-full border-gray-300 rounded-lg p-2 text-gray-700 text-sm">
            </div>

            {{-- Botões --}}
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition text-sm">
                    Pesquisar
                </button>
                <button type="button" id="limparFiltros"
                        class="w-full px-4 py-2 bg-gray-200 text-gray-800 rounded-lg shadow hover:bg-gray-300 transition text-sm">
                    Limpar
                </button>
            </div>
        </form>
    </div>

    {{-- 📋 Tabela / Cards --}}
    <div id="tabelaContainer" class="bg-white p-4 sm:p-6 rounded-xl shadow-lg">

        @if ($categorias->isEmpty())
            <p class="text-gray-600 text-center py-4 text-sm">Nenhuma categoria encontrada.</p>
        @else

            {{-- Desktop Table --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-xl text-sm sm:text-base">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                        <tr>
                            <th class="px-4 py-3 text-left w-1/12">#</th>
                            <th class="px-4 py-3 text-left w-4/12">Grupo</th>
                            <th class="px-4 py-3 text-left w-5/12">Categoria</th>
                            <th class="px-4 py-3 text-right w-2/12">Ações</th>
                        </tr>
                    </thead>

                    <tbody id="tabelaBody" class="divide-y divide-gray-200">
                        @foreach ($categorias as $index => $categoria)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $index + 1 + ($categorias->currentPage()-1)*$categorias->perPage() }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $categoria->grupo->Nome ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-800 font-medium">{{ $categoria->Nome }}</td>

                                {{-- ⚙️ Ações --}}
                                <td class="px-4 py-3">
                                    @auth
                                        @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                                            <div class="flex justify-end items-center gap-3 flex-nowrap">
                                                <a href="{{ route('dados_mestres.categorias.edit', $categoria->CategoriaId) }}"
                                                   class="px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 transition whitespace-nowrap text-sm">
                                                    Editar
                                                </a>
                                                <form action="{{ route('dados_mestres.categorias.destroy', $categoria->CategoriaId) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Tem certeza que deseja eliminar esta categoria?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="px-3 py-1 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 transition whitespace-nowrap text-sm">
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

            {{-- Mobile Cards --}}
            <div class="sm:hidden grid grid-cols-1 gap-3">
                @foreach ($categorias as $index => $categoria)
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 shadow-sm">
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600 font-medium text-sm">#{{ $index + 1 + ($categorias->currentPage()-1)*$categorias->perPage() }}</span>
                        </div>
                        <p class="text-gray-700 font-medium text-sm mb-1">{{ $categoria->grupo->Nome ?? '—' }}</p>
                        <p class="text-gray-800 font-semibold text-sm mb-2">{{ $categoria->Nome }}</p>

                        <div class="flex gap-2">
                            @auth
                                @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                                    <a href="{{ route('dados_mestres.categorias.edit', $categoria->CategoriaId) }}"
                                       class="w-1/2 py-2 bg-yellow-500 text-white text-center rounded-lg text-sm hover:bg-yellow-600 transition">
                                        Editar
                                    </a>
                                    <form action="{{ route('dados_mestres.categorias.destroy', $categoria->CategoriaId) }}"
                                          method="POST"
                                          onsubmit="return confirm('Tem certeza que deseja eliminar esta categoria?');"
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

            {{-- 📄 Paginação --}}
            <div class="mt-4" id="paginacao">
                {{ $categorias->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ⚙️ Script AJAX --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('filtroForm');
    const limparBtn = document.getElementById('limparFiltros');
    const tabelaContainer = document.getElementById('tabelaContainer');
    const q = document.getElementById('q');
    const grupo = document.getElementById('GrupoId');

    async function atualizarTabela(url = null) {
        const params = new URLSearchParams({ q: q.value, GrupoId: grupo.value });
        const fetchUrl = url || ("{{ route('dados_mestres.categorias.index') }}?" + params.toString());

        try {
            const response = await fetch(fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
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

    grupo.addEventListener('change', atualizarTabela);

    limparBtn.addEventListener('click', () => {
        q.value = '';
        grupo.value = '';
        atualizarTabela();
    });

    aplicarEventosPaginacao();
});
</script>
@endsection
