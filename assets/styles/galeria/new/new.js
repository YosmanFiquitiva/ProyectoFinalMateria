import './new.scss'
let image = document.getElementById("image");
let input = document.getElementById("cargaArchivo");


input.onchange = (e)=>{
    if(e.target.files[0]){
        const url = URL.createObjectURL(e.target.files[0]);
        image.style.backgroundImage = 'url('+url+')';
    }
}