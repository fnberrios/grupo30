<?php
  session_start();
?>
<?php include('../templates/header.html');   ?>
<?php include('../templates/navbar.html'); ?>
<body>
    <?php
    #Llama a conexión, crea el objeto PDO y obtiene la variable $db
    require("../config/conexion.php");

    $nom = $_GET["lugar"];
    $query = "SELECT Lugares.lnombre, Ciudades.cnombre, Ciudades.cpais,
    Lugares.tipo, LugaresMuseo.hora_apertura, LugaresMuseo.hora_cierre,
    LugaresIglesia.hora_apertura, LugaresIglesia.hora_cierre,
    LugaresMuseo.precio FROM Lugares INNER JOIN Ciudades ON Lugares.cid=Ciudades.cid
    LEFT JOIN LugaresIglesia ON LugaresIglesia.lid = Lugares.lid LEFT JOIN
    LugaresMuseo ON LugaresMuseo.lid=Lugares.lid WHERE Lugares.lid='$nom';";
    $result = $db30->prepare($query);
    $result->execute();
    $dataCollected = $result->fetchAll();
    ?>

    <table>
        <tr>
            <th>Nombre del Lugar</th>
            <th>Ciudad</th>
            <th>Pais</th>
            <th>Tipo</th>
            <th>Hora Apertura Museo</th>
            <th>Hora Cierre Museo</th>
            <th>Hora Apertura Iglesia</th>
            <th>Hora Cierre Iglesia</th>
            <th>Precio</th>

        </tr>
        <?php
        foreach ($dataCollected as $p) {
            echo "<tr> <td>$p[0]</td> <td>$p[1]</td> <td>$p[2]</td> <td>$p[3]</td>
            <td>$p[4]</td> <td>$p[5]</td> <td>$p[6]</td>
            <td>$p[7]</td> <td>$p[8]</td>
            <td>$p[9]</td></tr>";
        }
        ?>
    </table>

    <!------------ Buscar Obras que hay en este lugar --------------->
    <?php
    #Llama a conexión, crea el objeto PDO y obtiene la variable $db
    $query = "SELECT Artistas.anombre, Obras.onombre, Obras.oid, Creo.fecha_inicio,
    Creo.fecha_termino, Artistas.aid, lugares.tipo FROM Artistas INNER JOIN Creo ON Creo.aid=Artistas.aid INNER JOIN
    Obras ON Obras.oid=Creo.oid INNER JOIN Lugares ON Lugares.lid=Obras.lid WHERE
    Lugares.lid='$nom';";
    $result = $db30->prepare($query);
    $result->execute();
    $dataCollected = $result->fetchAll();
    ?>

    <table>
        <tr>
            <th>Artistas</th>
            <th>Obra en este lugar</th>
            <th>Año Inicio</th>
            <th>Año Termino</th>
            <th>Comprar Ticket</th>
        </tr>
        <?php
        foreach ($dataCollected as $p) {
          if ($p[6] = "museo"){
            echo "<tr><td><a href='consulta_artistas.php?artista=$p[5]' >$p[0]</a></td>
            <td><a href='consulta_obras.php?obra=$p[2]' >$p[1]</a></td> <td>$p[3]</td>
            <td>$p[4]</td> <td><a href='consulta_confirmar_compra.php?lugar=$nom' >Comprar entrada</a></td></tr>";

          }
          else{
            echo "<tr><td><a href='consulta_artistas.php?artista=$p[5]' >$p[0]</a></td>
            <td><a href='consulta_obras.php?obra=$p[2]' >$p[1]</a></td> <td>$p[3]</td>
            <td>$p[4]</td> <td></td></tr>";

          }
        }
        ?>
    </table>

    <?php include('../templates/footer.html'); ?>
