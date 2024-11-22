<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./images/sunz.webp">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/sessions.css">
    <!-- Links para footer -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.1.0/css/all.css">
    <!-- Links para footer -->
    <title>Suns COLAM</title>
</head>

<body>
    <section>
        <nav class=" navbar fixed-top" style="background-color: #FFFFFF; padding: 10px;">
            <div class="container-fluid">
                <a href="./index.php"><img src="./images/sunz.webp" alt="Icono Pandora"
                        style="height: 60px; width: 71px; margin-left: 39px; margin-right: 10px;"></a>
                <div align="right">
                    <a href="./unmoderated.php"
                        class="btn btn-dark btn-rounded"><strong>Unmoderated</strong></a>&nbsp;&nbsp;&nbsp;
                    <a href="./resolution.php"
                        class="btn btn-dark btn-rounded"><strong>Resolution</strong></a>&nbsp;&nbsp;&nbsp;
                </div>
            </div>
        </nav>
        <br><br><br><br><br>
        <!-- Temporizador con entrada de tiempo --><br><br><br><br>
        <div class="container text-center"
            style="position: absolute;top: 63%; left: 50%; transform: translate(-50%, -50%);">
            <p style="font-size: 120px; color:#000000;" id="timer">00:00</p>
            <!-- Entrada para minutos y segundos -->
            <div class="form-inline justify-content-center mb-3">
                <label style="font-size:50px; color:#000000;" for="minutes" class="mr-2">Minutes:</label>
                <input style="font-size:20px; padding-bottom: 10px; text-align: center; width:5%" type=" number"
                    id="minutes" sclass=" form-control" min="0" value="0">
            </div>
            <!-- Botones de control del temporizador -->
            <div>
                <button style="font-weight:bold;" class=" btn btn-primary" onclick="startTimer()">Start</button>
                <button style="font-weight:bold;" class="btn btn-secondary" onclick="pauseTimer()">Pause</button>
                <button style="font-weight:bold;" class="btn btn-success" onclick="resumeTimer()">Resume</button>
                <button style="font-weight:bold;" class="btn btn-danger" onclick="resetTimer()">Restart</button>
            </div>
        </div>
    </section>

    <script>
    let timerInterval;
    let remainingTime = 0;
    let isPaused = true;

    // Función para iniciar el temporizador con el tiempo ingresado
    function startTimer() {
        const minutes = parseInt(document.getElementById("minutes").value) || 0;
        remainingTime = minutes * 60;

        if (remainingTime > 0 && isPaused) {
            isPaused = false;
            updateTimerDisplay();
            timerInterval = setInterval(updateTime, 1000);
        } else {
            alert("Por favor, ingrese un tiempo válido.");
        }
    }


    // Función para pausar el temporizador
    function pauseTimer() {
        clearInterval(timerInterval);
        isPaused = true;
    }

    // Función para reanudar el temporizador
    function resumeTimer() {
        if (isPaused && remainingTime > 0) {
            isPaused = false;
            timerInterval = setInterval(updateTime, 1000);
        }
    }

    // Función para reiniciar el temporizador
    function resetTimer() {
        clearInterval(timerInterval);
        remainingTime = 0;
        isPaused = true;
        updateTimerDisplay();
    }

    // Actualiza el temporizador en la pantalla
    function updateTime() {
        if (remainingTime > 0) {
            remainingTime--;
            updateTimerDisplay();
        } else {
            clearInterval(timerInterval);
            alert("Tiempo terminado");
        }
    }

    function updateTimerDisplay() {
        const minutes = String(Math.floor(remainingTime / 60)).padStart(2, '0');
        const seconds = String(remainingTime % 60).padStart(2, '0');
        document.getElementById('timer').innerText = `${minutes}:${seconds}`;
    }
    </script>

    <footer class="bg-dark text-center text-white">
        <div class="container">
            <div class="row">
                <!-- Columna de redes sociales -->
                <div class="col-6 d-flex justify-content-start">
                    <p>
                        <i class="fas fa-envelope"></i> tecnologia@colam.edu.mx
                    </p>
                </div>
                <!-- Columna de copyright y logo -->
                <div class="col-6 d-flex justify-content-end">
                    <div>
                        <img src="./images/colamlog.webp" alt="Logo"><br>
                        <p>© 2024 COLAM Education SAPI</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>