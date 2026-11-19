document.addEventListener("DOMContentLoaded", () => {
  const MIN = 0.7, MAX = 2;
  const root = document.documentElement;

  let escala = parseFloat(localStorage.getItem("cosmix-fuente")) || 1;
  aplicar(escala);

  function aplicar(valor) {
    escala = Math.min(MAX, Math.max(MIN, valor));
    root.style.setProperty("--font-size", `${escala}em`);
    localStorage.setItem("cosmix-fuente", escala);
  }

  document.addEventListener("keydown", (e) => {
    const t = e.target;
    if (t.tagName === "INPUT" || t.tagName === "TEXTAREA" || t.isContentEditable) return;

    if (e.key === "+" || e.key === "ArrowUp") aplicar(escala * 1.1);
    if (e.key === "-" || e.key === "ArrowDown") aplicar(escala * 0.9);
  });
});