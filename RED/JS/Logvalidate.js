//colocar lo de la validacion

const campos = {
    usuario: false,
    contra: false
}

//expresiones regulares

const expresiones={
    usuario: /^[a-zA-Z0-9\_\-]{4,16}$/, // AQUI LE ESTAMOS DICIENDO QUE EN EL CAMPO USUARIO ACEPTE LETRAS MINUSCULAS Y MAYUSCULAS DE LA A HASTA LA Z, NÚMEROS DEL 0 HASTA EL 9, GUIONES BAJOS, GUIONES MEDIO Y UNA CANTIDAD MINIMA DE 4 CARACTERES Y MAXIMA DE 16 CARACTERES
    contra: /^[a-zA-Z0-9\_\-]{4,16}$/, // AQUI LE ESTAMOS DICIENDO QUE EN EL CAMPO USUARIO ACEPTE LETRAS MINUSCULAS Y MAYUSCULAS DE LA A HASTA LA Z, NÚMEROS DEL 0 HASTA EL 9, GUIONES BAJOS, GUIONES MEDIO Y UNA CANTIDAD MINIMA DE 4 CARACTERES Y MAXIMA DE 16 CARACTERES

}

// --------- SWITCH PARA SELECCIONAR EL INPUT DONDE ÉSTE HACIENDO FOCO EL USUARIO  ---------------
const validarFormulario = (e) => {
    switch(e.target.name) {
        case "usuario":
            validarCampo(expresiones.usuario, e.target, "usuario");
        break;
        case "password":
            validarCampo(expresiones.password, e.target, "password");
            validarPassword2();
        break;
    }
}

document.addEventListener("DOMContentLoaded", () => {
  const getFontSize = () =>
    parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--font-size'));

  const setFontSize = (size) => {
    document.documentElement.style.setProperty('--font-size', `${size}em`);
  };

  document.addEventListener("keydown", (e) => {
    const isTyping = e.target.tagName === "INPUT" || e.target.tagName === "TEXTAREA" || e.target.isContentEditable;
    if (isTyping) return;

    let currentSize = getFontSize();

    if (e.key === "+" || e.key === "ArrowUp") {
      setFontSize(currentSize * 1.1);
    }

    if (e.key === "-" || e.key === "ArrowDown") {
      setFontSize(currentSize * 0.9);
    }
  });


  const form = document.querySelector("form");
  const inputs = document.querySelectorAll(".inp");

  const expresiones = {
    usuario: /^[a-zA-Z0-9_-]{4,16}$/,
    password: /^[a-zA-Z0-9_-]{4,16}$/
  };

  const campos = {
    usuario: false,
    password: false
  };

  const validarCampo = (expresion, input, campo) => {
    const validationsDiv = input.nextElementSibling;
    const pls = validationsDiv?.querySelector(".pls");
    const error = validationsDiv?.querySelector(".error");

    const valor = input.value.trim();

    if (valor === "") {
      input.classList.remove("correcto", "incorrecto");
      if (pls) pls.style.display = "block";
      if (error) error.style.display = "none";
      campos[campo] = false;
    } else if (!expresion.test(valor)) {
      input.classList.add("incorrecto");
      input.classList.remove("correcto");
      if (pls) pls.style.display = "none";
      if (error) error.style.display = "block";
      campos[campo] = false;
    } else {
      input.classList.add("correcto");
      input.classList.remove("incorrecto");
      if (pls) pls.style.display = "none";
      if (error) error.style.display = "none";
      campos[campo] = true;
    }
  };

  inputs.forEach(input => {
    /*const name = input.placeholder.toLowerCase();*/
    const name = input.id;
    input.setAttribute("name", name);
    input.addEventListener("keyup", validarFormulario);
    input.addEventListener("blur", validarFormulario);
  });

  function validarFormulario(e) {
    const campo = e.target.name;
    if (campo === "user") {
      validarCampo(expresiones.usuario, e.target, "usuario");
    } else if (campo === "password") {
      validarCampo(expresiones.password, e.target, "password");
    }
  }

  form.addEventListener("submit", e => {
    inputs.forEach(input => validarFormulario({ target: input }));
    if (!campos.usuario || !campos.password) {
      e.preventDefault();
      alert("Por favor completa correctamente los campos.");
    }
  });
});