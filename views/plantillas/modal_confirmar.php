<!-- Modal Genérico de Confirmación -->
<div id="modalConfirmar" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:10px; max-width:400px; width:90%; padding:25px; box-shadow:0 10px 25px rgba(0,0,0,0.3); text-align:center;">
        <h3 style="color:#0b1329; margin-bottom:10px;">¿Confirmar Acción?</h3>
        <p style="color:#64748b; font-size:0.92rem; margin-bottom:20px;" id="modalConfirmarTexto">¿Estás seguro de que deseas proceder?</p>
        <div style="display:flex; justify-content:center; gap:12px;">
            <button type="button" class="btn btn-outline" onclick="cerrarModal('modalConfirmar')">Cancelar</button>
            <a href="#" id="modalConfirmarBtnAccion" class="btn btn-danger">Confirmar</a>
        </div>
    </div>
</div>

