<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.7.5/axios.min.js"></script>
<script>
    // --- Alterna campos de número de série ---
    const tipoSelect = document.getElementById('tipoNumeroSerie');
    const manualDiv = document.getElementById('numeroSerieManualDiv');
    const scannerDiv = document.getElementById('numeroScannerDiv');

    tipoSelect.addEventListener('change', function() {
        if (this.value === 'NumeroSerieManual') {
            manualDiv.style.display = 'block';
            scannerDiv.style.display = 'none';
        } else {
            manualDiv.style.display = 'none';
            scannerDiv.style.display = 'flex';
        }
    });

    // --- Modal câmera ---
    const cameraModal = document.getElementById('cameraModal');
    const reader = document.getElementById('reader');
    const fecharCamera = document.getElementById('fecharCamera');
    let html5QrCode = null;
    let inputTarget = null;

    document.querySelectorAll('.abrirCameraBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            inputTarget = document.getElementById(btn.dataset.target);
            cameraModal.classList.remove('opacity-0','pointer-events-none');
            cameraModal.classList.add('opacity-100','pointer-events-auto');

            html5QrCode = new Html5Qrcode("reader");
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    html5QrCode.start(
                        { facingMode: "environment" },
                        { fps: 10, qrbox: 250 },
                        (decodedText) => {
                            inputTarget.value = decodedText;
                            html5QrCode.stop();
                            cameraModal.classList.add('opacity-0','pointer-events-none');
                            cameraModal.classList.remove('opacity-100','pointer-events-auto');
                        },
                        (errorMessage) => {}
                    );
                }
            }).catch(err => console.error(err));
        });
    });

    fecharCamera.addEventListener('click', () => {
        if(html5QrCode) html5QrCode.stop();
        cameraModal.classList.add('opacity-0','pointer-events-none');
        cameraModal.classList.remove('opacity-100','pointer-events-auto');
    });

    cameraModal.addEventListener('click', e => {
        if(e.target === cameraModal) {
            if(html5QrCode) html5QrCode.stop();
            cameraModal.classList.add('opacity-0','pointer-events-none');
            cameraModal.classList.remove('opacity-100','pointer-events-auto');
        }
    });

    // --- AJAX Dinâmico ---
    const provinciaSelect = document.getElementById('provincia');
    const edificioSelect = document.getElementById('edificio');
    const pisoSelect = document.getElementById('piso');
    const grupoSelectField = document.getElementById('grupo');
    const categoriaSelect = document.getElementById('categoria');
    const subcategoriaSelect = document.getElementById('subcategoria');

    provinciaSelect.addEventListener('change', function() {
        axios.get(`/api/edificios/${this.value}`).then(res => {
            edificioSelect.innerHTML = '<option value="">Selecione</option>';
            res.data.forEach(e => edificioSelect.innerHTML += `<option value="${e.EdificioId}">${e.Nome}</option>`);
            pisoSelect.innerHTML = '<option value="">Selecione</option>';
        });
    });

    edificioSelect.addEventListener('change', function() {
        axios.get(`/api/pisos/${this.value}`).then(res => {
            pisoSelect.innerHTML = '<option value="">Selecione</option>';
            res.data.forEach(p => pisoSelect.innerHTML += `<option value="${p.PisoId}">${p.Nome}</option>`);
        });
    });

    grupoSelectField.addEventListener('change', function() {
        axios.get(`/api/categorias/${this.value}`).then(res => {
            categoriaSelect.innerHTML = '<option value="">Selecione</option>';
            res.data.forEach(c => categoriaSelect.innerHTML += `<option value="${c.CategoriaId}">${c.Nome}</option>`);
            atualizarCamposCapacidade();
        });
    });

    categoriaSelect.addEventListener('change', function() {
        axios.get(`/api/subcategorias/${this.value}`).then(res => {
            subcategoriaSelect.innerHTML = '<option value="">Selecione</option>';
            res.data.forEach(sc => subcategoriaSelect.innerHTML += `<option value="${sc.SubcategoriaId}" data-grupo="${sc.GrupoId}" data-categoria="${sc.CategoriaId}">${sc.Nome}</option>`);
        });
    });

    // --- Capacidade e Potência ---
    const capacidadeDiv = document.querySelector('input[name="Capacidade"]').parentElement;
    const potenciaDiv = document.querySelector('input[name="Potencia"]').parentElement;
    function atualizarCamposCapacidade() {
        const grupoSelecionado = grupoSelectField.options[grupoSelectField.selectedIndex].text.toUpperCase();
        if(['EQUIPAMENTOS', 'MATERIAL', 'MOBILIARIO'].includes(grupoSelecionado)) {
            capacidadeDiv.style.display = 'block';
            potenciaDiv.style.display = 'block';
        } else {
            capacidadeDiv.style.display = 'none';
            potenciaDiv.style.display = 'none';
        }
    }
    atualizarCamposCapacidade();

    // --- Subcategoria selecionada preenche grupo e categoria ---
    subcategoriaSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if(selected.value) {
            const grupoId = selected.getAttribute('data-grupo');
            const categoriaId = selected.getAttribute('data-categoria');
            atualizarCamposCapacidade();

            axios.get(`/api/categorias/${grupoId}`).then(res => {
                categoriaSelect.innerHTML = '<option value="">Selecione</option>';
                res.data.forEach(c => {
                    const sel = c.CategoriaId == categoriaId ? 'selected' : '';
                    categoriaSelect.innerHTML += `<option value="${c.CategoriaId}" ${sel}>${c.Nome}</option>`;
                });
            });
        }
    });

    // --- Carrega todas subcategorias inicialmente ---
    axios.get('/api/subcategorias-todas').then(res => {
        subcategoriaSelect.innerHTML = '<option value="">Selecione</option>';
        res.data.forEach(sc => subcategoriaSelect.innerHTML += `<option value="${sc.SubcategoriaId}" data-grupo="${sc.GrupoId}" data-categoria="${sc.CategoriaId}">${sc.Nome}</option>`);
    });

    document.getElementById('etiqueta').addEventListener('blur', async function() {
    const etiqueta = this.value.trim();
    if (!etiqueta) return;

    const response = await fetch(`/verificar-etiqueta?etiqueta=${etiqueta}`);
    const data = await response.json();

    if (data.existe) {
        alert("⚠️ Esta etiqueta já está cadastrada!");
        this.focus();
        this.classList.add('border-red-500');
    } else {
        this.classList.remove('border-red-500');
    }
});


    document.getElementById('piso').addEventListener('change', function() {
    const pisoId = this.value;
    const salaSelect = document.getElementById('sala');

    salaSelect.innerHTML = '<option value="">Carregando...</option>';

    if (pisoId) {
        fetch(`/salas-por-piso/${pisoId}`)
            .then(response => response.json())
            .then(data => {
                let options = '<option value="">Selecione</option>';
                data.forEach(sala => {
                    options += `<option value="${sala.SalaId}">${sala.Nome}</option>`;
                });
                salaSelect.innerHTML = options;
            })
            .catch(error => {
                console.error('Erro ao carregar salas:', error);
                salaSelect.innerHTML = '<option value="">Erro ao carregar</option>';
            });
    } else {
        salaSelect.innerHTML = '<option value="">Selecione um piso primeiro</option>';
    }
});
</script>
