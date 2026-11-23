(function () {
    const URL_REPORTES = '../Controllers/ReportesController.php';

    const boton = document.getElementById('btnGenerarReporte');
    const selector = document.getElementById('tipoReporte');
    const placeholder = document.getElementById('reportePlaceholder');
    const resultado = document.getElementById('reporteResultado');

    if (!boton || !selector || !resultado) return;

    function mostrarMensaje(texto) {
        resultado.innerHTML = '';
        const p = document.createElement('p');
        p.className = 'reporte-mensaje';
        p.textContent = texto;
        resultado.appendChild(p);
    }

    function pintar(tipo, datos) {
        resultado.innerHTML = '';

        const cabecera = document.createElement('div');
        cabecera.className = 'reporte-cabecera';

        const titulo = document.createElement('h3');
        titulo.className = 'reporte-titulo';
        titulo.textContent = datos.titulo;
        cabecera.appendChild(titulo);

        const csv = document.createElement('a');
        csv.className = 'btn reporte-csv';
        csv.href = URL_REPORTES + '?tipo=' + encodeURIComponent(tipo) + '&formato=csv';
        csv.innerHTML = '<i class="ion-md-download"></i> Descargar CSV';
        cabecera.appendChild(csv);

        resultado.appendChild(cabecera);

        if (!datos.filas.length) {
            const vacio = document.createElement('p');
            vacio.className = 'reporte-mensaje';
            vacio.textContent = 'No hay registros este mes.';
            resultado.appendChild(vacio);
            return;
        }

        const envoltura = document.createElement('div');
        envoltura.className = 'reporte-tabla-wrap';

        const tabla = document.createElement('table');
        tabla.className = 'reporte-tabla';

        const encabezado = document.createElement('thead');
        const filaEnc = document.createElement('tr');
        datos.columnas.forEach(function (col) {
            const th = document.createElement('th');
            th.textContent = col;
            filaEnc.appendChild(th);
        });
        encabezado.appendChild(filaEnc);
        tabla.appendChild(encabezado);

        const cuerpo = document.createElement('tbody');
        datos.filas.forEach(function (fila) {
            const tr = document.createElement('tr');
            fila.forEach(function (celda) {
                const td = document.createElement('td');
                td.textContent = celda;
                tr.appendChild(td);
            });
            cuerpo.appendChild(tr);
        });
        tabla.appendChild(cuerpo);

        envoltura.appendChild(tabla);
        resultado.appendChild(envoltura);
    }

    boton.addEventListener('click', function () {
        const tipo = selector.value;

        boton.disabled = true;
        if (placeholder) placeholder.style.display = 'none';

        fetch(URL_REPORTES + '?tipo=' + encodeURIComponent(tipo) + '&formato=json')
            .then(function (r) {
                if (r.status === 401) {
                    window.location.href = 'login.php';
                    return null;
                }
                return r.json();
            })
            .then(function (datos) {
                if (!datos) return;
                if (datos.ok) {
                    pintar(tipo, datos);
                } else {
                    mostrarMensaje(datos.mensaje || 'No se pudo generar el reporte.');
                }
            })
            .catch(function () { mostrarMensaje('Error de conexión. Intenta de nuevo.'); })
            .then(function () { boton.disabled = false; });
    });
})();
