(function () {

  // Si está desactivado en localStorage, remover elementos y no iniciar nada
  if (localStorage.getItem("cosmix_deriva_activa") === "false") {
    document.getElementById("botella-flotante")?.remove();
    document.getElementById("mensaje-card")?.remove();
    return; // salir de la función
  }

  // Pool de mensajes de ejemplo — en tu proyecto real vendrían de tu backend/BD
  const mensajes = [
    "A veces el silencio dice más que cualquier post que subamos.",
    "¿Alguien más siente que este año pasó volando?",
    "Un desconocido te desea un buen día, sea cual sea tu momento.",
    "Guardá este mensaje como una señal de que no estás solo/a hoy.",
    "La botella llegó hasta vos por una razón: seguí adelante."
  ];

  const botella = document.getElementById('botella-flotante');
  const card = document.getElementById('mensaje-card');
  const texto = document.getElementById('mensaje-texto');
  const btnCerrar = document.getElementById('btn-cerrar');
  const btnDevolver = document.getElementById('btn-devolver');

  function mensajeAleatorio() {
    return mensajes[Math.floor(Math.random() * mensajes.length)];
  }

  function abrirBotella() {
    texto.textContent = mensajeAleatorio();
    card.classList.add('visible');
  }

  function cerrarCard() {
    card.classList.remove('visible');
  }

  botella.addEventListener('click', abrirBotella);
  btnCerrar.addEventListener('click', cerrarCard);
  btnDevolver.addEventListener('click', cerrarCard); 
  // acá podrías, por ej, marcar "descartado" en tu backend

  // Opcional: que la botella "avise" con un pulso cada cierto tiempo,
  // simulando que llegó un mensaje nuevo (sin abrir la tarjeta sola)
  setInterval(() => {
    botella.style.animation = 'none';
    void botella.offsetWidth; // fuerza reinicio de animación
    botella.style.animation = 'flotar 3s ease-in-out infinite, aparecer 0.6s ease-out';
  }, 15000);

})();
