// Función para esperar a que un elemento esté presente en el DOM
function waitForElement(selector, timeout = 3000) {
    return new Promise((resolve, reject) => {
        const interval = 100;
        let elapsed = 0;

        const check = () => {
            const element = document.querySelector(selector);
            if (element) {
                resolve(element);
            } else if (elapsed >= timeout) {
                reject(new Error(`Elemento "${selector}" no encontrado después de ${timeout}ms`));
            } else {
                elapsed += interval;
                setTimeout(check, interval);
            }
        };

        check();
    });
}

// Función para verificar si ya se seleccionó un tópico
function isTopicAlreadySelected(topicId) {
    const hiddenTopics = document.getElementById("hidden-topics").value.split(",");
    return hiddenTopics.includes(topicId);
}

document.addEventListener("DOMContentLoaded", async () => {
    try {
        const dropdownMenu = await waitForElement("#dropdown-menu");
        const dropdownButton = await waitForElement("#dropdown-button");
        const container = await waitForElement("#topicos-container");
        const hiddenTopicsInput = await waitForElement("#hidden-topics");

        const response = await fetch("/php/api/topicos.php");
        const data = await response.json();

        data.sort((a, b) => a.nombre.localeCompare(b.nombre));

        data.forEach((topico) => {
            const item = document.createElement("div");
            item.classList.add("dropdown-item");
            item.textContent = topico.nombre;
            item.setAttribute("data-id", topico.id);
            dropdownMenu.appendChild(item);
        });

        dropdownButton.addEventListener("click", () => {
            dropdownMenu.classList.toggle("show");
        });

        dropdownMenu.addEventListener("click", (e) => {
            if (e.target.classList.contains("dropdown-item")) {
                const topicId = e.target.getAttribute("data-id");
                const topicName = e.target.textContent;

                if (isTopicAlreadySelected(topicId)) return;

                const topicDiv = document.createElement("div");
                topicDiv.classList.add("selected-topic");
                topicDiv.textContent = topicName;
                topicDiv.setAttribute("data-id", topicId);

                container.appendChild(topicDiv);

                const existingError = container.querySelector(".error-message");
                if (existingError) existingError.remove();

                const selectedIds = hiddenTopicsInput.value
                    ? hiddenTopicsInput.value.split(",")
                    : [];
                selectedIds.push(topicId);
                hiddenTopicsInput.value = selectedIds.join(",");

                dropdownMenu.classList.remove("show");
            }
        });

        container.addEventListener("click", (e) => {
            if (e.target.classList.contains("selected-topic")) {
                const topicId = e.target.getAttribute("data-id");
                e.target.remove();

                const updatedIds = hiddenTopicsInput.value
                    .split(",")
                    .filter((id) => id !== topicId);
                hiddenTopicsInput.value = updatedIds.join(",");
            }
        });

        document.querySelector("form, #form-modificar-revisor").addEventListener("submit", function (e) {
            const hiddenTopics = hiddenTopicsInput.value;

            const existingError = container.querySelector(".error-message");
            if (existingError) {
                existingError.remove();
            }

            if (!hiddenTopics || hiddenTopics.trim() === "") {
                e.preventDefault();

                const errorMsg = document.createElement("p");
                errorMsg.textContent = "Debes seleccionar al menos un tópico.";
                errorMsg.classList.add("error-message");
                container.appendChild(errorMsg);
            }
        });

    } catch (error) {
        console.error("Error al inicializar el selector de tópicos:", error);
    }
});