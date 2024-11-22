
let committees = [];
let currentCommitteeIndex = 0;
let timeRemaining;
let timerInterval;
let countryCounter = 1;
let spokenCountries = new Set();
let questions = []; // Array para almacenar preguntas

document.addEventListener("keydown", function(event) {
    if (event.key === "Enter") {
        event.preventDefault(); // Evita la acción del Enter
    }
});

function displayQuestionFollowUpCounts() {
    // Obtener los valores ingresados para Questions y Follow-Ups
    const questionCount = document.getElementById("questionCount").value;
    const followUpCount = document.getElementById("followUpCount").value;

    // Seleccionar el contenedor donde se mostrarán los valores
    const questionsList = document.getElementById("questionsList");

    // Limpiar el contenido previo
    questionsList.innerHTML = "";

    // Crear y agregar elementos para mostrar las cantidades ingresadas
    const questionCountItem = document.createElement("p");
    questionCountItem.textContent = `Questions: ${questionCount}`;
    questionsList.appendChild(questionCountItem);

    const followUpCountItem = document.createElement("p");
    followUpCountItem.textContent = `Follow-Ups: ${followUpCount}`;
    questionsList.appendChild(followUpCountItem);
}


// Función para agregar una nueva pregunta
function addQuestion() {
    const questionInput = document.getElementById("questionInput");
    const questionText = questionInput.value.trim();

    if (questionText) {
        questions.push(questionText); // Añadir la pregunta al array
        displayQuestions(); // Actualizar la visualización de preguntas
        questionInput.value = ""; // Limpiar el campo de entrada
    } else {
        Swal.fire({
            title: 'Error',
            text: 'Please enter a valid question.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
}

// Función para mostrar las preguntas en el contenedor
function displayQuestions() {
    const questionsList = document.getElementById("questionsList");
    questionsList.innerHTML = ""; // Limpiar el contenido previo

    questions.forEach((question, index) => {
        const questionItem = document.createElement("div");
        questionItem.classList.add("question-item");

        const questionText = document.createElement("span");
        questionText.textContent = `${index + 1}. ${question}`;
        questionItem.appendChild(questionText);

        // Botón para editar
        const editButton = document.createElement("button");
        editButton.textContent = "Edit";
        editButton.className = "btn btn-sm btn-primary ml-2";
        editButton.onclick = () => editQuestion(index);
        questionItem.appendChild(editButton);

        // Botón para eliminar
        const deleteButton = document.createElement("button");
        deleteButton.textContent = "Delete";
        deleteButton.className = "btn btn-sm btn-danger ml-2";
        deleteButton.onclick = () => deleteQuestion(index);
        questionItem.appendChild(deleteButton);

        questionsList.appendChild(questionItem);
    });
}

function editQuestion(index) {
    const newQuestion = prompt("Edit your question:", questions[index]);
    if (newQuestion !== null && newQuestion.trim() !== "") {
        questions[index] = newQuestion.trim(); // Actualizar la pregunta
        displayQuestions(); // Actualizar la visualización
    } else {
        Swal.fire({
            title: 'Error',
            text: 'The question cannot be empty.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
}

function showCommitteeCountries() {
    const selectedCommittee = document.getElementById("committeeSelect").value;
    const tableBody = document.getElementById("countryTableBody");
    tableBody.innerHTML = ''; // Limpiar la tabla

    if (comites[selectedCommittee]) {
        comites[selectedCommittee].forEach(country => {
            const row = document.createElement("tr");

            // Celda para el nombre del país
            const countryCell = document.createElement("td");
            countryCell.textContent = country;
            row.appendChild(countryCell);

            // Crear botón de activación/desactivación
            const toggleButton = document.createElement("button");
            toggleButton.textContent = spokenCountries.has(country) ? "Activate" :
                "Desactivate";
            toggleButton.className = "btn btn-sm " + (spokenCountries.has(country) ?
                "btn-success" :
                "btn-danger");
            // Asignar función para activar/desactivar al botón
            toggleButton.onclick = () => toggleCountry(country, row, toggleButton);
            // Celda para el botón
            const buttonCell = document.createElement("td");
            buttonCell.appendChild(toggleButton);
            row.appendChild(buttonCell);
            // Resaltar si el país ya ha hablado
            if (spokenCountries.has(country)) {
                row.classList.add('spoken');
            }

            tableBody.appendChild(row);
        });

        document.getElementById("countryTableContainer").style.display = "block";
        document.getElementById("questionsContainer").style.display =
            "block"; // Mostrar contenedor de preguntas

        // Habilitar arrastre en la tabla sin afectar la activación/desactivación
        new Sortable(tableBody, {
            animation: 150,
            ghostClass: 'blue-background-class'
        });
    } else {
        document.getElementById("countryTableContainer").style.display = "none";
        document.getElementById("questionsContainer").style.display =
            "none"; // Ocultar contenedor de preguntas
    }
}
// Función para alternar el estado del país y actualizar el botón
function toggleCountry(country, row, toggleButton) {
    const isSpoken = spokenCountries.has(country);
    if (isSpoken) {
        spokenCountries.delete(country); // Remover el país de la lista
        row.classList.remove('spoken');
        toggleButton.textContent = "Desactivate";
        toggleButton.className = "btn btn-sm btn-danger";
    } else {
        spokenCountries.add(country); // Agregar el país a la lista
        row.classList.add('spoken');
        toggleButton.textContent = "Activate";
        toggleButton.className = "btn btn-sm btn-success";
    }
}

function addSpokenCountryRow(country, counter) {
    const speakersTableBody = document.getElementById("speakersTableBody");
    const speakerRow = document.createElement("tr");
    speakerRow.innerHTML = `<td>${counter}</td><td>${country}</td>`;
    speakersTableBody.appendChild(speakerRow);
}

function updateNextCountries() {
    const nextSpeakersTableBody = document.getElementById("nextSpeakersTableBody");
    nextSpeakersTableBody.innerHTML = ""; // Limpiar la tabla

    for (let i = currentCommitteeIndex + 1; i < committees.length; i++) {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${committees[i].order}</td>
            <td>${committees[i].country}</td>
        `;
        nextSpeakersTableBody.appendChild(row);
    }
}

function startDebate() {
    // Validaciones
    const questionCount = document.getElementById("questionCount").value;
    const followUpCount = document.getElementById("followUpCount").value;
    const globalTime = parseInt(document.getElementById("globalTime").value, 10);

    // Verificar si questions y follow-ups están disponibles 
    if (!globalTime || globalTime <= 0 || !questionCount || questionCount <= 0 || !followUpCount || followUpCount <= 0) {
        Swal.fire({
            title: 'Error',
            text: 'Please enter valid values for time, questions, and follow-ups.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return; // Detener futuras ejecuciones si falla validación
    }

    const committeeSelect = document.getElementById("committeeSelect");
    const selectedCommittee = committeeSelect.options[committeeSelect.selectedIndex].text;

    // Mostrar seleccioness en la interfaz
    document.getElementById("questionsCountDisplay").textContent = `Questions: ${questionCount}`;
    document.getElementById("followUpsCountDisplay").textContent = `Follow-ups: ${followUpCount}`;

    // Esconder después de iniciar
    document.getElementById("committeeLabel").style.display = "none";
    committeeSelect.style.display = "none";
    document.getElementById("globalTimeLabel").style.display = "none";
    document.getElementById("globalTime").style.display = "none";
    document.getElementById("questionLabel").style.display = "none";
    document.getElementById("questionCount").style.display = "none";
    document.getElementById("followUpLabel").style.display = "none";
    document.getElementById("followUpCount").style.display = "none";

    // Mostrar nombre de comité
    const committeeText = document.createElement("h2");
committeeText.id = "committeeText";
committeeText.textContent = `Committee: ${selectedCommittee}`;
committeeText.style.marginTop = "20px";
committeeText.style.color = "black"; // Cambiar el color a negro
document.getElementById("setupForm").appendChild(committeeText);

    // Iniciar configuración de debate
    document.getElementById("committeeSelect").disabled = true;
    const rows = document.querySelectorAll("#countryTableBody tr");
    committees = [];

    // Comités con países que no se han pronunciado
    rows.forEach((row, index) => {
        const country = row.cells[0].textContent;
        const isSpoken = spokenCountries.has(country);
        if (!isSpoken) {
            committees.push({
                country,
                time: globalTime * 60, // Time in seconds
                order: index + 1
            });
        }
    });

    if (committees.length > 0) {
        document.getElementById("countryTableContainer").style.display = "none";
        document.getElementById("timerContainer").style.display = "flex";
        currentCommitteeIndex = 0;
        updateCommitteeLabel();
        updateNextCountries();
        startTimer(globalTime * 60); // Iniciar timer con tiempo especificado
    } else {
        Swal.fire({
            title: 'Error',
            text: 'No countries are available for the debate.',
            icon: 'error'
        });
    }
}

function updateCommitteeLabel() {
    const currentCommittee = committees[currentCommitteeIndex];
    const committeeLabel = document.getElementById("currentCommittee");

    committeeLabel.textContent = `Speaking: ${currentCommittee.country}`;
    committeeLabel.classList.add("highlight"); // Aplica la clase
}



function updateRemainingTime() {
    const newGlobalTime = parseInt(document.getElementById("newGlobalTime").value,
        10) * 60; // Convertir a segundos

    if (isNaN(newGlobalTime) || newGlobalTime <= 0) {
        Swal.fire({
            title: 'Error',
            text: 'Please, set a valid time in minutes.',
            icon: 'error'
        });
        return;
    }
    // Actualiza el tiempo de todos los países que aún no han hablado
    for (let i = currentCommitteeIndex; i < committees.length; i++) {
        committees[i].time = newGlobalTime;
    }
    // Si el temporizador está corriendo para el país actual, se reincia con tiempo nuevo
    if (currentCommitteeIndex < committees.length) {
        timeRemaining = newGlobalTime;
        updateTimerDisplay();
    }

    Swal.fire({
        title: 'Updated time',
        text: `Time has been updated to ${newGlobalTime / 60} minutes for the remaining countries.`,
        icon: 'success'
    });
}

// Iniciar el temporizador para el país actual
function startTimer(seconds) {
    clearInterval(timerInterval); // Limpiar cualquier temporizador previo
    timeRemaining = seconds; // Configurar el tiempo restante
    updateTimerDisplay();

    // Iniciar el temporizador
    timerInterval = setInterval(() => {
        timeRemaining--;
        updateTimerDisplay();
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            handleTimeUp(); // Llamada a la alerta sin avance automático
        }
    }, 1000);
}

function handleTimeUp() {
    const currentCountry = committees[currentCommitteeIndex].country;

    // Añade el país actual a la tabla de países hablados y configúralo como hablado
    addSpokenCountryRow(currentCountry, countryCounter++); // Agregar a tabla de países que hablaron
    spokenCountries.add(currentCountry); // Marca el país como hablado

    Swal.fire({
        title: 'Finished time',
        text: `Time is up for ${currentCountry}.`,
        icon: 'info'
    }).then(() => {
        // Avanzar a siguiente país si hay más
        if (currentCommitteeIndex < committees.length - 1) {
            currentCommitteeIndex++;
            updateCommitteeLabel();
            updateNextCountries();
            startTimer(committees[currentCommitteeIndex].time);
        } else {
            Swal.fire({
                title: 'Debate concluded',
                text: 'The debate has concluded for all delegates.',
                icon: 'success'
            });
            document.getElementById("timerScreen").style.display = "none";
            document.getElementById("nextSpeakersContainer").style.display = "none";
        }
    });
}

function updateTimerDisplay() {
    const minutes = Math.floor(timeRemaining / 60);
    const seconds = timeRemaining % 60;
    document.getElementById("timeDisplay").textContent =
        `${minutes}:${seconds.toString().padStart(2, '0')}`;
}

function pauseTimer() {
    clearInterval(timerInterval);
}

function resumeTimer() {
    startTimer(timeRemaining); // Reanudar el temporizador con el tiempo restante
}

function resetTimer() {
    timeRemaining = committees[currentCommitteeIndex].time;
    updateTimerDisplay();
}
// Avanzar al siguiente país en el debate y marcar el país actual como hablado
function nextCommittee() {
    Swal.fire({
        title: '¿Are you sure?',
        text: '¿Would you like to proceed to the next delegate?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, proceed',
        cancelButtonText: 'Cancel',
        didOpen: () => {
            document.querySelector('.swal2-container').addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    event.stopPropagation();
                }
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Se detiene el temporizador actual
            clearInterval(timerInterval);

            const currentCountry = committees[currentCommitteeIndex].country;
            addSpokenCountryRow(currentCountry, countryCounter++); // Agrega el país a la lista de países que han hablado
            spokenCountries.add(currentCountry); // Marca el país como hablado en el conjunto

            if (currentCommitteeIndex < committees.length - 1) {
                currentCommitteeIndex++;
                updateCommitteeLabel();
                updateNextCountries();
                startTimer(committees[currentCommitteeIndex].time); // Inicia el temporizador para el siguiente país
            } else {
                Swal.fire({
                    title: 'Debate concluded',
                    text: 'The debate has concluded for all delegates.',
                    icon: 'success'
                });
                document.getElementById("timerScreen").style.display = "none";
                document.getElementById("nextSpeakersContainer").style.display = "none";
            }
        }
    });
}

function updateNextCountries() {
    const nextSpeakersTableBody = document.getElementById("nextSpeakersTableBody");
    nextSpeakersTableBody.innerHTML = ''; // Limpiar la tabla

    // Mostrar los próximos dos países en la lista de espera
    for (let i = currentCommitteeIndex + 1; i < committees.length && i <= currentCommitteeIndex +
        2; i++) {
        const nextRow = document.createElement("tr");
        nextRow.innerHTML = `<td>${committees[i].order}</td><td>${committees[i].country}</td>`;
        nextSpeakersTableBody.appendChild(nextRow);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("updateQuestionsButton").addEventListener("click", () => {
        const newQuestionCount = parseInt(document.getElementById(
            "updateQuestionCount").value, 10);
        if (isNaN(newQuestionCount) || newQuestionCount < 0) {
            Swal.fire({
                title: 'Error',
                text: 'Please enter a valid number for questions.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Actualizar el valor mostrado
        document.getElementById("questionsCountDisplay").textContent =
            `Questions: ${newQuestionCount}`;

        Swal.fire({
            title: 'Questions Updated',
            text: `Number of questions updated to ${newQuestionCount}.`,
            icon: 'success',
            confirmButtonText: 'OK'
        });
    });
});
document.getElementById("updateFollowUpsButton").addEventListener("click", () => {
    const newFollowUpCount = parseInt(document.getElementById("updateFollowUpCount").value,
        10);
    if (isNaN(newFollowUpCount) || newFollowUpCount < 0) {
        Swal.fire({
            title: 'Error',
            text: 'Please enter a valid number for follow-ups.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Actualizar el valor mostrado
    document.getElementById("followUpsCountDisplay").textContent =
        `Follow-Ups: ${newFollowUpCount}`;

    Swal.fire({
        title: 'Follow-Ups Updated',
        text: `Number of follow-ups updated to ${newFollowUpCount}.`,
        icon: 'success',
        confirmButtonText: 'OK'
    });
});
