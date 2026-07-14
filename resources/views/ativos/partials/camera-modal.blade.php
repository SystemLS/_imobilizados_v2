{{-- Modal da câmera --}}
<div id="cameraModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 z-50">
    <div class="bg-white rounded-xl shadow-xl p-4 w-96 relative">
        <button id="fecharCamera" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-xl font-bold">&times;</button>
        <h2 class="text-xl font-semibold mb-2">Posicione o QR/Barcode</h2>
        <div id="reader" style="width:100%; height:300px;"></div>
    </div>
</div>

<style>
    /* Modal Estilizado */
    #cameraModal {
        transition: opacity 0.3s ease;
        z-index: 50;
        display: flex;
    }
    #cameraModal.opacity-0 {
        opacity: 0;
        pointer-events: none;
    }
    #cameraModal.opacity-100 {
        opacity: 1;
        pointer-events: auto;
    }
    #reader {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
</style>
