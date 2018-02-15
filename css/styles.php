<?php

header('content-type:text/css');
 
/******** STYLES *********/
$colorFondo = '#61122f';
/****** FIN STYLES *******/

/******* STYLES1: ********/ 
//$colorFondo = '#396540';
/****** FIN STYLES1 ******/

/******** STYLES2: *******/ 
//$colorFondo = '#cc514d';
/****** FIN STYLES2 ******/

echo <<<FINCSS
/**
******************************************************
*  @file styles.css
*  @brief Archivo con todas las hojas de estilo usadas en el programa.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Marzo 2017
*
*******************************************************/
* {
  margin: 0px;
  padding: 0px;
  box-sizing: border-box;
  font-family: serif, helvetica, arial;
  text-align: inherit;
}

html {
  min-height: 100%;
  position: relative;
}
body {
  font-size: 16px;
  color: #fff;
  background-color: $colorFondo;
  font-family: sans-serif;
  margin-bottom: 140px;
}

/****************************** HEADER **************************************/
#header-nav {
  background-color: #023184;
  border-radius: 0;
  border: 0;
  width: 100%;
  margin: 0;
}
.container {
  width: 100%;
}
.navbar-header {
  
}
.navbar-header button.navbar-toggle, .navbar-header .icon-bar {
  border: 1px solid #ccce8b;
}
.navbar-header button.navbar-toggle {
  clear: both;
  margin-top: -30px;
}
#logo-img {
  background: url('../images/logo-emsa.png') no-repeat;
  width: 300px;
  height: 100px;
  margin: 10px 10px 10px 0;
}
.navbar-brand {
  padding-top: 25px;
}
.navbar-brand h1 { /* Page section name */
  font-family: 'Lora', serif;
  color: #ccce8b;
  font-size: 2.1em;
  text-transform: uppercase;
  font-weight: bold;
  text-shadow: 1px 1px 1px #222;
  margin-top: 0;
  margin-bottom: 0;
  line-height: .75;
}
.navbar-brand a:hover, .navbar-brand a:focus {
  text-decoration: none;
}
#drop{
  background-color: #023184;
}

#nav-list a {
  color: #ccce8b;
  background-color: #023184;
}
#nav-list a:hover {
  background: #009999;
}
#nav-list .menu a:hover {
    zoom: 1.5;
}
#nav-list a span {
  font-size: 1.8em;
}
/*************************** END HEADER *************************************/



/****************************** FOOTER **************************************/
.panel-footer {
  margin-top: 10px;
  padding-top: 10px;
  padding-bottom: 10px;
  background-color: #023184;
  border-top: 0;
  position: absolute;
  bottom: 0;
  width: 100%;
  max-height: 140px;
}
.panel-footer div.row {
  margin-bottom: 15px;
  text-align: center;
}
/**************************** END FOOTER ************************************/



/**************************** HOME PAGE *************************************/
.container .jumbotron {
  box-shadow: 0 0 50px #3F0C1F;
  border: 2px solid #3F0C1F;
  margin: 0px;
  width: 100%;
  min-height:100%;
}
h2 {
  color:#ffcc66;
  font-weight: bold;
  text-decoration: underline;
  margin: 20px;
}
h3 h4{
  color:#fff;
}

input[type=checkbox] {
  zoom: 1.4;
}
.derecha {
  float: right;
}
.izquierda {
  float: left;
}
#main-content {
  text-align: center;
  background-color: #ffff99;/* agregardo durante el desarrollo para diferenciar!!!*/
  background-color: $colorFondo;
  /*padding: 15px;*/
  margin: auto;
  position: relative;
  
}
#fila {
  background-color: $colorFondo;
  /*background-color: #99ffff;*/
  padding: 30px;
  padding-top: 0px;
  margin: 0px;
}
#volverActividad, #volverReferencia, #volverUsuario {
  color:#fff;
}
a.detailObject:focus, a.detailObject:hover {
  background-color:#ffff00;
  font-weight: bolder;
  font-size: 1.3em;
}

#selector {
  background-color: $colorFondo;
  /*background-color: #99ff99;/* agregardo durante el desarrollo para diferenciar!!!*/
  position: relative;
  padding: 0px;
  height: 100%;
}
#selector a:hover, a:focus {
  color: #080571;
  text-decoration: underline;
}
#content {
  background-color: $colorFondo;
  /*background-color: #ffcc99; agregardo durante el desarrollo para diferenciar!!!*/
  padding-left: 50px;
  position: relative;
  padding: 0px;
}

/* TABLAS */
.tituloTabla {
  border-radius: 25px 25px 0 0;
  text-align: center;
}
.pieTabla {
  border-radius: 0 0 25px 25px;
}
.pieTablaIzquierdo {
  border-radius: 0 0 0 25px;
}
.pieTablaDerecho {
  border-radius: 0 0 25px 0;
}

.tituloTablaIzquierdo {
  border-radius: 25px 0 0 0;
}
.tituloTablaDerecho {
  border-radius: 0 25px 0 0;
}
.tabla1 {
  border: white 2px solid;
  background-color: #48403e;
  text-align: center;
  color: #000;
  border-collapse: separate;
  margin: 0 auto;
  max-width: 80%;
}
.tabla1 th {
  background-color: #0066cc;
  text-align: center;
  color: #ffff00;
  border: white 2px solid;
}
.tabla1 tr {
  border: white 2px solid;
}
.tabla1 td {
  border: white 2px solid;
}
.tabla1 td input text{
  width: 100%;
  text-align: center;
}

#ref table, #ref td, #ref th {
  padding: 0px;
  max-width: 70%;
}

.tabla2 {
  border: #c0c0c0 1px solid;
  background-color:#fbffe2;
  text-align: center;
  color: #000;
  border-collapse: separate;
  margin: 0 auto;
  border-radius: 25px;
  max-width: 60%;
}
.tabla2 caption {
  display: table-caption;
  text-align: center;
  caption-side: bottom;
}   
.tabla2 th {
  background-color: #0066cc;
  color: #ffff00;
  border: #c0c0c0 1px solid;
  margin: 0px;
  line-height:none;
  max-width: 100%;
  text-align: center;
}
.tabla2 tr {
  border: #c0c0c0 1px solid;
  border-collapse: separate;
  padding: 0px;
}
.tabla2 td {
  border: #c0c0c0 1px solid;
  border-collapse: separate;
  padding: 3px;
  /*font-size: 0.9em;*/
}
.tabla2 td input[type="text"] {
  line-height: normal;
  width: 100%;
  border: #c0c0c0 1px solid;
  border-collapse: separate;
  text-align: center;
  padding: 0px;
}
.tabla2 td input[type="textarea"] {
  line-height: normal;
  width: 100%;
  border: #c0c0c0 1px solid;
  border-collapse: separate;
  text-align: center;
  padding: 0px;
}
.tabla2 td select {
  line-height: normal;
  width: 100%;
  max-width: 100%;
  border: #c0c0c0 1px solid;
  border-collapse: separate;
  text-align: center;
  padding: 0px;
}

#snapshot {
  padding: 10px;
}
.encabezado {
  color: #ffcc00;
  color: #ccce8b;
  text-decoration: underline;
  font-weight: bolder;
}
.detailActivity {
  background-color: #ffff99;
}
.detailObject {
  /*background-color: #99ff99;*/
}
.detailUser {
  text-align: right;
}
.naranja {
  color: #ecb127;
}

.fondoNaranja {
  background-color: #ecb127;
}

.fondoVerde {
  background-color: #276731;
}

.fondoRojo {
  background-color: #c12929;
}

.negrita {
  font-weight: bolder;
}
.italica {
  font-style: italic;
}
.resaltado {
  background-color:#ffff00;
  background-color: #a3a59d;
  background-color: #9bf79b;
  font-weight: bolder;
  font-size: 1.2em;
  color: red;
}
.resaltado1 {
  background-color:#07ff00;
  font-weight: bolder;
  font-size: 1.6em;
  color: #1400af;
}
.alarma1 {
  background-color:#ffff33;
  font-weight: bolder;
  font-size: 1.4em;
  font-style: italic;
  color: black;
}
.alarma2 {
  background-color:#c12929;
  font-weight: bolder;
  font-size: 1.4em;
  font-style: italic;
  color: white;
}
.centrado {
  text-align: center;
}
.hint {
  max-width: 100px;
  position: relative;
  min-width: 100px;
  overflow:auto;
}
#hint {
  max-width: 100%;
  
  position: relative;
  overflow-x: scroll;
  overflow-y: auto;
}

.subtotal {
  background-color:#9db0f3;
  font-weight: bolder;
  font-size: 1.4em;
}
.totalConsumos {
  background-color:#efa569;
  font-weight: bolder;
  font-size: 1.4em;
}
.totalIngresos {
  background-color:#4b5af3;
  font-weight: bolder;
  font-size: 1.4em;
}

.tituloModal {
  background-color: #0c2c93;
}
.tblModal {
  color: #000;
}
/************************* END HOME PAGE *************************************/

/* Popup container */
.popup {
    position: relative;
    display: inline-block;
    cursor: pointer;
}

/* The actual popup (appears on top) */
.popup .popuptext {
    visibility: hidden;
    width: 160px;
    background-color: #555;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 8px 0;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    margin-left: -80px;
}

/* Popup arrow */
.popup .popuptext::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #555 transparent transparent transparent;
}

/* Toggle this class when clicking on the popup container (hide and show the popup) */
.popup .show {
    visibility: visible;
    -webkit-animation: fadeIn 1s;
    animation: fadeIn 1s
}

/* Add animation (fade in the popup) */
@-webkit-keyframes fadeIn {
    from {opacity: 0;} 
    to {opacity: 1;}
}

@keyframes fadeIn {
    from {opacity: 0;}
    to {opacity:1 ;}
}

.arrow {
  position: fixed;
  text-decoration: none;
  color: #fff;
  
  font-size: 16px;
  padding: 1em;
  display: none;
  opacity: 1;
}

.arrow:hover {
  background-color: rgba(0, 0, 0, 0.4);
}  
  
.arrow-bottom {
  top: 2em;
  right: 0em;      
}
        
.arrow-top {
  bottom: 9em;
  right: 0em;
}  


/************************* MEDIA QUERIES *************************************/

/********** Large devices only (> 1200px) **********/
@media (min-width: 1200px) {
  /* Header */
  .navbar-brand h1 { /* Page section name */
  font-size: 3.0em;
  margin-top: 7px;
  }
  /* End Header */
  
  .container .jumbotron {
    background: url('../images/jumbotron_1200.jpg') no-repeat;
    height: 675px;
  }
}

/********** Medium devices only (992px - 1199px )**********/
@media (min-width: 992px) and (max-width: 1199px) {
  /* Header */
  .navbar-brand h1 { /* Page section name */
  font-size: 4.15vw; /* 1vw = 1% of viewport width */
  margin-top: 9px;
  }
  /* End Header */

  /* Home Page */
  .container .jumbotron {
    background: url('../images/jumbotron_992.jpg') no-repeat;
    height: 558px;
  }
  /* End Home Page */
}

/********** Small devices only (768px - 991px ) **********/
@media (min-width: 768px) and (max-width: 991px) {
  /* Header */
  .navbar-brand {
    padding-top: 5px;
    padding-right: 0px;
    height: 100px;
    margin-top: 10px
  }
  .navbar-brand h1 { /* Page section name */
  font-size: 3.73vw; /* 1vw = 1% of viewport width */
  padding-top: 20px;
  padding-right: 10px;
  }
  #logo-img {
    background: url('../images/logo-emsa150.png') no-repeat;
    width: 225px;
    height: 100px;
    margin: 10px 10px 10px 0;
  }
  /* End Header */
 
  /* Home Page */
  .container .jumbotron {
    background: url('../images/jumbotron_768.jpg') no-repeat;
    height: 432px;
  }
  /* End Home Page */
  
  #testimonials {
    font-size: 15px;
  }
}

/********** Extra small devices only (480px - 767px) **********/
@media (max-width: 767px) {
  /* Header */
  .navbar-brand {
    padding-top: 10px;
    height: 100px;
    margin-top: 10px
  }
  .navbar-brand h1 { /* Restaurant name */
    padding-top: 20px;
    font-size: 3.6vw; /* 1vw = 1% of viewport width */
  }

  #logo-img {
  background: url('../images/logo-emsa150.png') no-repeat;
  width: 150px;
  height: 100px;
  margin: 20px 10px 10px 0;
  }

  .divider {
        height: 1px;
        margin: 9px 0;
        overflow: hidden;
        background-color: #e5e5e5;
    }
  .navbar-header button.navbar-toggle {
  margin-top: -85px;
  }
  #collapsable-nav a { /* Collapsed nav menu text */
    font-size: 1.2em;
  }
  #collapsable-nav a span { /* Collapsed nav menu glyph */
    font-size: 1em;
    margin-right: 5px;
  }
  #nav-list a:hover {
  background: #009999;
}
  /* End Header */

  /* Footer */
  .panel-footer {
    max-height: 150px;
    font-size: 11px;
  }
  .panel-footer section {
    margin-bottom: 10px;
    text-align: center;
  }
  .panel-footer section:nth-child(3) {
    margin-bottom: 0; /* margin already exists on the whole row */
  }
  .panel-footer section hr {
    width: 100%;
  }
  /* End Footer */
}


/********** Super extra small devices Only :-) (e.g., iPhone 4) **********/
@media (max-width: 576px) {
  /* Header */
  .navbar-brand {
    padding-top: 10px;
    height: 100px;
    margin-top: 10px
  }
  .navbar-brand h1 { /* Restaurant name */
    padding-top: 15px;
    font-size: 3.5vw;
  }
  #logo-img {
    background: url('../images/logo-emsa150.png') no-repeat;
    width: 150px;
    height: 100px;
    margin: 20px 10px 10px 0;
  }
  #nav-list  {
    font-size: 1.9vw;
  }
  /* End Header */
  
  /* Home page */
  .col-xxs-12 {
    position: relative;
    min-height: 1px;
    padding-right: 15px;
    padding-left: 15px;
    float: left;
    width: 100%;
  }
}

/************************* END MEDIA QUERIES ********************************/

FINCSS;
?>