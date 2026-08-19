document.addEventListener("DOMContentLoaded", () => {
  // ============================
  // VALIDACIÓN FORMULARIO IDENTIDAD
  // ============================
  const formIdentidad = document.querySelector(".form-identidad");
  if (formIdentidad) {
    const inputsIdentidad = formIdentidad.querySelectorAll(".inp");
    const regexUsuario = /^[a-zA-Z0-9_]{4,}$/;
    const estadoIdentidad = {
      usuario: false,
      arrobaUser: false
    };

    const aplicarClase = (input, estado) => {
      input.classList.remove("correcto", "incorrecto");
      input.classList.add(estado ? "correcto" : "incorrecto");
    };

    const validarIdentidad = (input) => {
      const campo = input.name === "@user" ? "arrobaUser" : "usuario";
      const valor = input.value.trim();
      const validationsDiv = input.nextElementSibling;
      const pls = validationsDiv?.querySelector(".pls");
      const error = validationsDiv?.querySelector(".error");

      if (valor === "") {
        pls?.style.setProperty("display", "block");
        error?.style.setProperty("display", "none");
        estadoIdentidad[campo] = false;
        aplicarClase(input, false);
      } else if (!regexUsuario.test(valor)) {
        pls?.style.setProperty("display", "none");
        error?.style.setProperty("display", "block");
        estadoIdentidad[campo] = false;
        aplicarClase(input, false);
      } else {
        pls?.style.setProperty("display", "none");
        error?.style.setProperty("display", "none");
        estadoIdentidad[campo] = true;
        aplicarClase(input, true);
      }
    };

    inputsIdentidad.forEach(input => {
      input.addEventListener("keyup", () => validarIdentidad(input));
      input.addEventListener("blur", () => validarIdentidad(input));
    });

    formIdentidad.addEventListener("submit", e => {
      inputsIdentidad.forEach(validarIdentidad);
      if (!estadoIdentidad.usuario || !estadoIdentidad.arrobaUser) {
        e.preventDefault();
        alert("Por favor completa correctamente ambos campos.");
      }
    });
  }

  // ============================
  // VALIDACIÓN FORMULARIO CONFIGURACIÓN
  // ============================
  const formConfig = document.querySelector(".form-configuracion");
  if (formConfig) {
    const inputsConfig = formConfig.querySelectorAll(".inp");
    const fechaInput = formConfig.querySelector('input[name="Fecha"]');
    const passwordInput = formConfig.querySelector('input[name="password"]');
    const password2Input = formConfig.querySelector('input[name="password2"]');

    const expresiones = {
      nombre: /^[a-zA-Z\s]{4,}$/,
      password: /^[a-zA-Z0-9_-]{4,16}$/,
      email: /^[a-zA-Z0-9._%+-]{4,}@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
    };

    const campos = {
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
        pls?.style.setProperty("display", "block");
        error?.style.setProperty("display", "none");
        campos[campo] = false;
        aplicarClase(input, false);
      } else if (!expresion.test(valor)) {
        pls?.style.setProperty("display", "none");
        error?.style.setProperty("display", "block");
        campos[campo] = false;
        aplicarClase(input, false);
      } else {
        pls?.style.setProperty("display", "none");
        error?.style.setProperty("display", "none");
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
        pls?.style.setProperty("display", "block");
        error?.style.setProperty("display", "none");
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
        pls?.style.setProperty("display", "none");
        error?.style.setProperty("display", "block");
        campos.fecha = false;
        aplicarClase(fechaInput, false);
      } else {
        pls?.style.setProperty("display", "none");
        error?.style.setProperty("display", "none");
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
        pls?.style.setProperty("display", "block");
        error?.style.setProperty("display", "none");
        notsame?.style.setProperty("display", "none");
        campos.password2 = false;
        aplicarClase(password2Input, false);
      } else if (!expresiones.password.test(pass2)) {
        pls?.style.setProperty("display", "none");
        error?.style.setProperty("display", "block");
        notsame?.style.setProperty("display", "none");
        campos.password2 = false;
        aplicarClase(password2Input, false);
      } else if (pass1 !== pass2) {
        pls?.style.setProperty("display", "none");
        error?.style.setProperty("display", "none");
        notsame?.style.setProperty("display", "block");
        campos.password2 = false;
        aplicarClase(password2Input, false);
      } else {
        pls?.style.setProperty("display", "none");
        error?.style.setProperty("display", "none");
        notsame?.style.setProperty("display", "none");
        campos.password2 = true;
        aplicarClase(password2Input, true);
      }
    };

    inputsConfig.forEach(input => {
      const campo = input.name;
      if (campo === "nombre" || campo === "password") {
        input.addEventListener("keyup", () => validarCampo(expresiones[campo], input, campo));
        input.addEventListener("blur", () => validarCampo(expresiones[campo], input, campo));
      }
      if (campo === "password2") {
        input.addEventListener("keyup", validarPassword2);
        input.addEventListener("blur", validarPassword2);
      }
      if (campo === "Fecha") {
        input.addEventListener("change", validarEdad);
        input.addEventListener("blur", validarEdad);
      }
      if (campo === "email") {
        input.addEventListener("keyup", () => validarCampo(expresiones.email, input, campo));
        input.addEventListener("blur", () => validarCampo(expresiones.email, input, campo));
      }
    });

    formConfig.addEventListener("submit", e => {
      validarEdad();
      validarPassword2();

      if (!campos.nombre || !campos.password || !campos.password2 || !campos.fecha || !campos.email) {
        e.preventDefault();
        alert("Por favor completa correctamente todos los campos.");
      }
    });
  }
});