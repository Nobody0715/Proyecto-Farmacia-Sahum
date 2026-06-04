// Validación de formularios
function validarCantidad() {
    const cant = document.getElementById('cantidad');
    if (cant.value <= 0) {
        alert("La cantidad debe ser mayor a 0");
        return false;
    }
    return true;
}