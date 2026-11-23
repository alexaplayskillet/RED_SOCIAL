const API = '../Controllers/ColeccionController.php';
const dlg = document.getElementById('dlg-guardar');
const sel = document.getElementById('sel-coleccion');
let pubActual = null;

document.addEventListener('click', async e => {
  const btn = e.target.closest('.btn-guardar');
  if (!btn) return;

  pubActual = btn.dataset.idPub;
  const json = await (await fetch(`${API}?accion=listar`)).json();
  sel.innerHTML = '';
  json.datos.forEach(c => sel.add(new Option(c.nombre, c.id_tablero)));

  if (json.datos.length === 0) { alert('Primero crea una colección.'); return; }
  dlg.showModal();
});

document.getElementById('btn-cancelar-guardar').addEventListener('click', () => dlg.close());

document.getElementById('btn-confirmar-guardar').addEventListener('click', async () => {
  const body = new URLSearchParams({ accion: 'guardar', id_tablero: sel.value, id_pub: pubActual });
  const json = await (await fetch(API, { method: 'POST', body })).json();
  dlg.close();
  alert(json.ok ? 'Publicación guardada' : json.error);
});