const form = document.querySelector("form");
  const inputs = document.querySelectorAll(".inp");

  const expresiones = {
    usuario: /^[a-zA-Z0-9_-]{4,16}$/,
    tag: /^[a-zA-Z0-9_-]{4,16}$/
  };

  const campos = {
    usuario: false,
    tag: false
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
    } else if (campo === "tag") {
      validarCampo(expresiones.tag, e.target, "tag");
    }
  }

    form.addEventListener("submit", e => {
      e.preventDefault(); // Siempre prevenimos el envío nativo del form

      inputs.forEach(input => validarFormulario({ target: input }));

      if (!campos.usuario || !campos.tag) {
          alert("Por favor completa correctamente los campos.");
      } else {
          // Todo válido -> redirigir al index
          window.location.href = "config.html";
      }
  });
