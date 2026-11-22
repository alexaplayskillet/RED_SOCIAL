document.querySelectorAll('.post[data-id-pub]').forEach(post => {
    post.addEventListener('click', e => {
        if (e.target.closest('.post-actions, .caja_com, .form-comentario, .lista-comentarios, .post-img, .post-video, .post-menu, a, button, input')) return;
        window.location.href = 'verPublicacion.php?id=' + post.dataset.idPub;
    });
});