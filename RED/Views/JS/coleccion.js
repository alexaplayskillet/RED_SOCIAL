  const modal = document.getElementById("modalColeccion");
            const abrirModal = document.getElementById("abrirModal");
            const cerrarModal = document.getElementById("cerrarModal");
            const cancelarModal = document.getElementById("cancelarModal");

            abrirModal.addEventListener("click", function() {
                modal.classList.add("mostrar");
            });

            cerrarModal.addEventListener("click", function() {
                modal.classList.remove("mostrar");
            });

            cancelarModal.addEventListener("click", function() {
                modal.classList.remove("mostrar");
            });


            modal.addEventListener("click", function(event) {

                if (event.target === modal) {
                    modal.classList.remove("mostrar");
                }

            });


            const emojis = document.querySelectorAll(".emoji-opcion");

            let emojiSeleccionado = "📁";

            emojis.forEach(function(emoji) {

                emoji.addEventListener("click", function() {

                    emojis.forEach(function(item) {
                        item.classList.remove("seleccionado");
                    });

                    emoji.classList.add("seleccionado");

                    emojiSeleccionado = emoji.textContent;

                });

            });


            document.getElementById("crearColeccion").addEventListener("click", function() {

                const nombre = document
                    .getElementById("nombreColeccion")
                    .value
                    .trim();

                if (nombre === "") {
                    return;
                }


                const lista = document.getElementById("listaColecciones");


                const nuevaColeccion = document.createElement("div");

                nuevaColeccion.className = "coleccion-menu";


                nuevaColeccion.innerHTML = `

                    <div class="mini-coleccion nueva">

                        <span>${emojiSeleccionado}</span>

                    </div>

                    <div class="info-menu-coleccion">

                        <span>${nombre}</span>

                        <small>Solo yo</small>

                    </div>

                `;


                lista.appendChild(nuevaColeccion);


                document.getElementById("nombreColeccion").value = "";

                modal.classList.remove("mostrar");

            });


            const colecciones =
                document.querySelectorAll(".coleccion-menu");


            colecciones.forEach(function(coleccion) {

                coleccion.addEventListener("click", function() {

                    colecciones.forEach(function(item) {
                        item.classList.remove("activo");
                    });

                    coleccion.classList.add("activo");

                });

            });