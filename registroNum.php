<?php
require_once './conexion.php';
session_start(); // Inicia la sesión

// Almacena el número de países en la sesión cuando se envía la primera solicitud
if (isset($_POST['submit_count'])) {
    $_SESSION['numCountries'] = $_POST['numCountries'];
}

// Recupera el número de países desde la sesión, si está definido
$numCountries = $_SESSION['numCountries'] ?? null;

// Recupera el idComite y commitName de la sesión
$idComite = $_SESSION['idComite'] ?? null; 
$commitName = $_SESSION['commitName'] ?? null; 

if (isset($_POST['submit_register']) && $idComite) {
    $errors = [];

    for ($i = 0; $i < $numCountries; $i++) {
        $country = $_POST['country'][$i];
        $delegate = $_POST['delegate'][$i];

        if (!empty($country) && !empty($delegate)) {
            $sql = $cnnPDO->prepare("INSERT INTO countries (idComite, commitName, country, delegate) VALUES (:idComite, :commitName, :country, :delegate)");
            $sql->bindParam(':idComite', $idComite);
            $sql->bindParam(':commitName', $commitName); // Vincular commitName
            $sql->bindParam(':country', $country);
            $sql->bindParam(':delegate', $delegate);

            if (!$sql->execute()) {
                $errors[] = "Error al registrar el país $country.";
            }
        } else {
            $errors[] = "Completa todos los campos para el país " . ($i + 1) . ".";
        }
    }

    if (empty($errors)) {
        echo "Todos los países se registraron correctamente.";
        // Limpia la sesión para que el formulario vuelva a la etapa inicial
        unset($_SESSION['numCountries']);
    } else {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>"; // Mostrar errores con Bootstrap
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suns COLAM</title>
    <link rel="icon" href="./images/sunz.webp">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Registrar Países</title>
</head>

<body>

    <section>
        <nav class="navbar fixed-top " style="background-color: #FFFFFF; padding: 10px;">
            <div class="container-fluid">
                <a href="index.php"><img src="./images/sunz.webp" alt="Icono Pandora"
                        style="height: 60px; width: 71px; margin-left: 39px; margin-right: 10px;"></a>
                <div align="right">
                    <a href="./debate.php" class="btn btn-dark btn-rounded"><strong>SUNS</strong></a>&nbsp;&nbsp;&nbsp;
                    <a href="./registroC.php"
                        class="btn btn-dark btn-rounded"><strong>Registro</strong></a>&nbsp;&nbsp;&nbsp;
                </div>
            </div>
        </nav>
        <br><br><br><br>

        <!-- Muestra el nombre del comité actual -->
        <h2>Comité: <?php echo htmlspecialchars($commitName); ?></h2>

        <?php if (!isset($numCountries)): ?>
        <!-- Solicita el número de países a registrar -->
        <form action="" method="post">
            <label for="numCountries">Número de países a registrar:</label>
            <input type="number" name="numCountries" id="numCountries" min="1" required>
            <button type="submit" name="submit_count">Enviar</button>
        </form>
        <?php else: ?>
        <!-- Muestra el formulario para registrar los países -->
        <form action="" method="post">
            <input type="hidden" name="numCountries" value="<?php echo htmlspecialchars($numCountries); ?>">

            <?php for ($i = 0; $i < $numCountries; $i++): ?>
            <h3>País <?php echo $i + 1; ?></h3>
            <input type="text" name="country[]" placeholder="Nombre del país" required>
            <input type="text" name="delegate[]" placeholder="Nombre del delegado" required>
            <?php endfor; ?>

            <button type="submit" name="submit_register">Registrar</button>
        </form>
        <?php endif; ?>

    </section>

</body>

</html>