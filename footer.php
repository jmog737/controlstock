<footer class='panel-footer'>
  <a href='#' class="arrow arrow-bottom"><img border='0'  src="images/arrowDown1.png" height="35" width="35" title="BAJAR" /></a>
  <a href='#' class="arrow arrow-top"><img border='0'  src="images/arrowUp1.png" height="35" width="35"  title="SUBIR" /></a>
  <div class='container-fluid'>
    <div class='row'>
      <section id='hours' class='col-sm-4'>
        <span>Horario:</span>
        <br>Lunes a Viernes: 09:00 - 18:00<br>
        <hr class='d-block d-sm-none'>
        <?php
          if (!empty($_SESSION['user_id']))
            {
        ?>
        Usuario:
        <font class='naranja'>
        <?php
            // Confirm the successful log-in
            echo "<a href='#modalPwd' title='Cambiar contraseña de acceso' class='naranja' id='user'>".strtoupper($_SESSION['username'])."</a>"
              . " <br>"
              . "<a href='#modalParametros' title='Cambiar los parámetros' class='naranja' id='param'>--- Cambiar Par&aacute;metros ---</a>";
        ?>
        </font>
        <br>
        <font><a title="Salir del programa" href="salir.php">Salir</a></font>
        <hr class='d-block d-sm-none'>
          <?php
            }
        ?>
      </section>
      <section id='address' class='col-sm-4 d-none d-sm-block'>
        <span>Dirección:</span><br>
        Buenos Aires 486<br>
        Montevideo, Uruguay<br>v.3.0<br>
        &copy; Copyright EMSA SERV. TEC. 2017
        <hr class='d-block d-sm-none'>
      </section>
      <section id='testimonials' class='col-sm-4'>
        <span>Teléfonos:</span>
        <br>29153304 / 29160318 / 29154195
      </section>
    </div>
    <p>
      <input id="usuarioSesion" name="usuarioSesion" type="text" value="" style="color: black; display: none">
      <input id="userID" name="userID" type="text" value="" style="color: black; display: none">
      <input id="timestampSesion" name="timestampSesion" type="text" value="" style="color: black; display: none">
      <input id="nombreGrafica" name="nombreGrafica" type="text" value="<?php echo $_SESSION["nombreGrafica"]?>" style="color: black; display: none">
    </p>
  </div>
</footer>
