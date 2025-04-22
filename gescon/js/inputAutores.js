let usuarioActual = null;

document.addEventListener("DOMContentLoaded", () => {
fetch("php/api/actual.data.usuario.php")
    .then((response) => response.json())
    .then((data) => {
    if (!data.error) {
        usuarioActual = data;
    }
    })
    .catch((error) => {
    console.error("Error al obtener el usuario actual:", error);
    });

const emailInput = document.getElementById("nuevo-email");
emailInput.addEventListener("input", () => {
    const errorDiv = emailInput.nextElementSibling;
    if (errorDiv && errorDiv.classList.contains("error")) {
    if (emailInput.value.trim() === "") {
        errorDiv.textContent = "";
        errorDiv.style.display = "none";
    } else {
        errorDiv.style.display = "none";
    }
    }
});
});

function buscarYAgregarAutor() {
const emailInput = document.getElementById("nuevo-email");
const email = emailInput.value.trim().toLowerCase();

let errorDiv = emailInput.nextElementSibling;
if (!errorDiv || !errorDiv.classList.contains("error")) {
    errorDiv = document.createElement("div");
    errorDiv.classList.add("error");
    emailInput.insertAdjacentElement("afterend", errorDiv);
}

if (!email) {
    errorDiv.textContent = "";
    errorDiv.style.display = "none";
    return;
}

if (!emailInput.checkValidity()) {
    errorDiv.textContent = "Debes ingresar un email válido.";
    errorDiv.style.display = "block";
    return;
}

const emailInputs = document.querySelectorAll('input[name="email[]"]');
for (let input of emailInputs) {
    if (input.value.trim().toLowerCase() === email) {
    errorDiv.textContent = "Este autor ya ha sido agregado.";
    errorDiv.style.display = "block";
    return;
    }
}

if (usuarioActual && usuarioActual.email.toLowerCase() === email) {
    errorDiv.textContent = "No puedes agregarte a ti mismo.";
    errorDiv.style.display = "block";
    return;
}

errorDiv.textContent = "";
errorDiv.style.display = "none";

fetch(`php/api/data.usuario.email.php?email=${encodeURIComponent(email)}`)
    .then((response) => response.json())
    .then((data) => {
    if (data.error) {
        errorDiv.textContent = data.error;
        errorDiv.style.display = "block";
    } else {
        const table = document.getElementById("tabla-autores");
        const newRow = table.insertRow(-1);
        const index = table.rows.length - 2;
        
        newRow.classList.add("autor-info");

        newRow.innerHTML = `
            <td><input type="text" name="nombre[]" value="${data.nombre}" readonly></td>
            <td><input type="email" name="email[]" value="${data.email}" readonly></td>
            <td><input type="radio" name="contacto" value="${index}"></td>
            <td><button class="remover" type="button" onclick="eliminarAutor(this)">X</button></td>
        `;
        emailInput.value = "";
    }
    })
    .catch((error) => {
    console.error("Error al buscar el autor:", error);
    errorDiv.textContent = "Hubo un error al buscar el autor.";
    errorDiv.style.display = "block";
    });
}

function eliminarAutor(button) {
    const row = button.closest("tr");
    row.remove();
}