document.querySelectorAll('.post[data-id-pub]').forEach(post => {
    post.addEventListener('click', e => {
        if (e.target.closest('.post-actions, .caja_com, a, button, input')) return;
        window.location.href = 'verPublicacion.php?id=' + post.dataset.idPub;
    });
});