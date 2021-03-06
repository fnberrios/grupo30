<?php
  session_start();
?>
<?php include('../templates/header.html');   ?>
<?php include('../templates/navbar.html'); ?>
<body>
  <?php
    #Llama a conexión, crea el objeto PDO y obtiene la variable $db
    require("../config/conexion.php");
    $users_ = NULL;

    if (isset($_SESSION['user_id'])){
      $query = "SELECT uid,username,correo,contrasena FROM usuarios WHERE uid = :uid ;";
      $result = $db53 -> prepare($query);
      $result->bindParam(':uid', $_SESSION['user_id']);
      $result -> execute();
      $users_ = $result -> fetchAll();

      $query1 = "SELECT reservas.fechainicio, reservas.fechatermino, hoteles.direccionhotel  FROM realiza, reservas,  hoteles WHERE realiza.uid = :uid AND realiza.rid = reservas.rid AND reservas.hid = hoteles.hid;";
      $result1 = $db53 -> prepare($query1);
      $result1->bindParam(':uid', $_SESSION['user_id']); #se relacionan
      $result1 -> execute();
      $reservas = $result1 -> fetchAll();

      $query2 = "SELECT tickets.asiento, tickets.fechacompra, tickets.fechaviaje, destinos.ciudad_origen, ciudades.nombreciudad FROM tickets,destinos,viaje,ciudades WHERE tickets.uid = :uid AND tickets.did = destinos.did AND viaje.did = destinos.did AND viaje.cid = ciudades.cid; ";
      $result2 = $db53 -> prepare($query2);
      $result2->bindParam(':uid', $_SESSION['user_id']); #se relacionan
      $result2 -> execute();
      $tickets = $result2 -> fetchAll();

      $query3 = "SELECT entradas.fecha_actual, lugares.lnombre, lugaresmuseo.hora_apertura, lugaresmuseo.hora_cierre FROM lugares, lugaresmuseo, entradas WHERE entradas.uid = :uid AND entradas.lid = lugaresmuseo.lid AND entradas.lid = lugares.lid;";
      $result3 = $db30 -> prepare($query3);
      $result3->bindParam(':uid', $_SESSION['user_id']); #se relacionan
      $result3 -> execute();
      $entradas = $result3 -> fetchAll();
    }

  ?>
    <h3>Mi Perfil</h3>
    </br>
    <table>
      <tr>
        <th>Uid</th>
        <th>Username</th>
        <th>Correo</th>
        <th>Contraseña</th>
      </tr>
      <?php
      foreach ($users_ as $u) {
        echo "<tr><td>$u[0]</td><td>$u[1]</td><td>$u[2]</td><td>$u[3]</td></tr>";
      }
      ?>
    </table>
    </br>
    <table>
      <tr>
        <th>Fecha de Inicio</th>
        <th>Fecha de termino</th>
        <th>Dirección Hotel</th>
      </tr>
      <?php
      foreach ($reservas as $r) {
        echo "<tr><td>$r[0]</td><td>$r[1]</td><td>$r[2]</td></tr>";
      }
      ?>
    </table>
    </br>
    <table>
      <tr>
        <th>n° asiento</th>
        <th>Fecha de compra</th>
        <th>Fecha del viaje</th>
        <th>Ciudad de origen</th>
        <th>Ciudad de destino</th>
      </tr>
      <?php
      foreach ($tickets as $t){
        echo "<tr><td>$t[0]</td><td>$t[1]</td><td>$t[2]</td><td>$t[3]</td><td>$t[4]</td></tr>";
      }
      ?>
    </table>
  </br>
  <table>
    <tr>
      <th>Fecha de compra</th>
      <th>Museo</th>
      <th>Hora de apertura</th>
      <th>Hora de cierre</th>
    </tr>
    <?php
    foreach ($entradas as $e){
      echo "<tr><td>$e[0]</td><td>$e[1]</td><td>$e[2]</td><td>$e[3]</td></tr>";
    }
    ?>
  </table>

  <a href="consulta_confirmar_eliminar.php">Eliminar</a>
