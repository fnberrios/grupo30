<?php
   session_start();
?>

<?php include('../templates/header.html');   ?>
<?php include('../config/call_api.php');   ?>
<?php include('../templates/navbar.html'); ?>
<body>
    <?php
    #Llama a conexión, crea el objeto PDO y obtiene la variable $db
    require("../config/conexion.php");

    #Tenemos el id del usuario
    $user = $_SESSION['user_id'];
    ?>

    <h3> Que mensajes deseas buscar:</h3>
    <p>(Si desea filtrar por mas de una palabra debe ingresarlas separadas por espacio)</p>
    <form action="consulta_cuatro_api_2.php" method="post">
        </br>
        Deseado: <input type="text" name="desired">
        </br>
        Requerido: <input type="text" name="required">
        </br>
        Prohibido: <input type="text" name="forbidden">
        </br>
        UserId: <input type="text" name="userId">
    </br>
    </br>
    <input type = "submit" value = "Buscar">
    </form>

<?php include('../templates/footer.html'); ?>
