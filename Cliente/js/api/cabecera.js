function cargarCabecera(rutaLogo, rutaPerfil) {
    return `
        <div class="primero">
            <div class="banner">
                <div class="logo">
                    <a href="${rutaPerfil}"><img src="${rutaLogo}" alt="Logo" /></a>
                </div>
                <div class="text-container">
                    <h1 class="title">Perros Perdidos</h1>
                    <p class="slogan">Reconstruyendo familias</p>
                    <p class="project-info">Proyecto de fin de ciclo de DAW de Linkia</p>
                </div>
            </div>
            <audio id="musicPlayer" controls>
                <source src="/perros/ApiPerrosPerdidos/Publico/mp3/relax.mp3" type="audio/mp3">
                Tu navegador no soporta audio HTML.
            </audio>
        </div>
    `;
}

function cargarPieDePagina(rutaLogoPie) {
    return `
        <footer class="footer">
            <div class="footer-content">
                <p>&copy; 2023 Perros Perdidos. Todos los derechos reservados.</p>
            </div>
            <img src="${rutaLogoPie}" alt="Logo" />
        </footer>
    `;
}
