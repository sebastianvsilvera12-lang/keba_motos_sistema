// ==============================================================
// VALIDACIONES EN JAVASCRIPT - KEBA MOTOS
// ==============================================================

document.addEventListener('DOMContentLoaded', function () {
    // 1. Validar solo números en campos DNI y Teléfono
    const inputsDNI = document.querySelectorAll('input[name="dni"], input[name="numero_documento"]');
    inputsDNI.forEach(input => {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 8);
        });
    });

    const inputsTelefono = document.querySelectorAll('input[name="telefono"]');
    inputsTelefono.forEach(input => {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 9);
        });
    });

    // 2. Formato automático de placa en mayúsculas (ej: 1234-5B)
    const inputsPlaca = document.querySelectorAll('input[name="placa"]');
    inputsPlaca.forEach(input => {
        input.addEventListener('input', function () {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9-]/g, '').slice(0, 10);
        });
    });

    // 3. Confirmación antes de eliminar
    const btnsEliminar = document.querySelectorAll('.btn-confirmar-eliminar');
    btnsEliminar.forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm('¿Estás seguro de que deseas eliminar este registro? Esta acción no se puede deshacer.')) {
                e.preventDefault();
            }
        });
    });
});

