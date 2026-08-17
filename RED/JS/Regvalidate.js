document.addEventListener("DOMContentLoaded", () => {
  // Obtener el tamaño actual de fuente desde la variable CSS
  const getFontSize = () =>
    parseFloat(getComputedStyle(document.documentElement)
      .getPropertyValue('--font-size'));

  // Escuchar las teclas arriba y abajo
  document.addEventListener('keydown', (e) => {
    const target = e.target;
    const isTyping = target.tagName === 'INPUT' || target.tagName === 'TEXTAREA' || target.isContentEditable;
    if (isTyping) return;

    let fontSize = getFontSize();

    if (e.key === '+') {
      document.documentElement.style.setProperty('--font-size', `${fontSize * 1.1}em`);
    }

    if (e.key === '-') {
      document.documentElement.style.setProperty('--font-size', `${fontSize * 0.9}em`);
    }
  });

  const form = document.querySelector("form");
  const inputs = document.querySelectorAll(".inp");
  
  /*const fechaInput = document.querySelector('input[name="Fecha"]');
  const passwordInput = document.querySelector('input[name="password"]');
  const password2Input = document.querySelector('input[name="password2"]');
*/
  const fechaInput = document.getElementById("fecha");
  const passwordInput = document.getElementById("password");
  const password2Input = document.getElementById("password2");

  const expresiones = {
    usuario: /^[a-zA-Z0-9_-]{4,16}$/,
    nombre: /^[a-zA-Z\s]{4,}$/,
    password: /^[a-zA-Z0-9_-]{4,16}$/,
    email: /^[a-zA-Z0-9._%+-]{4,}@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
  };

  const campos = {
    usuario: false,
    nombre: false,
    password: false,
    password2: false,
    fecha: false,
    email: false
  };

  const aplicarClase = (input, estado) => {
    input.classList.remove("correcto", "incorrecto");
    input.classList.add(estado ? "correcto" : "incorrecto");
  };

  const validarCampo = (expresion, input, campo) => {
    const validationsDiv = input.nextElementSibling;
    const pls = validationsDiv?.querySelector(".pls");
    const error = validationsDiv?.querySelector(".error");
    const valor = input.value.trim();

    if (valor === "") {
      if (pls) pls.style.display = "block";
      if (error) error.style.display = "none";
      campos[campo] = false;
      aplicarClase(input, false);
    } else if (!expresion.test(valor)) {
      if (pls) pls.style.display = "none";
      if (error) error.style.display = "block";
      campos[campo] = false;
      aplicarClase(input, false);
    } else {
      if (pls) pls.style.display = "none";
      if (error) error.style.display = "none";
      campos[campo] = true;
      aplicarClase(input, true);
    }
  };

  const validarEdad = () => {
    const validationsDiv = fechaInput.nextElementSibling;
    const pls = validationsDiv?.querySelector(".pls");
    const error = validationsDiv?.querySelector(".error");
    const valor = fechaInput.value;

    if (!valor) {
      if (pls) pls.style.display = "block";
      if (error) error.style.display = "none";
      campos.fecha = false;
      aplicarClase(fechaInput, false);
      return;
    }

    const hoy = new Date();
    const nacimiento = new Date(valor);
    const edad = hoy.getFullYear() - nacimiento.getFullYear();
    const cumpleEsteAño = new Date(hoy.getFullYear(), nacimiento.getMonth(), nacimiento.getDate()) > hoy;
    const edadFinal = cumpleEsteAño ? edad - 1 : edad;

    if (edadFinal < 13) {
      if (pls) pls.style.display = "none";
      if (error) error.style.display = "block";
      campos.fecha = false;
      aplicarClase(fechaInput, false);
    } else {
      if (pls) pls.style.display = "none";
      if (error) error.style.display = "none";
      campos.fecha = true;
      aplicarClase(fechaInput, true);
    }
  };

  const validarPassword2 = () => {
    const validationsDiv = password2Input.nextElementSibling;
    const pls = validationsDiv?.querySelector(".pls");
    const error = validationsDiv?.querySelector(".error");
    const notsame = validationsDiv?.querySelector(".notsame");

    const pass1 = passwordInput.value.trim();
    const pass2 = password2Input.value.trim();

    if (pass2 === "") {
      if (pls) pls.style.display = "block";
      if (error) error.style.display = "none";
      if (notsame) notsame.style.display = "none";
      campos.password2 = false;
      aplicarClase(password2Input, false);
    } else if (!expresiones.password.test(pass2)) {
      if (pls) pls.style.display = "none";
      if (error) error.style.display = "block";
      if (notsame) notsame.style.display = "none";
      campos.password2 = false;
      aplicarClase(password2Input, false);
    } else if (pass1 !== pass2) {
      if (pls) pls.style.display = "none";
      if (error) error.style.display = "none";
      if (notsame) notsame.style.display = "block";
      campos.password2 = false;
      aplicarClase(password2Input, false);
    } else {
      if (pls) pls.style.display = "none";
      if (error) error.style.display = "none";
      if (notsame) notsame.style.display = "none";
      campos.password2 = true;
      aplicarClase(password2Input, true);
    }
  };

  inputs.forEach(input => {
    //const campo = input.name;
    const campo = input.id;
    if (campo === "usuario" || campo === "nombre" || campo === "password") {
      input.addEventListener("keyup", () => validarCampo(expresiones[campo], input, campo));
      input.addEventListener("blur", () => validarCampo(expresiones[campo], input, campo));
    }
    if (campo === "password2") {
      input.addEventListener("keyup", validarPassword2);
      input.addEventListener("blur", validarPassword2);
    }
    if (campo === "fecha") {
      input.addEventListener("change", validarEdad);
      input.addEventListener("blur", validarEdad);
    }
    if (campo === "email") {
      input.addEventListener("keyup", () => validarCampo(expresiones.email, input, campo));
      input.addEventListener("blur", () => validarCampo(expresiones.email, input, campo));
    }
  });

  form.addEventListener("submit", e => {
    validarEdad();
    validarPassword2();

    if (!campos.usuario || !campos.nombre || !campos.password || !campos.password2 || !campos.fecha || !campos.email) {
       e.preventDefault();
      alert("Por favor completa correctamente todos los campos.");
    }
  });
});
