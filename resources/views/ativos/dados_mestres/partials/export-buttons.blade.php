@php
    $exportBase = 'dados_mestres.' . $section . '.export';
@endphp

<div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
    <a href="{{ route($exportBase . '.excel', request()->query()) }}"
       class="px-4 sm:px-6 py-2 bg-green-500 text-white font-semibold rounded-lg shadow hover:bg-green-600 transition text-center">
        <span class="hidden sm:inline">Exportar</span> Excel
    </a>

    <a href="{{ route($exportBase . '.pdf', request()->query()) }}"
       class="px-4 sm:px-6 py-2 bg-red-500 text-white font-semibold rounded-lg shadow hover:bg-red-600 transition text-center">
        <span class="hidden sm:inline">Exportar</span> PDF
    </a>
</div>
