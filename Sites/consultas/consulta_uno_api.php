<?php
  session_start();
?>
<?php include('../templates/header.html');   ?>
<?php include('../config/call_api.php');   ?>

<body>
    <?php
    #Llama a conexión, crea el objeto PDO y obtiene la variable $db
    require("../config/conexion.php");

    #Se obtiene el id del usuario
    $user = $_SESSION['user_id'];
    $query = "SELECT * FROM usuarios WHERE usuarios.uid=$user;";
    $result = $db53 -> prepare($query);
    $result -> execute();
    $dataCollected = $result -> fetchAll();
    foreach ($dataCollected as $p) {
        echo "<tr> <td>$p[0]</td> <td>$p[1]</td> <td>$p[2]</td> <td>$p[3]</td>
        <td>$p[4]</td> <td>$p[5]</td></tr>";
    }
    $data = CallAPI($GET, 'https://e5db.herokuapp.com/messages');
    echo gettype($data);
    $data = json_decode($data, true);
    echo gettype($data);
    $data_filtrada = array();
    echo gettype($user);
    foreach ($data as $message) {
      echo $message['mid'];
    }

    ?>
    <?php include('../templates/footer.html'); ?>
