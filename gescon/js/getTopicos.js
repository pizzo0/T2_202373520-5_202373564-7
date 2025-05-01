const topicosContainer = document.querySelector('.topicos-container');

document.addEventListener("DOMContentLoaded", () => {
    fetch("php/api/topicos.php")
        .then((response) => response.json())
        .then((data) => {

            data.sort((a, b) => a.nombre.localeCompare(b.nombre));

            data.forEach((topico) => {
                const topicoDiv = document.createElement("div");
                topicoDiv.className = "topico-preview";

                const topicoNombre = document.createElement("p");
                topicoNombre.textContent = `${topico.id} - ${topico.nombre}`;

                const topicoDeleteBtn = document.createElement("button");
                topicoDeleteBtn.textContent = "Eliminar";
                topicoDeleteBtn.className = "btn-rojo";

                topicoDeleteBtn.addEventListener('click', () => {
                    if(!confirm(`Seguro que quieres eliminar el topico ${topico.nombre}`)) return;

                    fetch("/php/api/topicos.eliminar.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                            id: topico.id
                        })
                    }).then((response) => response.json()).then((res) => {
                        if (res.ok) {
                            topicoDiv.remove();
                        } else {
                            alert("Error al eliminar el topico.");
                        }
                    })
                    .catch((e) => {
                        console.error("ERROR:", e);
                    });
                });

                topicoDiv.appendChild(topicoNombre);
                topicoDiv.appendChild(topicoDeleteBtn);

                topicosContainer.appendChild(topicoDiv);
            });
        })
});