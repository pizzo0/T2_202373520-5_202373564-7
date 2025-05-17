const loadingContainer = document.getElementById("loading");
const loadingBar = loadingContainer.querySelector(".loading-bar");
let progreso = 0;
let intervalo = null;
let timeoutHide = null;
let timeoutCollapse = null;

const iniciarCarga = () => {
    if (intervalo !== null) return;
    progreso = 0;

    loadingBar.style.width = "0%";
    loadingContainer.style.display = "flex";
    loadingContainer.style.height = "5px";

    intervalo = setInterval(() => {
        if (progreso >= 100) {
            loadingBar.style.width = "100%";
            clearInterval(intervalo);
            intervalo = null;

            timeoutCollapse = setTimeout(() => {
                loadingContainer.style.height = "0px";
            }, 200);
            timeoutHide = setTimeout(() => {
                loadingContainer.style.display = "none";
            }, 500);

            return;
        }

        if (progreso < 50) {
            progreso += 0.5;
        }

        loadingBar.style.width = `${progreso}%`;
    }, 16);
};

const reiniciarCarga = () => {
    if (intervalo !== null) {
        clearInterval(intervalo);
        intervalo = null;
    }
    if (timeoutCollapse !== null) {
        clearTimeout(timeoutCollapse);
        timeoutCollapse = null;
    }
    if (timeoutHide !== null) {
        clearTimeout(timeoutHide);
        timeoutHide = null;
    }
    progreso = 0;
    loadingBar.style.transition = "none";
    loadingBar.style.width = "0%";
    void loadingBar.offsetWidth;
    loadingBar.style.transition = "";
    loadingContainer.style.display = "flex";
    loadingContainer.style.height = "5px";

    iniciarCarga();
};