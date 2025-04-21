document.addEventListener("DOMContentLoaded", () => {
    fetch("php/api/topicos.php")
        .then((response) => response.json())
        .then((data) => {
            const dropdownMenu = document.getElementById("dropdown-menu");
            const dropdownButton = document.getElementById("dropdown-button");

            data.sort((a, b) => a.nombre.localeCompare(b.nombre));

            data.forEach((topico) => {
                const item = document.createElement("div");
                item.classList.add("dropdown-item");
                item.textContent = topico.nombre;
                item.setAttribute("data-id", topico.id);
                dropdownMenu.appendChild(item);
            });

            dropdownButton.addEventListener("click", function () {
                dropdownMenu.classList.toggle("show");
            });

            dropdownMenu.addEventListener("click", function (e) {
                if (e.target.classList.contains("dropdown-item")) {
                    const selectedTopic = e.target;
                    const topicId = selectedTopic.getAttribute("data-id");
                    const topicName = selectedTopic.textContent;

                    if (isTopicAlreadySelected(topicId)) {
                        return;
                    }

                    const topicDiv = document.createElement("div");
                    topicDiv.classList.add("selected-topic");
                    topicDiv.textContent = topicName;
                    topicDiv.setAttribute("data-id", topicId);

                    const container = document.getElementById("topicos-container");
                    container.appendChild(topicDiv);

                    const existingError = container.querySelector(".error-message");
                    if (existingError) {
                        existingError.remove();
                    }

                    const hiddenTopics = document.getElementById("hidden-topics");
                    const selectedIds = hiddenTopics.value
                        ? hiddenTopics.value.split(",")
                        : [];
                    selectedIds.push(topicId);
                    hiddenTopics.value = selectedIds.join(",");

                    dropdownMenu.classList.remove("show");
                }
            });

            document.getElementById("topicos-container").addEventListener("click", function (e) {
                if (e.target.classList.contains("selected-topic")) {
                    const topicDiv = e.target;
                    const topicId = topicDiv.getAttribute("data-id");

                    topicDiv.remove();

                    const hiddenTopicsInput = document.getElementById("hidden-topics");
                    const updatedIds = hiddenTopicsInput.value
                        .split(",")
                        .filter((id) => id !== topicId);
                    hiddenTopicsInput.value = updatedIds.join(",");
                }
            });
        })
        .catch((error) => console.error("Error al cargar los tópicos:", error));
});

document.getElementById("form").addEventListener("submit", function (e) {
    const hiddenTopics = document.getElementById("hidden-topics").value;
    const container = document.getElementById("topicos-container");

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

function isTopicAlreadySelected(topicId) {
    const hiddenTopics = document.getElementById("hidden-topics").value.split(",");
    return hiddenTopics.includes(topicId);
}