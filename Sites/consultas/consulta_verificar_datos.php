<?php
  session_start();
?>
<?php include('../templates/header.html');   ?>

<body>
<?php
  #Llama a conexión, crea el objeto PDO y obtiene la variable $db
  require("../config/conexion.php");

  $username_ = $_POST["username_"];
  $contrasena_ = $_POST["contrasena_"];

  #Se construye la consulta como un string
  $query = "SELECT uid,username,correo,contrasena FROM usuarios WHERE username = '$username_' and contrasena = '$contrasena_';";

	$result = $db53-> prepare($query);
	$result -> execute();
	$users = $result -> fetchAll();
  $filas = count($users);
  ?>
      <?php
        if ($username_ == NULL && $contrasena_ == NULL):
          echo "Usted no ha ingresado los datos. Porfavor vuelva e ingreselos";

        elseif ($username_ == NULL):
          echo "No ha ingresado su username";

        elseif ($contrasena_ == NULL):
          echo "No ha ingresado su contraseña";

        elseif ($filas == 1):
          foreach ($users as $u) {
            $uid = $u[0];
        }
          $_SESSION['user_id'] = $uid;
          $a = $_SESSION['user_id'];
          $_SESSION['eliminado'] = NULL;
          $_SESSION['entrada'] = NULL;
          $_SESSION['reserva'] = NULL;
      ?>
          <?php include('../templates/navbar.html'); ?>
          <h3 align="center"> Has ingresado con exito a tu cuenta</h3>
          <form align="center" action="../consultas/consulta_perfil.php" method="post">
            <input type="submit" value="Ver Perfil">
          </form>
          </br>
          <form action="consulta_confirmar_eliminar.php" method="post">
            <input type="submit" value="Eliminar cuenta">
          </form>
          </br>
          <form action="../index.php" method="post">
            <input type="submit" value="Ir al Inicio">
          </form>
          </br>
          <form action="../cosulta_uno_api.php" method="post">
            <input type="submit" value="Mostrar todos los atributos de tus mensajes recibidos">
          </form>
          </br>
          <form action="../cosulta_dos_api.php" method="post">
            <input type="submit" value="Mostrar todos los atributos de tus mensajes recibidos">
          </form>
          </br>
          <form action="../cosulta_tres_api.php" method="post">
            <input type="submit" value="Enviar mensaje a un usuario">
          </form>
          </br>
          <form action="../cosulta_cuatro_api.php" method="post">
            <input type="submit" value="Buscar Mensaje">
          </form>
          </br>
          <form action="../cosulta_cinco_api.php" method="post">
            <input type="submit" value="Ir al Inicio">
          </form>
          
        
      <?php
        else:
          echo "La contraseña o el usuario es incorrecto";

      endif;?>
