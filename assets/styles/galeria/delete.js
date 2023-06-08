import Swal from "sweetalert2";

const eliminar = document.getElementById('eliminar');
const id = eliminar.getAttribute("data");

eliminar.addEventListener("click", () => {
    Swal.fire({
        title: '¿QUIERES ELIMINAR LA IMAGEN?',
        text: "No puedes recuperarla si la eliminar",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ELIMINAR'
      }).then( async (result) => {
        if (result.isConfirmed) {
            await fetch('/galeria/delete/'+id)
            .then(window.location = "/galeria");

        }
      }).catch(console.log())
})