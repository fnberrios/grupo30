<?php
  session_start();
?>
<?php include('../templates/header.html');   ?>

<body>
<?php
   require("../config/conexion.php");
   $lugar = $_GET["lugar"];
?>
  <form align="center" action="consulta_comprar_entrada.php?lugar=<?php$lugar?>" method="post">
  	<p>¿Seguro quieres comprar esta entrada?</p>
  	<p>
    <input type="submit" name="Respuesta" value="Si">
    <input type="submit" name="Respuesta" value="No">
    </p>
  </form>


<?php include('../templates/footer.html'); ?>
