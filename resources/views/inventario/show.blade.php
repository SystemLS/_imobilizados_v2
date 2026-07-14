@extends('layouts.app')

@section('title', 'Detalhes do Ativo')

@section('content')
<div class="container mx-auto p-4 sm:p-6">

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-3">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-3">
            {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded mb-3">
            {{ session('info') }}
        </div>
    @endif

    <h1 class="text-2xl md:text-3xl font-bold mb-6 text-gray-800 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 9V5.25m4.5 3.75V5.25M3 9h18M4.5 9V19.5a2.25 2.25 0 002.25 2.25h10.5A2.25 2.25 0 0019.5 19.5V9" />
        </svg>
        Ativo — {{ $bem->Nome }}
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Card de informações --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all p-5">
            <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-3 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6l4 2" />
                </svg>
                Informações do Ativo
            </h3>

            <ul class="space-y-2 text-sm text-gray-600">
                <li>
                    <span class="font-semibold text-gray-800">Etiqueta: </span>{{ $bem->Etiqueta }}</li>
                <li><span class="font-semibold text-gray-800">Marca / Modelo:</span> {{ $bem->Marca }} {{ $bem->Modelo }}</li>
                <li><span class="font-semibold text-gray-800">Nº Série:</span> {{ $bem->NumeroSerieManual ?? '-' }}</li>
                <li><span class="font-semibold text-gray-800">Localização:</span>
                    {{ optional($bem->sala->piso->edificio->provincia)->Nome ?? '-' }} -
                    {{ optional($bem->sala->piso->edificio)->Nome ?? '-' }} -
                    {{ $bem->sala->Nome ?? '-' }}
                </li>
                <li><span class="font-semibold text-gray-800">Preço de Aquisição:</span>
                    <span class="text-green-700 font-medium">{{ number_format($bem->preco_aquisicao ?? 0, 2, ',', '.') }} Kz</span>
                </li>
            </ul>

            {{-- Foto do ativo --}}
            @if($bem->Foto1)
            <div class="mt-4">
                <h4 class="font-semibold text-gray-700 mb-2">Fotografia do Ativo</h4>
                <img src="{{ asset('storage/' . $bem->Foto1) }}" alt="Foto do Ativo" class="w-48 h-48 object-cover rounded cursor-pointer" onclick="openModal(this)">
            </div>
            @endif
        </div>

        {{-- Card de ações --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all p-5 space-y-6">
            <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-2 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                </svg>
                Ações do Ativo
            </h3>

            {{-- Atualizar Estado --}}
            <form action="{{ route('inventario.updateStatus', $bem) }}" method="POST" class="space-y-3">
                @csrf
                <label class="block font-medium text-sm text-gray-700">Estado de Conservação</label>
                <select name="status" class="border rounded px-3 py-2 w-full">
                    <option value="">-- Selecionar --</option>
                    @foreach(\App\Models\EstadoConservacao::all() as $est)
                        <option value="{{ $est->EstadoConservacaoId }}"
                            @selected($bem->EstadoConservacaoId == $est->EstadoConservacaoId)>
                            {{ $est->Nome }}
                        </option>
                    @endforeach
                </select>
                <button class="w-full md:w-auto px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Atualizar Estado
                </button>
            </form>

            <hr class="border-gray-200">

            {{-- Atualizar Dados do Bem --}}
            <form action="{{ route('inventario.update', $bem) }}" method="POST" class="space-y-3">
                @csrf
                <label class="block font-medium text-sm text-gray-700">Nome do Ativo</label>
                <input name="Nome" value="{{ $bem->Nome }}" class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-green-500 focus:outline-none" />

                <label class="block font-medium text-sm text-gray-700">Etiqueta</label>
                <input value="{{ $bem->Etiqueta }}" readonly class="border border-gray-300 rounded px-2 py-1 w-full bg-gray-100 cursor-not-allowed" />

                <button class="w-full md:w-auto px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    Salvar Alterações
                </button>
            </form>
        </div>
    </div>

    {{-- Voltar --}}
    <div class="mt-8 text-center">
        <a href="{{ route('inventario.index') }}"
           class="inline-block bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition">
           Voltar ao Inventário
        </a>
    </div>

</div>

{{-- Modal para imagem --}}
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center hidden z-50">
    <span class="absolute top-4 right-6 text-white text-3xl cursor-pointer font-bold" onclick="closeModal()">&times;</span>
    <img id="modalImage" class="max-h-[90%] max-w-[90%] object-contain rounded" src="" alt="Foto do Ativo">
</div>

<script>
    function openModal(img) {
        document.getElementById('modalImage').src = img.src;
        document.getElementById('imageModal').classList.remove('hidden');
    }
    function closeModal() {
        document.getElementById('imageModal').classList.add('hidden');
    }
</script>

@endsection
