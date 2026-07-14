@extends('layouts.app')

@section('title','Follow Up — Inventário de Ativos')

@section('content')
<div class="container mx-auto p-4 md:p-6">

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2">
    <h1 class="text-2xl font-bold text-gray-700">🔍 Follow Up — Inventário</h1>
    <a href="{{ route('inventario.index') }}"
       class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600
              transition w-full sm:w-auto text-center sm:text-left">
        Voltar ao Inventário
    </a>
</div>


    {{-- Seleção em cascata responsiva --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @php $selectClasses = "border border-gray-300 rounded-lg px-3 py-2 w-full"; @endphp

        <div>
            <label class="font-semibold mb-1 block">Província</label>
            <select id="provincia" class="{{ $selectClasses }}">
                <option value="">-- Selecione --</option>
                @foreach($provincias as $prov)
                    <option value="{{ $prov->ProvinciaId }}">{{ $prov->Nome }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="font-semibold mb-1 block">Edifício</label>
            <select id="edificio" class="{{ $selectClasses }}" disabled>
                <option>-- Selecione --</option>
            </select>
        </div>

        <div>
            <label class="font-semibold mb-1 block">Piso</label>
            <select id="piso" class="{{ $selectClasses }}" disabled>
                <option>-- Selecione --</option>
            </select>
        </div>

        <div>
            <label class="font-semibold mb-1 block">Sala</label>
            <select id="sala" class="{{ $selectClasses }}" disabled>
                <option>-- Selecione --</option>
            </select>
        </div>
    </div>

    {{-- Card Bens da Sala --}}
    <div id="followupBox" class="hidden bg-white rounded-xl shadow-md p-4 mb-6">
        <h2 class="font-semibold text-lg mb-3">Ativos da Sala: <span id="salaNome" class="text-indigo-600"></span></h2>

        <div class="flex flex-wrap gap-2 mb-3 justify-start">
            <button id="checkAll" class="px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600 transition w-full sm:w-auto">Marcar Todos</button>
            <button id="uncheckAll" class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition w-full sm:w-auto">Desmarcar Todos</button>
        </div>

        <div class="overflow-x-auto mb-4">
            <table class="w-full text-sm border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 border text-center">#</th>
                        <th class="p-2 border">Etiqueta</th>
                        <th class="p-2 border">Nome</th>
                        <th class="p-2 border">Estado</th>
                        <th class="p-2 border text-center">Conferido</th>
                    </tr>
                </thead>
                <tbody id="bensTable"></tbody>
            </table>
        </div>

        <div class="mt-4">
            <label for="observacao" class="font-semibold mb-1 block">Observação</label>
            <textarea id="observacao" class="border border-gray-300 rounded-lg px-3 py-2 w-full" rows="3" placeholder="Digite aqui alguma observação..."></textarea>
        </div>

        <button id="btnSubmit" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition w-full md:w-auto">Finalizar FollowUp</button>
    </div>
</div>

<script>
const api = {
    edificios: id => `/followup/edificios/${id}`,
    pisos: id => `/followup/pisos/${id}`,
    salas: id => `/followup/salas/${id}`,
    bens: id => `/followup/bens/${id}`,
    submit: `/followup/submit`
};

const selects = ['provincia','edificio','piso','sala'].reduce((acc,id)=>({...acc,[id]:document.getElementById(id)}),{});
const tableBody = document.getElementById('bensTable');
const followupBox = document.getElementById('followupBox');
const salaNome = document.getElementById('salaNome');

async function fetchOptions(url, select, placeholder='-- Selecione --') {
    try {
        const res = await fetch(url);
        const data = await res.json();
        select.innerHTML = `<option value="">${placeholder}</option>`;
        data.forEach(i => select.innerHTML += `<option value="${i.id}" data-nome="${i.Nome}">${i.Nome}</option>`);
        select.disabled = data.length === 0;
    } catch(err) {
        console.error('Erro ao buscar opções:', err);
        select.innerHTML = `<option value="">Erro ao carregar</option>`;
        select.disabled = true;
    }
}

// Cascata Província → Edifício → Piso → Sala
selects.provincia.onchange = () => {
    fetchOptions(api.edificios(selects.provincia.value), selects.edificio);
    selects.piso.innerHTML = selects.sala.innerHTML = '<option>-- Selecione --</option>';
    selects.piso.disabled = selects.sala.disabled = true;
    followupBox.classList.add('hidden');
};

selects.edificio.onchange = () => {
    fetchOptions(api.pisos(selects.edificio.value), selects.piso);
    selects.sala.innerHTML = '<option>-- Selecione --</option>';
    selects.sala.disabled = true;
    followupBox.classList.add('hidden');
};

selects.piso.onchange = () => fetchOptions(api.salas(selects.piso.value), selects.sala);

selects.sala.onchange = async () => {
    const salaId = selects.sala.value;
    if (!salaId) return followupBox.classList.add('hidden');

    salaNome.textContent = selects.sala.options[selects.sala.selectedIndex].text;

    try {
        const res = await fetch(api.bens(salaId));
        const bens = await res.json();

        if (!bens.length) {
            tableBody.innerHTML = `<tr><td colspan="5" class="p-2 text-center">Nenhum ativo encontrado nesta sala.</td></tr>`;
        } else {
            tableBody.innerHTML = bens.map((b,i) => `
                <tr class="hover:bg-gray-50">
                    <td class="p-2 border text-center">${i+1}</td>
                    <td class="p-2 border">${b.Etiqueta ?? '-'}</td>
                    <td class="p-2 border">${b.Nome ?? '-'}</td>
                    <td class="p-2 border">${b.estadoConservacao ?? '-'}</td>
                    <td class="p-2 border text-center"><input type="checkbox" value="${b.id}" class="bemCheck"></td>
                </tr>
            `).join('');
        }

        followupBox.classList.remove('hidden');
    } catch(err) {
        console.error('Erro ao buscar bens:', err);
        tableBody.innerHTML = `<tr><td colspan="5" class="p-2 text-center text-red-600">Erro ao carregar ativos.</td></tr>`;
    }
};

// Botões Marcar / Desmarcar Todos
document.getElementById('checkAll').onclick = () => document.querySelectorAll('.bemCheck').forEach(c=>c.checked=true);
document.getElementById('uncheckAll').onclick = () => document.querySelectorAll('.bemCheck').forEach(c=>c.checked=false);

// Finalizar FollowUp via AJAX com envio dos nomes da sala, piso, edifício e província
document.getElementById('btnSubmit').onclick = async () => {
    const salaId = selects.sala.value;
    const salaNomeVal = selects.sala.options[selects.sala.selectedIndex].text;
    const pisoNomeVal = selects.piso.options[selects.piso.selectedIndex]?.text ?? '-';
    const edificioNomeVal = selects.edificio.options[selects.edificio.selectedIndex]?.text ?? '-';
    const provinciaNomeVal = selects.provincia.options[selects.provincia.selectedIndex]?.text ?? '-';

    const bens = Array.from(document.querySelectorAll('.bemCheck')).map(c => ({
        id: c.value,
        presente: c.checked ? 1 : 0,
        sala_nome: salaNomeVal,
        piso_nome: pisoNomeVal,
        edificio_nome: edificioNomeVal,
        provincia_nome: provinciaNomeVal
    }));

    const observacao = document.getElementById('observacao').value;

    if (!salaId) return alert('Selecione uma sala.');
    if (!bens.length) return alert('Nenhum ativo disponível.');

    try {
        const res = await fetch(api.submit, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ sala_id: salaId, bens, observacao })
        });

        const data = await res.json();
        if (data.success) {
            window.location.href = `/followup/comparacao/${data.followup_id}`;
        } else {
            alert('Erro ao enviar follow up: ' + (data.error || 'Erro desconhecido'));
        }
    } catch(err) {
        console.error('Erro no submit:', err);
        alert('Erro ao enviar follow up.');
    }
};
</script>
@endsection
