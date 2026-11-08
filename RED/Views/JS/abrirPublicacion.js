(function () {
    const boton = document.getElementById('btnAgregarFotos');
    const ventana = document.querySelector('.crear-publi');

    if (!boton || !ventana) return;

    function abrir() {
        ventana.classList.add('active');
        const texto = ventana.querySelector('textarea');
        if (texto) texto.focus();
    }

    function cerrar() {
        ventana.classList.remove('active');
    }

    boton.addEventListener('click', function (e) {
        e.preventDefault();
        abrir();
    });

    ventana.querySelectorAll('.cerrar-publi').forEach(function (b) {
        b.addEventListener('click', cerrar);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && ventana.classList.contains('active')) cerrar();
    });
})();