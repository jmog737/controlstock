<footer class='panel-footer'>
  <div class='container'>
    <div class='row'>
      <section id='hours' class='col-sm-4'>
        <span>Horario:</span>
        <br>Lunes a Viernes: 09:00 - 18:00<br>
        <hr class='visible-xs'>
        <?php
          if (!empty($_SESSION['user_id']))
            {
        ?>
        Usuario:
        <font class='naranja'>
        <?php
            // Confirm the successful log-in
            echo "<a href='#modalPwd' class='naranja' id='user'>".strtoupper($_SESSION['username'])."</a>";
        ?>
        </font>
        <br>
        <font><a href="salir.php">Salir</a></font>
        <?php
            }
        ?>
      </section>
      <section id='address' class='col-sm-4 hidden-xs'>
        <span>Dirección:</span><br>
        Buenos Aires 486<br>
        Montevideo, Uruguay<br>v.1.0.1<br>
        &copy; Copyright EMSA SERV. TEC. 2017
        <hr class='visible-xs'>
      </section>
      <section id='testimonials' class='col-sm-4'>
        <span>Teléfonos:</span>
        <br>29153304 / 29160318 / 29154195
        <hr class='visible-xs'>
      </section>
    </div>
  </div>
</footer>
