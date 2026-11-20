document.addEventListener("DOMContentLoaded", () =>{
    //Añadir cambio de clases entre botones
    document.querySelectorAll(".like_btn").forEach((btn) => {
        const icon = btn.querySelector(".like");

        btn.addEventListener("click", () => {
            icon.classList.toggle("ion-md-heart-empty");
            icon.classList.toggle("ion-md-heart");
        btn.classList.toggle("active");
        icon.classList.add("animate");

        setTimeout(() => {
            icon.classList.remove("animate");
        }, 200);
        });
  });

  document.querySelectorAll(".comment_btn").forEach((btn) => {
        const icon = btn.querySelector(".comment");

        btn.addEventListener("click", () => {
    
        btn.classList.toggle("active");
        icon.classList.add("animate");

        setTimeout(() => {
            icon.classList.remove("animate");
        }, 200);
        });
  });

   document.querySelectorAll(".compartir_btn").forEach((btn) => {
        const icon = btn.querySelector(".share");

        btn.addEventListener("click", () => {
    
        btn.classList.toggle("active");
        icon.classList.add("animate");

        setTimeout(() => {
            icon.classList.remove("animate");
        }, 200);
        });
  });

   document.querySelectorAll(".guardar_btn").forEach((btn) => {
        const icon = btn.querySelector(".save");

        btn.addEventListener("click", () => {
    
        btn.classList.toggle("active");
        icon.classList.add("animate");

        setTimeout(() => {
            icon.classList.remove("animate");
        }, 200);
        });
  });
})