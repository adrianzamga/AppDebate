        <?php
        require_once './conexion.php';
        // Obtener comités y sus países
        $comites = [];
        $sql = $cnnPDO->query("SELECT commitName, country FROM countries");
        if ($sql)   {
            $results = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($results as $row) {
                $comites[$row['commitName']][] = $row['country'];
            }
        }
        // Función para actualizar la sesión del debate en la base de datos 
        function updateDebateSession($idComite, $currentCountry, $timeRemaining, $pdo) {
            $sql = "INSERT INTO debate_sessions (idComite, currentCountry, timeRemaining) 
                    VALUES (:idComite, :currentCountry, :timeRemaining)
                    ON DUPLICATE KEY UPDATE
                        currentCountry = :currentCountry,
                        timeRemaining = :timeRemaining,
                        lastUpdated = CURRENT_TIMESTAMP";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':idComite' => $idComite,
                ':currentCountry' => $currentCountry,
                ':timeRemaining' => $timeRemaining
            ]);
        }
        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>SUNS COLAM</title>
            <link rel="icon" href="./images/sunz.webp">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
                integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
                crossorigin="anonymous">
            <link rel="stylesheet" href="./styles/debate.css">
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.title = "Suns COLAM";
            });
            document.addEventListener("keydown", function(event) {
                if (event.key === "Enter") {
                    event.preventDefault(); // Bloquea tecla enter
                    event.stopPropagation();
                }
            });
            </script>

        </head>

        <body>
            <div class="wrapper">
                <section>
                    <nav class="navbar fixed-top" style="background-color: #FFFFFF; padding: 10px;">
                        <div class="container-fluid">
                            <a href="./index.php"><img src="./images/sunz.webp" alt="Icono Pandora"
                                    style="height: 60px; width: 71px; margin-left: 39px; margin-right: 10px;"></a>
                            <div align="right">
                                <a href="./debate.php"
                                    class="btn btn-dark btn-rounded"><strong>SUNS</strong></a>&nbsp;&nbsp;&nbsp;
                                <!-- <a href="./registroC.php" class="btn btn-dark btn-rounded">
                                <strong>Registro</strong></a>&nbsp;&nbsp;&nbsp; -->
                                <a href="./moderated.php"
                                    class="btn btn-dark btn-rounded"><strong>Moderated</strong></a>&nbsp;&nbsp;&nbsp;
                            </div>
                        </div>
                    </nav>
                    <br><br><br><br>
                    <h1 style="color:black;">Speakers List</h1>
                    <!-- Formulario para configurar los comités -->
                    <form id="setupForm">
                        <label id="committeeLabel" style="font-size: 20px; margin-right: 10px; color:black;"
                            for="committeeSelect">Which
                            committee is participating?</label>
                        <select style="background: #ffffff; border: none; width: 9%;" id="committeeSelect"
                            name="committeeSelect" onchange="showCommitteeCountries()" required>
                            <option value="" style="color:black;">Select a Committee</option>
                            <?php foreach (array_keys($comites) as $comite): ?>
                            <option value="<?php echo htmlspecialchars($comite); ?>">
                                <?php echo htmlspecialchars($comite); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <!-- Entrada para el tiempo global -->
                        <label id="globalTimeLabel" style="font-size: 20px; margin-left: 20px; color:black;"
                            for="globalTime">Set time
                            for
                            all countries (minutes):</label>
                        <!-- Inputs en general -->
                        <input type="number" style="width: 5%;" id="globalTime" min="1" placeholder="Minutes" required>
                        <label id="questionLabel"
                            style="font-size: 20px; margin-left: 20px; color:black;">Questions:</label>
                        <input type="number" style="width: 3%;" id="questionCount" min="1" placeholder="Set" required>
                        <label id="followUpLabel"
                            style="font-size: 20px; margin-left: 20px; color:black;">Follow-Ups:</label>
                        <input type="number" style="width: 3%;" id="followUpCount" min="1" placeholder="Set" required>
                        <!-- Fin inputs -->
                    </form>
                    <!-- Despliegue tabla de cómites -->
                    <div id="countryTableContainer" style="display: none;">
                        <h2>Countries in the selected committee</h2>
                        <table id="countryTable" class="table table-dark">
                            <thead>
                                <tr>
                                    <th>Country</th>
                                    <th>Activate/Desactivate</th>
                                </tr>
                            </thead>
                            <tbody id="countryTableBody"></tbody>
                        </table>
                        <button type="button" onclick="startDebate()" class="btn btn-primary mt-3">
                            Start Debate
                        </button> <br><br>
                    </div>
                    <!-- Empieza div para tiempo y tablas -->
                    <div id="timerContainer" style="display: none;">
                        <div id="speakersContainer">
                            <div class="title">Countries that have spoken</div>
                            <table class="table table-dark">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Country</th>
                                    </tr>
                                </thead>
                                <tbody id="speakersTableBody"></tbody>
                            </table>
                        </div>
                        <!-- Temporizador -->
                        <div id="timerScreen">
                            <div class="committee-label" id="currentCommittee">Tiene la palabra: </div>
                            <div class="timer" id="timeDisplay" style="color:black;">00:00</div>
                            <div class="d-flex justify-content-center mt-3">
                                <button type="button" class="btn btn-outline-primary mr-2" style="width: 100px;"
                                    onclick="pauseTimer()">Pause</button>
                                <button type="button" class="btn btn-outline-success mr-2"
                                    onclick="resumeTimer()">Resume</button>
                                <button type="button" class="btn btn-outline-warning mr-2"
                                    onclick="resetTimer()">Restart</button>
                                <button type="button" class="btn btn-outline-danger ml-2" onclick="nextCommittee()"
                                    style="width: 100px;">Next</button>
                            </div>
                            <br><br>
                            <!--Campo de entrada para cambiar el tiempo restante -->
                            <p for="newGlobalTime" style="font-size: 20px; color:black;">Update time for remaining
                                countries:
                            <div class="input-container spacing">
                                </p>
                                <div class="input-container">
                                    <input type="number" id="newGlobalTime" min="1" placeholder="Set a new time">
                                </div>
                                <button type="button" style=" width: 90%;"
                                    onclick="updateRemainingTime(), displayQuestionFollowUpCounts()"
                                    class="btn btn-outline-info">Update Time</button>
                            </div>
                            <!-- Tabla de países siguientes -->
                        </div>
                        <div id="nextSpeakersContainer">
                            <div class="title">Following Countries</div>
                            <table class="table table-dark">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Country</th>
                                    </tr>
                                </thead>
                                <tbody id="nextSpeakersTableBody"></tbody>
                            </table>
                            <!-- Aquí se mostrarán las preguntas -->
                            <div id="questionsContainer" class="mt-3"
                                style="display: none; margin-top: 30px; text-align:center;">
                                <h1>Agenda</h1>
                                <div id="agendaDetails" style="margin-top: 20px;">
                                    <div>
                                        <p style="font-size: 35px;" id="questionsCountDisplay"></p>
                                        <p style="font-size: 35px;" id="followUpsCountDisplay"></p>
                                    </div>
                                    <!-- Inputs y botones para actualizar -->
                                    <div style="margin-top: 20px;">
                                        <label for="updateQuestionCount" style="font-size: 20px;">Update
                                            Questions:</label>
                                        <input id="updateQuestionCount" style="width: 50%; margin:auto;" type="number"
                                            class="form-control mb-2" min="0" placeholder="New number of questions">
                                        <button id="updateQuestionsButton"
                                            class="btn btn-success mb-3">Update</button><br>

                                        <label for="updateFollowUpCount" style="font-size: 20px;">Update
                                            Follow-Ups:</label>
                                        <input id="updateFollowUpCount" style="width: 50%; margin: auto;" type="number"
                                            class="form-control mb-2" min="0" placeholder="New number of follow-ups">
                                        <br>
                                        <button id="updateFollowUpsButton" class="btn btn-success">Update</button>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                    <script>
                    const comites = <?php echo json_encode($comites); ?>;
                    </script>
                    <script src="app.js"></script>

                </section>
                <footer class="bg-dark text-center text-white">
                    <div class="container">
                        <div class="row">
                            <!-- Correo -->
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
            </div>
        </body>

        </html>