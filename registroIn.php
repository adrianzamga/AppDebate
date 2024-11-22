<?php
require_once './conexion.php';

if (isset($_POST['submit'])) 
{
    $country = $_POST['country'];
    $delegate = $_POST['delegate'];

    if (!empty($country) && !empty($delegate)) {
        $idComite = uniqid();

        $sql = $cnnPDO->prepare("INSERT INTO countries (idComite, country, delegate) VALUES (:idComite, :country, :delegate)");
        
        $sql->bindParam(':idComite', $idComite);
        $sql->bindParam(':country', $country);
        $sql->bindParam(':delegate', $delegate);

        if ($sql->execute()) {
            // Redirige a otra página
            header('Location: ./index.php'); 
            exit; // Termina el script después de la redirección
        } else {
            echo "Error al registrar el comité.";
        }
    } else {
        echo "Por favor, completa todos los campos.";
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
    <link rel="stylesheet" href="./styles/registro.css">
</head>

<body
    style="background-image: url(./images/); background-size: cover; background-repeat: no-repeat; padding: top 70px;">
    <section>
        <nav class="navbar fixed-top" style="background-color: #FFFFFF; padding: 10px;">
            <div class="container-fluid">
                <a href="./index.php"><img src="./images/sunz.webp" alt="Icono Pandora"
                        style="height: 60px; width: 71px; margin-left: 39px; margin-right: 10px;"></a>

                <div align="right">
                    <a href="./debate.php" class="btn btn-dark btn-rounded"><strong>SUNS</strong></a>&nbsp;&nbsp;&nbsp;
                    <a href="./registro.php"
                        class="btn btn-dark btn-rounded"><strong>Registro</strong></a>&nbsp;&nbsp;&nbsp;
                </div>
            </div>
        </nav>
    </section>

    <section class="form-main">
        <div class="form-content">
            <div class="box">
                <form action="" method="post">
                    <h3>Welcome</h3>
                    <div class="input-box">
                        <input type="text" name="country" placeholder="Provide the country's name"
                            class="input-control" />
                    </div>
                    <div class="input-box">
                        <input type="text" name="delegate" placeholder="Provide the delegate's name"
                            class="input-control" />
                    </div>

                    <div>
                        <button type="submit" name="submit" class="btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</body>

</html>