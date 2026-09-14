(function () {

    
    if (localStorage.getItem("cosmix_contador_activo") === "false") {
        return;
    }

    const inicio = Date.now();
    const elemento = document.getElementById("contador-valor");

    function formatear(segundosTotales) {
        const horas = Math.floor(segundosTotales / 3600);
        const minutos = Math.floor((segundosTotales % 3600) / 60);
        const segundos = segundosTotales % 60;

        const pad = (n) => String(n).padStart(2, "0");
        return `${pad(horas)}:${pad(minutos)}:${pad(segundos)}`;
    }

    function actualizar() {
        const transcurrido = Math.floor((Date.now() - inicio) / 1000);
        if (elemento) {
            elemento.textContent = formatear(transcurrido);
        }
    }

    actualizar();
    setInterval(actualizar, 1000);

})();


//Filtros de publis
(function () {

    const boton = document.getElementById("filtrosBtn");
    const menu = document.getElementById("filtrosMenu");
    const opciones = document.querySelectorAll(".filtro-opcion");

    if (!boton || !menu) {
        return;
    }

    boton.addEventListener("click", function (evento) {

        evento.stopPropagation();

        menu.classList.toggle("active");
        boton.classList.toggle("active");

    });

    opciones.forEach(function (opcion) {

        opcion.addEventListener("click", function () {

            opciones.forEach(function (o) {
                o.classList.remove("active");
            });

            opcion.classList.add("active");

            const filtroElegido = opcion.getAttribute("data-filtro");

            aplicarFiltro(filtroElegido);

            menu.classList.remove("active");
            boton.classList.remove("active");

        });

    });

    document.addEventListener("click", function (evento) {

        if (!menu.contains(evento.target) && !boton.contains(evento.target)) {
            menu.classList.remove("active");
            boton.classList.remove("active");
        }

    });

    function aplicarFiltro(tipo) {

        //Logica para filtrar publis

        console.log("Filtro aplicado:", tipo);

    }

})();