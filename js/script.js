/**
///  \file script.js
///  \brief Archivo que contiene todas las funciones de Javascript.
///  \author Juan Martín Ortega
///  \version 1.0
///  \date Marzo 2017
*/

/***********************************************************************************************************************
/// ************************************************** FUNCIONES GENÉRICAS *********************************************
************************************************************************************************************************
*/

function verificarSesion() {
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        var myObj = JSON.parse(this.responseText);
        var user = myObj.user;
        var timestamp = myObj.timestamp;
        $("#estadoSesion").val(user);
        $("#timestampSesion").val(timestamp);
    }
  };
  xmlhttp.open("GET", "data/estadoSesion.php", true);
  xmlhttp.send();
}

/**
 * \brief Función que vacía el contenido del div cuyo Id se pasa como parámetro.
 * @param id {String} String del Id del DIV que se quiere vaciar.
 */
function vaciarContent (id) {
  $(id).empty();
}

/**
  \brief Función que habilita o deshabilita los botones según el caso en el que se esté.
*/
function cambiarEdicion()
  {
  var fuente = document.getElementById("fuente").value;
  var editar = "";
  switch (fuente) {
    case "actividad": editar = document.getElementById("editarActividad").value;
                      break;
    case "referencia": editar = document.getElementById("editarReferencia").value;
                       break;
    case "llave": editar = document.getElementById("editarLlave").value;
                  break;
    case "certificado": editar = document.getElementById("editarCertificado").value;
                        break;
    case "usuario": editar = document.getElementById("editarUsuario").value;
                        break;    
    case "slot": editar = document.getElementById("editarSlot").value;
                        break;                    
    default: break;
  }
  
  if (editar === 'EDITAR') {
    accion = 'habilitar';
  }
  else {
    accion = 'deshabilitar';
  }
  
  switch (fuente)
    {
    case "llave":
                 if (accion === "habilitar")
                    {
                    habilitarLlave();
                  }
                 else
                    {
                    inhabilitarLlave(); 
                  }
                 break;
    case "certificado":
                      if (accion === "habilitar")
                        {
                        habilitarCertificado();
                      }
                      else
                        {
                        inhabilitarCertificado();      
                      }
                      break;
    case "actividad":
                    if (accion === "habilitar")
                      {
                      habilitarActividad(); 
                    }
                    else
                      {
                      inhabilitarActividad();
                    }
                    break;
    case "referencia":
                      if (accion === "habilitar")
                        {
                        habilitarReferencia();
                      }
                      else
                        {
                        inhabilitarReferencia(); 
                      }
                      break;
    case "usuario":
                      if (accion === "habilitar")
                        {
                        habilitarUsuario();
                      }
                      else
                        {
                        inhabilitarUsuario();
                      }
                      break;
    case "slot":
                      if (accion === "habilitar")
                        {
                        habilitarSlot();
                      }
                      else
                        {
                        inhabilitarSlot(); 
                      }
                      break;                  
    default: break;                  
  }  
}

/**
  \brief Función que valida que el parámetro pasado sea un entero.
  @param valor Dato a validar.                  
*/
function validarEntero(valor)
  {
  valor = parseInt(valor);
  //Compruebo si es un valor num�rico
  if (isNaN(valor)) {
      //entonces (no es numero) devuelvo el valor cadena vacia
      return false;
      }
  else{
      //En caso contrario (Si era un n�mero) devuelvo el valor
      return true;
  }
}

/**
 * 
 * @param {String} str String con la cadena de texto a buscar como parte del producto.
 * \brief Función que muestra las sugerencias de los productos disponibles.
 */
function showHint(str) {
  if (str.length === 0) { 
    document.getElementById("producto").innerHTML = "";
    return;
  } else {
    var url = "data/selectQuery.php";
    var query = "select idprod, entidad, nombre_plastico, bin from productos where nombre_plastico like '"+str+"%' order by nombre_plastico asc";
    
    $.getJSON(url, {query: ""+query+""}).done(function(request) {
      var sugerencias = request.resultado;
      var totalSugerencias = request.rows;
      $("[name='hint']").remove();
      
      var mostrar = '';
      
      if (totalSugerencias >= 1) {
        mostrar = '<select name="hint" id="hint" style="width: 100%">';
        mostrar += '<option value="NADA" name="NADA">--Seleccionar--</option>';
        for (var i in sugerencias) {
          var bin = sugerencias[i]["bin"];
          if (bin === null) {
            bin = 'SIN BIN';
          }
          mostrar += '<option value="'+sugerencias[i]["idprod"]+'" name="'+bin+'">(' + sugerencias[i]["entidad"]+') '+sugerencias[i]["nombre_plastico"] + ' -- ' +bin+' --</option>';
        }
        mostrar += '</select>';
      }
      else {
        mostrar = '<p name="hint" value="">No se encontraron sugerencias!</p>';
      }
      $("#producto").after(mostrar);
    });
  }
}

/***********************************************************************************************************************
/// ********************************************** FIN FUNCIONES GENÉRICAS *********************************************
************************************************************************************************************************
*/


/***********************************************************************************************************************
/// ************************************************ FUNCIONES MOVIMIENTOS *********************************************
************************************************************************************************************************
*/


/**
 * \brief Función que valida los datos pasados para el movimiento.
 * @returns {Boolean} Devuelve un booleano que indica si se pasó o no la validación de los datos para el movimiento.
 */
function validarMovimiento()
  {
  var seguir = false;
  var cantidad = document.getElementById("cantidad").value;
  var cantidad2 = document.getElementById("cantidad2").value;
  var fecha = document.getElementById("fecha").value;
  
  if (fecha === "") {
    alert('Se debe seleccionar la Fecha');
    document.getElementById("fecha").focus();
    seguir = false;
  }
  else {
    if ($("#hint").find('option:selected').val() === "NADA")
      {
      alert('Debe seleccionar el nombre del plástico.');
      document.getElementById("hint").focus();
      seguir = false;
    }
    else
      {
      var cant = validarEntero(document.getElementById("cantidad").value);

      if ((cantidad <= 0) || (cantidad === "null") || !cant)
        {
        alert('La cantidad de tarjetas debe ser un entero mayor o igual a 1.');
        $("#cantidad").val("");
        document.getElementById("cantidad").focus();
        seguir = false;
      } 
      else
        {
        var cant2 = validarEntero(document.getElementById("cantidad2").value);  

        if ((cantidad2 <= 0) || (cantidad2 === "null") || !cant2)
          {
          alert('La repetición de la cantidad de tarjetas debe ser un entero mayor o igual a 1.');
          $("#cantidad2").val("");
          document.getElementById("cantidad2").focus();
          seguir = false;
        } 
        else
          {
          if (cantidad !== cantidad2)
            {
            alert('Las cantidades de tarjetas ingresadas deben coincidir. Por favor verifique!.');
            $("#cantidad").val("");
            $("#cantidad2").val("");
            document.getElementById("cantidad").focus();
            seguir = false;
          } 
          else
            { 
            seguir = true;
          }// cantidad != cantidad2
        }// cantidad 2
      }// cantidad
    }// nombre_plastico
  }
  if (seguir) return true;
  else return false;
}

/***********************************************************************************************************************
/// ********************************************* FIN FUNCIONES MOVIMIENTOS ********************************************
************************************************************************************************************************
**/


/***********************************************************************************************************************
/// ********************************************** FUNCIONES IMPORTACIONES *********************************************
************************************************************************************************************************
*/

/**
 * \brief Función que valida los datos pasados para la importación.
 * @returns {Boolean} Devuelve un booleano que indica si se pasó o no la validación de los datos para la importación.
 */
function validarImportacion()
  {
  var seguir = false;
  var cantidad = document.getElementById("cantidad").value;
  var cantidad2 = document.getElementById("cantidad2").value;
  var fecha = document.getElementById("fecha").value;
  
  if (fecha === "") {
    alert('Se debe seleccionar la Fecha');
    document.getElementById("fecha").focus();
    seguir = false;
  }
  else
    {
    if ($("#hint").find('option:selected').val() === "NADA")
      {
      alert('Debe seleccionar el nombre del plástico.');
      document.getElementById("hint").focus();
      seguir = false;
    }
    else
      {
        
      var cant = validarEntero(document.getElementById("cantidad").value);

      if ((cantidad <= 0) || (cantidad === "null") || !cant)
        {
        alert('La cantidad de tarjetas debe ser un entero mayor o igual a 1.');
        $("#cantidad").val("");
        document.getElementById("cantidad").focus();
        seguir = false;
      } 
      else
        {
        var cant2 = validarEntero(document.getElementById("cantidad2").value);  

        if ((cantidad2 <= 0) || (cantidad2 === "null") || !cant2)
          {
          alert('La repetición de la cantidad de tarjetas debe ser un entero mayor o igual a 1.');
          $("#cantidad2").val("");
          document.getElementById("cantidad2").focus();
          seguir = false;
        } 
        else
          {
          if (cantidad !== cantidad2)
            {
            alert('Las cantidades de tarjetas ingresadas deben coincidir. Por favor verifique!.');
            $("#cantidad").val("");
            $("#cantidad2").val("");
            document.getElementById("cantidad").focus();
            seguir = false;
          } 
          else
            {
            seguir = true;
          }// cantidad != cantidad2
        }// cantidad 2
      }// cantidad
    }// nombre_plastico
  }
  
  if (seguir) return true;
  else return false;
}

/***********************************************************************************************************************
/// ******************************************** FIN FUNCIONES IMPORTACIONES *******************************************
************************************************************************************************************************
**/


/***********************************************************************************************************************
/// ************************************************* FUNCIONES USUARIOS ***********************************************
************************************************************************************************************************
*/

/**
 * \brief Función que carga en el form pasado como parámetro todos los usuarios.
 * @param {String} selector String con el DIV donde se deben de cargar los datos.
 * @param {Int} user Id del usuario a resaltar en el listado.
 */ 
function cargarUsuarios(selector, user){
  var url = "data/selectQuery.php";
  var query = "select idusuarios, apellido, nombre, empresa from usuarios where estado='activo' order by empresa asc, apellido asc, idusuarios asc";
  
  $.getJSON(url, {query: ""+query+""}).done(function(request) {
    var usuario = request.resultado;
    var total = parseInt(request.rows, 10);
    var encabezado = '<h2 id="titulo" class="encabezado">LISTADO DE USUARIOS</h2>';
    var cargar = '';
    cargar += encabezado;
    if (total >= 1) {
      var tabla = '<table id="usuarios" name="usuarios" class="tabla2">';
      var tr = '<tr>\n\
                  <th colspan="4" class="tituloTabla">USUARIOS</th>\n\
                </tr>';
      tr += '<tr>\n\
                <th>Ítem</th>\n\
                <th>Apellido</th>\n\
                <th>Nombre</th>\n\
                <th>Empresa</th>\n\
            </tr>';
      for (var index in usuario) {
        var nombre = usuario[index]["nombre"];
        var apellido = usuario[index]["apellido"];
        var empresa = usuario[index]["empresa"];
        var id = usuario[index]["idusuarios"];
        var i = parseInt(index, 10) + 1;
        var clase = '';
        if ((id !== 0) && (id === user)){
                clase = 'resaltado';
            }
            else {
              clase = '';
            }
        tr += '<tr>\n\
                  <td>'+i+'</td>\n\
                  <td><a href="#" id="'+id+'" class="detailUser '+clase+'">'+apellido+'</a></td>\n\
                  <td><a href="#" id="'+id+'" class="detailUser '+clase+'">'+nombre+'</a></td>\n\
                  <td><b>'+empresa+'</b></td>\n\
              </tr>';
      }
      tr += '<tr>\n\
                <td class="pieTabla" colspan="4"><input type="button" value="NUEVO" id="nuevoUsuario" class="btn-success"></td>\n\
             </tr>';
      tr += '</table>';
      tabla += tr;
      cargar += tabla;
      $(selector).html(cargar);
    }
    else {
      var texto = '<h2>Ya NO quedan usuarios activos!.</h2>';
      cargar += texto;
      vaciarContent("#main-content");
      $("#main-content").html(cargar);
    }    
    
  });
}

/**
  \brief Función que recupera y carga los datos del usuario pasado como parámetro.
  @param {Int} user Entero con el índice del usuario a recuperar.
*/
function cargarDetalleUsuario(user) {
  var url = "data/selectQuery.php"; 
  var query = "select nombre, apellido, empresa, mail, telefono, observaciones from usuarios where idusuarios='"+user+"'";
  
  $.getJSON(url, {query: ""+query+""}).done(function(request) {
    var usuario = request.resultado[0];
    var nombre = usuario["nombre"];
    var apellido = usuario["apellido"];
    var empresa = usuario["empresa"];
    var mail = usuario["mail"];if (mail === null) mail = '';
    var tel = usuario["telefono"];if (tel === null) tel = '';
    var obs = usuario["observaciones"];if (obs === null) obs = '';
    if ($("#content").length === 0) {
      var divs = "<div id='fila' class='row'>\n\
                    <div id='selector' class='col-md-6 col-sm-12'></div>\n\
                    <div id='content' class='col-md-6 col-sm-12'></div>\n\
                  </div>";
      $("#main-content").empty();
      $("#main-content").append(divs);
    }
    cargarUsuarios('#selector', user);
    $("#selector").css('padding-right', '30px');
    
    var emsa = '';
    var bbva = '';
    var itau = '';
    var scotia = '';
    switch (empresa) {
      case "EMSA": emsa = 'selected';
                   break;
      case "BBVA": bbva = 'selected';
                   break;
      case "ITAU": itau = 'selected';
                   break;
      case "SCOTIA": scotia = 'selected';
                     break;
      default: break;               
    }
    var formu = '<form name="userDetail" id="userDetail" method="post" action="exportar.php" class="exportarForm">';
    var tabla = '<table id="detalleUsuario" name="detalleUsuario" class="tabla2">';
    var tr = '<tr>\n\
                <th colspan="4" class="tituloTabla">DATOS DEL USUARIO</th>\n\
              </tr>';
    tr += '<tr>\n\
              <th>Apellido</th>\n\
              <td><input id="apellido" name="apellido" class="resaltado" type="text" value="'+apellido+'" disabled="true"></td>\n\
              <th>Nombre</th>\n\
              <td><input id="nombre" name="nombre" class="resaltado" type="text" value="'+nombre+'" disabled="true"></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th>Empresa</th>\n\
              <td colspan="3">\n\
                <select id="empresa" name="empresa" style="width:100%" disabled="true">\n\
                  <option value="seleccionar">---SELECCIONAR---</option>\n\
                  <option value="EMSA" '+emsa+'>EMSA</option>\n\
                  <option value="BBVA" '+bbva+'>BBVA</option>\n\
                  <option value="ITAU" '+itau+'>ITAU</option>\n\
                  <option value="SCOTIA" '+scotia+'>SCOTIA</option>\n\
                </select>\n\
              </td>\n\
          </tr>';
    tr += '<tr>\n\
              <th>Mail</th>\n\
              <td colspan="3"><input id="mail" name="mail" type="text" value="'+mail+'" disabled="true"></td>\n\
           </tr>';
    tr += '<tr>\n\
              <th>Teléfono</th>\n\
              <td colspan="3"><input id="telefono" name="telefono" type="text" value="'+tel+'" disabled="true"></td>\n\
           </tr>';
    tr += '<tr>\n\
              <th>Observaciones</th>\n\
              <td colspan="3"><textarea id="observaciones" name="observaciones" style="width: 100%;resize: none" disabled="true">'+ obs +'</textarea></td>\n\
           </tr>';
    tr += '<tr>\n\
              <td class="pieTablaIzquierdo"><input type="button" id="editarUsuario" name="editarUsuario" value="EDITAR" onclick="cambiarEdicion()" class="btn-info"></td>\n\
              <td colspan="1"><input type="button" id="actualizarUsuario" name="actualizarUsuario" disabled="true" value="ACTUALIZAR" class="btn-warning"></td>\n\
              <td colspan="1"><input type="button" id="7" name="exportarUsuario" value="EXPORTAR" class="btn-info exportar"></td>\n\
              <td class="pieTablaDerecho"><input type="button" id="eliminarUsuario" name="eliminarUsuario" value="ELIMINAR" class="btn-danger"></td>\n\
              <td style="display:none"><input type="text" id="fuente" name="fuente" value="usuario"></td>\n\
              <td style="display:none"><input type="text" id="param" name="param" value=""></td>\n\
              <td style="display:none"><input type="text" id="iduser" name="iduser" value="'+user+'"></td>\n\
          </tr>'; 
    tr += '</table>';
    tr += '</form>';
    tabla += tr;
    var encabezado = '<h3 id="titulo" class="encabezado">DETALLES DEL USUARIO</h3>';
    var cargar = '';
    cargar += encabezado;
    cargar += formu;
    cargar += tabla;
    $("#content").html(cargar);
  });
}

/**
  \brief Función que deshabilita los input del form Usuario.
*/
function inhabilitarUsuario(){
  document.getElementById("nombre").disabled = true;
  document.getElementById("apellido").disabled = true;
  document.getElementById("empresa").disabled = true;
  document.getElementById("mail").disabled = true;
  document.getElementById("telefono").disabled = true;
  document.getElementById("observaciones").disabled = true;
  document.getElementById("editarUsuario").value = "EDITAR";
  document.getElementById("actualizarUsuario").disabled = true;
}

/**
  \brief Función que habilita los input del form Usuario.
*/
function habilitarUsuario(){
  document.getElementById("nombre").disabled = false;
  document.getElementById("apellido").disabled = false;
  document.getElementById("empresa").disabled = false;
  document.getElementById("mail").disabled = false;
  document.getElementById("telefono").disabled = false;
  document.getElementById("observaciones").disabled = false;
  document.getElementById("editarUsuario").value = "BLOQUEAR";
  document.getElementById("actualizarUsuario").disabled = false;
}

/**
 * \brief Función que valida los datos pasados para el usuario.
 * @returns {Boolean} Devuelve un booleano que indica si se pasó o no la validación de los datos para el usuario.
 */
function validarUsuario()
  {
  var seguir = false;
  
  if (document.getElementById("nombre").value.length === 0)
    {
    alert('Debe ingresar el nombre del usuario.');
    document.getElementById("nombre").focus();
    seguir = false;
  }
  else
    {
    if (document.getElementById("apellido").value.length === 0)
      {
      alert('Debe ingresar el apellido del usuario.');
      document.getElementById("apellido").focus();
      seguir = false;
    } 
    else
      {
      if (document.getElementById("empresa").value.length === 0)
        {
        alert('Debe ingresar la empresa donde trabaja el usuario.');
        document.getElementById("empresa").focus();
        seguir = false;
      }
      else
        {
        if (document.getElementById("empresa").value === 'seleccionar')
          {
          alert('Debe seleccionar una empresa.');
          document.getElementById("empresa").focus();
          seguir = false;
        }
        else
          {
          seguir = true;
        }// empresa = seleccionar  
      }// empresa
    }// apellido
  }// nombre
  
  if (seguir) return true;
  else return false;
}

/***********************************************************************************************************************
/// *********************************************** FIN FUNCIONES USUARIOS *********************************************
************************************************************************************************************************
**/


/***********************************************************************************************************************
/// ************************************************* FUNCIONES BÚSQUEDAS **********************************************
************************************************************************************************************************
*/
function validarBusqueda() {
  
}

/***********************************************************************************************************************
/// ********************************************** FIN FUNCIONES BÚSQUEDAS *********************************************
************************************************************************************************************************
**/


/**
\brief Función que se ejecuta al cargar la página.
En la misma se ve primero desde que página se llamó, y en base a eso
se llama a la función correspondiente para cargar lo que corresponda (actividades, referencias, etc.)
Además, en la función también están los handlers para los distintos eventos jquery.
*/
function todo () {
  ///Levanto la url actual: 
  var urlActual = jQuery(location).attr('pathname');
  ///Según en que url esté, es lo que se carga:
  switch (urlActual) {
    case "/testKMS/index.php": 
      {
      
      //setTimeout(cargarActividades('#main-content', false, false, false),1000);
      break;
    }
    case "/testKMS/usuario.php": 
      {
      cargarUsuarios("#main-content", 0);
      break;
    }
    case "/testKMS/busquedas.php": 
      {
      
      break;
    }
    default: break;
  }  
  
/***************************************************************************************************************************
/// Comienzan las funciones que manejan los eventos relacionados a los MOVIMIENTOS como ser creación, edición y eliminación.
****************************************************************************************************************************
*/

///Disparar funcion al cambiar el elemento elegido en el select con las sugerencias para los productos.
///Por ahora simplemente cambia el color de fondo para resaltarlo.
$(document).on("change", "#hint", function (){
  var nombreFoto = 'images/snapshots/';
  var bin = $(this).find('option:selected').attr("name");
  nombreFoto += bin + ".jpg";
  $(this).css('background-color', '#ffffff');
  
  $("#snapshot").remove();
  
  if (bin !== "NADA"){
    var mostrar = '<img id="snapshot" name="hint" src="'+nombreFoto+'" alt="No se cargó la foto aún." height="100" width="300"></img>';
    $(this).css('background-color', '#efe473');
  }
  
  $("#hint").after(mostrar);
});

///Disparar funcion al hacer clic en el link del usuario.
$(document).on("click", "#agregarMovimiento", function (){
  //$(this).css('background-color', '#efe473');
  var seguir = true;
  seguir = validarMovimiento();
  if (seguir) {
    var fecha = $("#fecha").val();
    var producto = $("#hint").val();
    var nombreProducto = $("#hint").find('option:selected').text( );
    var tipo = $("#tipo").val()
    var cantidad = $("#cantidad").val();
    var idUserBoveda = $("#usuarioBoveda").val();
    var userBoveda = $("#usuarioBoveda").find('option:selected').attr("name");
    var idUserGrabaciones = $("#usuarioGrabaciones").val();
    var userGrabaciones = $("#usuarioGrabaciones").find('option:selected').attr("name");
    alert("¿Confirma el ingreso de los siguientes datos? \n\nFecha: "+fecha+"\nProducto: "+nombreProducto+"\nTipo: "+tipo+"\nCantidad: "+cantidad+"\nControl Bóveda: "+userBoveda+"\nControl Grabaciones: "+userGrabaciones);
  }
});

/*******************************************************************************************************************************
/// ***************************************************** FIN MOVIMIENTOS ******************************************************
********************************************************************************************************************************
*/

/*****************************************************************************************************************************
/// Comienzan las funciones que manejan los eventos relacionados a las IMPORTACIONES como ser creación, edición y eliminación.
******************************************************************************************************************************
*/

///Disparar funcion al hacer clic en el link del usuario.
$(document).on("click", "#agregarImportacion", function (){
  //$(this).css('background-color', '#efe473');
  var seguir = true;
  seguir = validarImportacion();
  if (seguir) {
    alert('validada');
  }
});

/*******************************************************************************************************************************
/// **************************************************** FIN IMPORTACIONES *****************************************************
********************************************************************************************************************************
*/

/***************************************************************************************************************************
/// Comienzan las funciones que manejan los eventos relacionados a los USUARIOS como ser creación, edición y eliminación.
****************************************************************************************************************************
*/

///Disparar funcion al hacer clic en el link del usuario.
///Se cargan en el DIV #content los datos del mismo.
$(document).on("click", ".detailUser", function () {
    var user = $(this).attr("id");
    
    var timestamp = Math.round(Date.now() / 1000);
      
    if(timestamp - $("#timestampSesion").val() > $("#duracionSesion").val()) {
      window.location.href = "http://localhost/testKMS/index.php";
    }
    else {
      verificarSesion();
      cargarDetalleUsuario(user);
    }
    
  });//*** fin del click detailUser ***
  
///Disparar funcion al hacer click en el botón eliminar.
///Esto hace que el registro correspondiente al usuario pase a estado de inactivo.
///Además, se "limpia" el form del div #selector quitando el usuario eliminado.
$(document).on("click", "#eliminarUsuario", function () {
  var pregunta = confirm('Está a punto de eliminar el registro. ¿Desea continuar?');
  if (pregunta) {
    var url = "data/selectQuery.php";
    var user = document.getElementById("iduser").value;
    var query = "select idusuarios as iduser from usuarios where estado='activo' order by empresa asc, apellido asc, idusuarios asc";
    
    $.getJSON(url, {query: ""+query+""}).done(function(request) {
      var idks = request["resultado"];
      var total = request["rows"];
      var ids = new Array();
      for (var index in idks) {
        ids.push(idks[index]["iduser"]);
      }
      
      var indiceActual = ids.indexOf(user);//alert(indiceActual);
      var user1 = 0;
      
      if (total === 1)  {//alert('total = 1. Este es el último');
        var volver = '<br><a href="index.php">Volver</a>';
        var texto = '<h2>Ya NO quedan usuarios!.</h2>';
        texto += volver;
        vaciarContent("#main-content");
        $("#main-content").html(texto);
      }
      else {
        if ((indiceActual !== 0)&&(indiceActual !== -1)) {
          user1 = indiceActual - 1;
        }
        else {
          if (indiceActual === 0) {
            user1 = indiceActual + 1;
          }
        }
      }
      var url = "data/updateQuery.php";
      var query = "update usuarios set estado='inactivo' where idusuarios='" + user + "'";
      
      $.getJSON(url, {query: ""+query+""}).done(function(request) {
        var resultado = request["resultado"];
        if (resultado === "OK") {
          if (total > 1) {
            cargarDetalleUsuario(ids[user1]);
          }
        }
        else {
          alert('Hubo un error. Por favor verifique.');
        }
      });
    });
  }
  else {
    //alert('no quiso borrar');
  }
});//*** fin del click eliminarUsuario ***

///Disparar funcion al hacer clic en el botón actualizar.
///Se validan todos los campos antes de hacer la actualización, y una vez hecha se inhabilita el form y parte de los botones.
$(document).on("click", "#actualizarUsuario", function (){
    var seguir = true;
    seguir = validarUsuario();

    ///En caso de que se valide todo, se prosigue a enviar la consulta con la actualización en base a los parámetros pasados
    if (seguir) {
      ///Recupero valores editados y armo la consulta para el update:
      var iduser = document.getElementById("iduser").value;
      var nombre = (document.getElementById("nombre").value).trim();
      var apellido = (document.getElementById("apellido").value).trim();
      var empresa = (document.getElementById("empresa").value).trim();
      var mail = (document.getElementById("mail").value).trim();
      var tel = (document.getElementById("telefono").value).trim();
      var obs = (document.getElementById("observaciones").value).trim();
      //alert('iduser: '+iduser+'\nnombre: '+nombre +'\napellido: '+apellido+'\nempresa: '+empresa+'\nmail: '+mail+'\ntel: '+tel+'\nobs: '+obs);
      
      var query = "select idusuarios as id, apellido from usuarios where nombre='"+nombre+"' and apellido='"+apellido+"' and empresa='"+empresa+"'";
      var url = "data/selectQuery.php";
      //alert(query);
      $.getJSON(url, {query: ""+query+""}).done(function(request) {
        var total = request["rows"];
        var id;
        if (total > 0) {
          id = request["resultado"][0]["id"];
        }
        if (((total <= 1) && (iduser === id)) || ((iduser !== id) && (total < 1))) {  
          var query = "update usuarios set nombre='" + nombre + "', apellido='" + apellido + "', empresa='"+empresa+"' , mail='"+mail+"', telefono='" + tel +"', observaciones='"+obs+"' where idusuarios='" + iduser + "'";
          var url = "data/updateQuery.php";
          //alert(query);
          ///Ejecuto la consulta y muestro mensaje según resultado:
          $.getJSON(url, {query: ""+query+""}).done(function(request) {
            var resultado = request["resultado"];
            if (resultado === "OK") {
              alert('Registro modificado correctamente!');  
              $("#actualizarUsuario").attr("disabled", "disabled");
              document.getElementById("editarUsuario").value = "EDITAR";
              inhabilitarUsuario();
              cargarUsuarios("#selector", iduser);
            }
            else {
              alert('Hubo un error. Por favor verifique.');
            }
          });
        }
        else {
          alert('Ya existe un usuario con esos datos. Por favor verifique.');
        }
      });  
    }
  });//*** fin del click actualizarUsuario ***
  
///Disparar función al hacer click en el botón Nuevo Usuario.
///Se vuelve al DIV #main-content y se genera un form en blanco para agregar los datos del usuario.
$(document).on("click", "#nuevoUsuario", function() {
  var encabezado = '<h1 class="encabezado">NUEVO USUARIO</h1>';
  var tabla = '<table id="datosUsuario" name="datosUsuario" class="tabla2" style="max-width:40%">';
  var tr = '<tr>\n\
              <th colspan="4" class="tituloTabla">DATOS DEL USUARIO</th>\n\
            </tr>';
  tr += '<tr>\n\
            <th>Apellido</th>\n\
            <td><input id="apellido" name="apellido" class="resaltado" type="text"></td>\n\
            <th>Nombre</th>\n\
            <td><input id="nombre" name="nombre" class="resaltado" type="text"></td>\n\
        </tr>';
  tr += '<tr>\n\
          <th>Empresa</th>\n\
          <td colspan="3">\n\
            <select id="empresa" name="empresa" style="width:100%">\n\
              <option value="seleccionar" selected="yes">---SELECCIONAR---</option>\n\
              <option value="EMSA" style="margin:auto; padding:auto">EMSA</option>\n\
              <option value="BBVA">BBVA</option>\n\
              <option value="ITAU">ITAU</option>\n\
              <option value="SCOTIA">SCOTIA</option>\n\
            </select>\n\
          </td>\n\
        </tr>';
  tr += '<tr>\n\
            <th>Mail</th>\n\
            <td colspan="3"><input id="mail" name="mail" type="text"></td>\n\
         </tr>';
  tr += '<tr>\n\
            <th>Teléfono</th>\n\
            <td colspan="3"><input id="telefono" name="telefono" type="text"></td>\n\
         </tr>';
  tr += '<tr>\n\
            <th>Observaciones</th>\n\
            <td colspan="3"><textarea id="observaciones" name="observaciones" style="width: 100%;resize: none"></textarea></td>\n\
         </tr>';
  tr += '<tr>\n\
            <td colspan="4" class="pieTabla"><input type="button" id="agregarUsuario" name="agregarUsuario" value="AGREGAR" class="btn-success"></td>\n\
         </tr>'; 
  tr += '</table>';
  tabla += tr;
  var cargar = encabezado;
  cargar += tabla;
  var volver = '<br><a id="volverUsuario" href="usuario.php">Volver</a>';
  cargar += volver;
  vaciarContent("#main-content");
  $("#main-content").html(cargar);  
});//*** fin del click nuevoUsuario ***

///Disparar función al hacer click en el botón Agregar Usuario.
///Se validan los datos para el usuario, luego 
///se cargan los dos DIVs (#selector y #content), en #selector se cargan el listado de usuarios y en 
///#content los datos del usuario recién agregado, siempre y cuando haya pasado la validación.
$(document).on("click", "#agregarUsuario", function(){
  var seguir = true;
  seguir = validarUsuario();
  
  ///En caso de que se valide todo, se prosigue a enviar la consulta con la actualización en base a los parámetros pasados
  if (seguir) {
    ///Recupero valores y armo la consulta para el insert:
    var apellido = (document.getElementById("apellido").value).trim();
    var nombre = (document.getElementById("nombre").value).trim();
    var empresa = (document.getElementById("empresa").value).trim();
    var tel = (document.getElementById("telefono").value).trim();
    var mail = (document.getElementById("mail").value).trim();
    var obs = (document.getElementById("observaciones").value).trim();
    //alert('apellido: '+apellido+'\nnombre: '+nombre +'\nempresa: '+empresa+'\nmail: '+mail+'\ntel: '+tel+'\nobs: '+obs);
      
    var query = "select apellido from usuarios where nombre='"+nombre+"' and apellido='"+apellido+"' and empresa='"+empresa+"'";
    var url = "data/selectQuery.php";
    //alert(query);
    $.getJSON(url, {query: ""+query+""}).done(function(request) {
      var total = request["rows"];
      if (total === 0) {
        var query = 'insert into usuarios (nombre, apellido, empresa, telefono, mail, observaciones) values ("'+nombre+'", "'+apellido+'", "'+empresa+'", "'+tel+'", "'+mail+'", "'+obs+'")';
        var url = "data/updateQuery.php";
        //alert(query);
   
        $.getJSON(url, {query: ""+query+""}).done(function(request) {
          var resultado = request["resultado"];
          if (resultado === "OK") {  
            var query = 'select max(idusuarios) as ultimoUser from usuarios';
            var url = "data/selectQuery.php";
            $.getJSON(url, {query: ""+query+""}).done(function(request) {
              var iduser = request.resultado[0]["ultimoUser"];
              alert('Registro agregado correctamente!');
              if ($("#content").length === 0) {
                var divs = "<div id='fila' class='row'>\n\
                              <div id='selector' class='col-md-5 col-sm-12'></div>\n\
                              <div id='content' class='col-md-7 col-sm-12'></div>\n\
                            </div>";
              }  
              $("#main-content").empty();
              $("#main-content").append(divs);
              cargarUsuarios("#selector", iduser);
              cargarDetalleUsuario(iduser);
              inhabilitarUsuario();
            });
          } 
          else {
            alert('Hubo un error... Por favor verifique.');
          }
        }); 
      }
      else {
        alert('Ya existe un usuario con esos datos. Por favor verifique.');
      }
    });
  }
});//*** fin del click agregarUsuario ***
    
/**************************************************************************************************************************
/// **************************************************** FIN USUARIOS *****************************************************
***************************************************************************************************************************
*/

/**************************************************************************************************************************
/// Comienzan las funciones que manejan los eventos relacionados a las BÚSQUEDAS como ser creación, edición y eliminación.
***************************************************************************************************************************
*/

$(document).on("click", "#realizarBusqueda", function () {
  var timestamp = Math.round(Date.now() / 1000);
      
  if(timestamp - $("#timestampSesion").val() > $("#duracionSesion").val()) {
    window.location.href = "http://localhost/testKMS/index.php";
  }
  else {
    verificarSesion();
    var radio = $('input:radio[name=criterio]:checked').val();
    var inicio = document.getElementById("inicio").value;
    var fin = document.getElementById("fin").value;
    var motivo = document.getElementById("motivo").value;
    var codigo = document.getElementById("codigo").value;
    var nombreHsm = document.getElementById("nombreHsm").value;
    var nombreSlot = document.getElementById("nombreSlot").value;
    var nombreUsuario = document.getElementById("nombreUsuario").value;
    var empresa = document.getElementById("empresa").value;
    var nombreLlave = document.getElementById("nombreLlave").value;
    var ownerLlave = document.getElementById("ownerLlave").value;
    var versionLlave = document.getElementById("versionLlave").value;
    var nombreCert = document.getElementById("nombreCert").value;
    var ownerCert = document.getElementById("ownerCert").value;
    var versionCert = document.getElementById("versionCert").value;
    //alert('radio: '+radio+'\nmotivo: '+motivo+'\ninicio: '+inicio+'\nfin: '+fin+'\ncódigo: '+codigo+'\nnombre HSM: '+nombreHsm+'\nnombre Slot: '+nombreSlot+'\nUsuario: '+nombreUsuario+'\nempresa: '+empresa+'\nLlave: '+nombreLlave+'\nownerLlave: '+ownerLlave+'\nversion llave: '+versionLlave+'\nCert: '+nombreCert+'\nowner Cert: '+ownerCert+'\nversion Cert: '+versionCert);
    var query = '';
    var consulta = '';
    var tipoConsulta = '';
    var campos;
    var largos;
    var x;
    var validado = true;
    switch (radio) {
      case 'motivo':  if (motivo === '') {
                        alert('Debe ingresar el motivo a consultar para este tipo de búsqueda. Por favor verifique!.');
                        validado = false;
                        return false;
                      }
                      else {
                        query = "select idactividades, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha, motivo from actividades where motivo like '%"+motivo+"%'";
                        consulta = "select DATE_FORMAT(fecha, '%d/%m/%Y') as fecha, motivo from actividades where motivo like '%"+motivo+"%'";
                        campos = "Id-Fecha-Motivo";
                        largos = "1-1.5-3.5";
                        x = 55;
                        tipoConsulta = "Listado de las actividades que tienen como motivo: "+motivo;
                      }
                      break;
      case 'fecha': if ((inicio === '') && (fin === '')) {
                      alert('Debe seleccionar al menos una de las dos fechas. Por favor verifique!.');
                      validado = false;
                      return false;
                    }
                    else {
                      if (inicio === '') {
                        inicio = $("#inicio" ).attr("min");
                      }
                      if (fin === '') {
                        var temp = new Date();
                        var dia = temp.getDate();
                        var mes = temp.getMonth()+1;
                        if (dia < 10) {
                          dia = '0'+dia;
                        }                     
                        if (mes < 10) {
                          mes = '0'+mes;
                        }
                        fin = temp.getFullYear()+'-'+mes+'-'+dia;
                      }
                      //alert("inicio: "+inicio+"\nfin: "+fin);
                      if (inicio>fin) {
                        alert('Error. La fecha inicial NO puede ser mayor que la fecha final. Por favor verifique.');
                        validado = false;
                        return false;
                      }
                      else {
                        query = "select idactividades, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha, motivo from actividades where fecha>='"+inicio+"' and fecha<='"+fin+"' order by fecha";
                      }
                      consulta = "select DATE_FORMAT(fecha, '%d/%m/%Y') as fecha, motivo from actividades where fecha>='"+inicio+"' and fecha<='"+fin+"' order by fecha";
                      campos = "Id-Fecha-Motivo";
                      largos = "1-1.5-3.5";
                      x = 55;
                      var inicioMostrar = inicio.split('-').reverse().join('/');
                      var finMostrar = fin.split('-').reverse().join('/');
                      tipoConsulta = "Listado de las actividades entre las fechas: "+inicioMostrar+" y "+finMostrar;
                    }
                    break;
      case 'codigo':  if (codigo === '') {
                        alert('Debe ingresar el código a consultar para este tipo de búsqueda. Por favor verifique!.');
                        validado = false;
                        return false;
                      }
                      else {
                        query = "select idreferencias, codigo, detalles from referencias where codigo like '%"+codigo+"%'";
                        consulta = "select codigo, detalles from referencias where codigo like '%"+codigo+"%'";
                        campos = "Id-Código-Detalles";
                        largos = "1-2-4";
                        x = 45;
                        tipoConsulta = "Listado de las referencias que tienen como parte del código: "+codigo;
                      }
                      break;
      case 'slot':  if ((nombreHsm === 'ninguno') && (nombreSlot === '')) {
                      alert('Debe seleccionar uno de los HSMs y/o un nombre para este tipo de consultas. Por favor verifique.');
                      validado = false;
                      return false;
                    }
                    else {
                      var hsmMostrar = '';
                      switch (nombreHsm) {
                        case "1": hsmMostrar = "Producción";
                                break;
                        case "2": hsmMostrar = "Back Up";
                                break;
                        case "3": hsmMostrar = "Test";
                                break;
                        default: break;
                      }
                      if (nombreSlot === '') {
                        query = "select idslots, slots.nombre, slots.estado, hsm.nombre as hsm from slots inner join hsm on slots.hsm=hsm.idhsm where hsm.idhsm='"+nombreHsm+"'";
                        consulta = "select slots.nombre, hsm.nombre as hsm, slots.estado from slots inner join hsm on slots.hsm=hsm.idhsm where hsm.idhsm='"+nombreHsm+"'";
                        tipoConsulta = "Listado de los slots en el HSM de "+hsmMostrar;
                      }
                      else {
                        if (nombreHsm === 'ninguno') {
                          query = "select idslots, slots.nombre, slots.estado, hsm.nombre as hsm from slots inner join hsm on slots.hsm=hsm.idhsm where slots.nombre like '%"+nombreSlot+"%'";
                          consulta = "select slots.nombre, hsm.nombre as hsm, slots.estado from slots inner join hsm on slots.hsm=hsm.idhsm where slots.nombre like '%"+nombreSlot+"%'";
                          tipoConsulta = "Listado de los slots cuyo nombre contiene: "+nombreSlot;
                        }
                        else {
                          query = "select idslots, slots.nombre, slots.estado, hsm.nombre as hsm from slots inner join hsm on slots.hsm=hsm.idhsm where slots.nombre like '%"+nombreSlot+"%' and hsm.idhsm='"+nombreHsm+"'";
                          consulta = "select slots.nombre, hsm.nombre as hsm, slots.estado from slots inner join hsm on slots.hsm=hsm.idhsm where slots.nombre like '%"+nombreSlot+"%' and hsm.idhsm='"+nombreHsm+"'";
                          tipoConsulta = "Listado de los slots en el HSM de "+hsmMostrar+" y cuyo nombre contiene: "+nombreSlot;
                        }
                      }
                    campos = "Id-Nombre-HSM-Estado";
                    largos = "1-2-2-1";
                    x = 56;
                    }
                    break;
      case 'usuario': if ((nombreUsuario === '') && (empresa === '')){
                        alert('Debe seleccionar al menos el nombre del usuario y/o su empresa para este tipo de consulta. Por favor verifique.');
                        validado = false;
                        return false;
                      }
                      else {
                        if (nombreUsuario === '') {
                          query = "select idusuarios, nombre, apellido, empresa, estado from usuarios where empresa like '%"+empresa+"%' order by empresa, apellido";
                          consulta = "select apellido, nombre, empresa, estado from usuarios where empresa like '%"+empresa+"%' order by empresa, apellido";
                          tipoConsulta = "Listado de los usuarios cuya empresa contiene: "+empresa;
                        }
                        else {
                          if (empresa === '') {
                            query = "select idusuarios, nombre, apellido, empresa, estado from usuarios where nombre like '%"+nombreUsuario+"%' or apellido like '%"+nombreUsuario+"%' order by empresa, apellido";
                            consulta = "select apellido, nombre, empresa, estado from usuarios where nombre like '%"+nombreUsuario+"%' or apellido like '%"+nombreUsuario+"%' order by empresa, apellido";
                            tipoConsulta = "Listado de los usuarios cuyos nombres y/o apellidos contienen: "+nombreUsuario;
                          }
                          else {
                            query = "select idusuarios, nombre, apellido, empresa, estado from usuarios where empresa like '%"+empresa+"%' and (nombre like '%"+nombreUsuario+"%' or apellido like '%"+nombreUsuario+"%') order by empresa, apellido";
                            consulta = "select apellido, nombre, empresa, estado from usuarios where empresa like '%"+empresa+"%' and (nombre like '%"+nombreUsuario+"%' or apellido like '%"+nombreUsuario+"%') order by empresa, apellido";
                            tipoConsulta = "Listado de los usuarios cuya empresa contiene: "+empresa+" y cuyos nombres y/o apellidos contienen: "+nombreUsuario;
                          }
                        }
                        campos = "Id-Apellido-Nombre-Empresa-Estado";
                        largos = "1-1.5-1.5-1-2";
                        x = 45;
                      }
                      break;
      case 'llave': if ((ownerLlave === '') && (nombreLlave === '') && (versionLlave === '')) {
                      alert('Debe seleccionar al menos alguna de las tres opciones (nombre, owner o versión) para este tipo de consulta. Por favor verifique.');
                      validado = false;
                      return false;
                    }
                    else {
                      query = "select idkeys, llaves.nombre, llaves.owner, llaves.version, llaves.estado, llaves.kcv, tareas.referencia, slots.nombre as slot, hsm.nombre as hsm from llaves inner join tareas on tareas.idtareas=llaves.tarea inner join referencias on referencias.idreferencias=tareas.referencia inner join slots on slots.idslots=referencias.slot inner join hsm on hsm.idhsm=slots.hsm where ";
                      consulta = "select llaves.nombre, llaves.owner, llaves.version, llaves.kcv, slots.nombre as slot, hsm.nombre as hsm, llaves.estado from llaves inner join tareas on tareas.idtareas=llaves.tarea inner join referencias on referencias.idreferencias=tareas.referencia inner join slots on slots.idslots=referencias.slot inner join hsm on hsm.idhsm=slots.hsm where ";
                      if (nombreLlave !== '') {
                        query += "llaves.nombre like '%"+nombreLlave+"%' ";
                        consulta += "llaves.nombre like '%"+nombreLlave+"%' ";
                        tipoConsulta = "Listado de todas las llaves cuyos nombres contienen: "+nombreLlave;
                      }
                      if (ownerLlave !== '') {
                        if (nombreLlave !== '') {
                          query += "and llaves.owner like '%"+ownerLlave+"%' ";
                          consulta += "and llaves.owner like '%"+ownerLlave+"%' ";
                          tipoConsulta += ", y cuyos owners contienen: "+ownerLlave;

                        }
                        else {
                          query += "llaves.owner like '%"+ownerLlave+"%' ";
                          consulta += "llaves.owner like '%"+ownerLlave+"%' ";
                          tipoConsulta = "Listado de todas las llaves cuyos owners contienen: "+ownerLlave;
                        }
                      }
                      if (versionLlave !== '') {
                        if ((ownerLlave !== '') || (nombreLlave !== '')) {
                          query += "and llaves.version like '%"+versionLlave+"%' ";
                          consulta += "and llaves.version like '%"+versionLlave+"%' ";
                          tipoConsulta += ", y cuyas versiones contienen: "+versionLlave;
                        }
                        else {
                          query += "llaves.version like '%"+versionLlave+"%' ";
                          consulta += "llaves.version like '%"+versionLlave+"%' ";
                          tipoConsulta = "Listado de todas las llaves cuyas versiones contienen: "+versionLlave;
                        }
                      }
                      query += "order by hsm.nombre desc, slots.nombre, llaves.owner, llaves.nombre, llaves.version";
                      consulta += "order by hsm.nombre desc, slots.nombre, llaves.owner, llaves.nombre, llaves.version";
                      campos = "Id-Nombre-Owner-Versión-KCV-Slot-HSM-Estado";
                      largos = "1-1.6-1.2-1-1-1.5-1.5-1";
                      x = 18;
                    }
                    break;
      case 'cert':  if ((ownerCert === '') && (nombreCert === '') && (versionCert === '')) {
                      alert('Debe seleccionar al menos alguna de las tres opciones (nombre, owner o versión) para este tipo de consulta. Por favor verifique.');
                      validado = false;
                      return false;
                    }
                    else {
                      query = "select idcertificados, certificados.nombre, certificados.owner, certificados.version, certificados.estado, certificados.bandera, DATE_FORMAT(certificados.vencimiento, '%d/%m/%Y') as vencimiento, tareas.referencia, slots.nombre as slot, hsm.nombre as hsm from certificados inner join tareas on tareas.idtareas=certificados.tarea inner join referencias on referencias.idreferencias=tareas.referencia inner join slots on slots.idslots=referencias.slot inner join hsm on hsm.idhsm=slots.hsm where ";
                      consulta = "select certificados.nombre, certificados.owner, certificados.version, certificados.bandera, DATE_FORMAT(certificados.vencimiento, '%d/%m/%Y') as vencimiento, slots.nombre as slot, hsm.nombre as hsm, certificados.estado from certificados inner join tareas on tareas.idtareas=certificados.tarea inner join referencias on referencias.idreferencias=tareas.referencia inner join slots on slots.idslots=referencias.slot inner join hsm on hsm.idhsm=slots.hsm where ";
                      if (nombreCert !== '') {
                        query += "certificados.nombre like '%"+nombreCert+"%' ";
                        consulta += "certificados.nombre like '%"+nombreCert+"%' ";
                        tipoConsulta = "Listado de todos los certificados cuyos nombres contienen: "+nombreCert;
                      }
                      if (ownerCert !== '') {
                        if (nombreCert !== '') {
                          query += "and certificados.owner like '%"+ownerCert+"%' ";
                          consulta += "and certificados.owner like '%"+ownerCert+"%' ";
                          tipoConsulta += ", y cuyos owners contienen: "+ownerCert;
                        }
                        else {
                          query += "certificados.owner like '%"+ownerCert+"%' ";
                          consulta += "certificados.owner like '%"+ownerCert+"%' ";
                          tipoConsulta = "Listado de todos los certificados cuyos owners contienen: "+ownerCert;
                        }
                      }
                      if (versionCert !== '') {
                        if ((ownerCert !== '') || (nombreCert !== '')) {
                          query += "and certificados.version like '%"+versionCert+"%' ";
                          consulta += "and certificados.version like '%"+versionCert+"%' ";
                          tipoConsulta += ", y cuyas versiones contienen: "+versionCert;
                        }
                        else {
                          query += "certificados.version like '%"+versionCert+"%' ";
                          consulta += "certificados.version like '%"+versionCert+"%' ";
                          tipoConsulta = "Listado de todos los certificados cuyas versiones contienen: "+versionCert;
                        }
                      }
                      query += "order by hsm.nombre desc, slots.nombre, certificados.nombre, certificados.owner, certificados.version";
                      consulta += "order by hsm.nombre desc, slots.nombre, certificados.nombre, certificados.owner, certificados.version";
                      campos = "Id-Nombre-Owner-Versión-Bandera-Vencimiento-Slot-HSM-Estado";
                      largos = "0.6-1.6-1.3-1-1-1.4-1.5-1.4-1";
                      x = 8;
                    }
                    break;
      default: break;
    }
    if (validado) {
      var url = "data/selectQuery.php";
      $.getJSON(url, {query: ""+query+""}).done(function(request){
        var datos = request.resultado;
        var total = request.rows;
        if (total >= 1) {
          var divs = "<div id='fila' class='row'>\n\
                        <div id='criterios' class='col-md-5 col-sm-12'></div>\n\
                        <div id='resultado' class='col-md-7 col-sm-12'></div>\n\
                      </div>";
          var tituloCriterios = '<h2>Criterios</h2>';
          var tablaCriterios = '<table id="parametros" name="parametros" class="tabla2">';
          var tr1 = '<tr>\n\
                       <th colspan="5" class="tituloTabla">ACTIVIDADES</th>\n\
                     </tr>';
          tr1 += '<tr>\n\
                      <td><input type="radio" name="criterio" value="motivo" checked="checked"></td>\n\
                      <td>Motivo:</td><td colspan="3"><input type="text" name="motivo" id="motivo"></td>\n\
                    </tr>';
          tr1 += '<tr>\n\
                    <td><input type="radio" name="criterio" value="fecha"></td>\n\
                    <td>Entre:</td><td><input type="date" name="inicio" id="inicio" style="width:100%; text-align: center" min="2016-07-01"></td>\n\
                    <td>y:</td><td><input type="date" name="fin" id="fin" style="width:100%; text-align: center" min="2016-10-01"></td>\n\
                  </tr>';
          tr1 += '<tr>\n\
                    <th colspan="5">REFERENCIAS</th>\n\
                  </tr>';
          tr1 += '<tr>\n\
                    <td><input type="radio" name="criterio" value="codigo"></td>\n\
                    <td>Código:</td>\n\
                    <td colspan="3"><input type="text" name="codigo" id="codigo"></td>\n\
                  </tr>';
          tr1 += '<tr>\n\
                    <th colspan="5">SLOTS</th>\n\
                  </tr>';
          tr1 += '<tr>\n\
                    <td rowspan="2"><input type="radio" name="criterio" value="slot"></td>\n\
                    <td>HSM:</td>\n\
                    <td colspan="3">\n\
                      <select id="nombreHsm" name="nombreHsm" style="width:100%">\n\
                        <option value="ninguno" selected="yes">---SELECCIONAR---</option>\n\
                        <option value="1">Producción</option>\n\
                        <option value="2">Back Up</option>\n\
                        <option value="3">Test</option>\n\
                      </select>\n\
                    </td>\n\
                  </tr>';
          tr1 += '<tr>\n\
                    <td>Nombre:</td>\n\
                    <td colspan="3"><input type="text" name="nombreSlot" id="nombreSlot"></td>\n\
                  </tr>';
          tr1 += '<tr>\n\
                    <th colspan="5">USUARIOS</th>\n\
                  </tr>';
          tr1 += '<tr>\n\
                    <td rowspan="2"><input type="radio" name="criterio" value="usuario"></td>\n\
                    <td>Nombre:</td><td colspan="3"><input type="text" name="nombreUsuario" id="nombreUsuario"></td>\n\
                  </tr>';
          tr1 += '<tr>\n\
                    <td>Empresa:</td>\n\
                    <td colspan="3"><input type="text" name="empresa" id="empresa"></td>\n\
                  </tr>';
          tr1 += '<tr>\n\
                    <th colspan="5">LLAVES</th>\n\
                  </tr>';
          tr1 += '<tr>\n\
                    <td rowspan="3"><input type="radio" name="criterio" value="llave"></td>\n\
                    <td>Nombre:</td>\n\
                    <td colspan="3"><input type="text" name="nombreLlave" id="nombreLlave"></td>\n\
                  </tr>';
          tr1 += '<tr>\n\
                    <td>Owner:</td>\n\
                    <td colspan="3"><input type="text" name="ownerLlave" id="ownerLlave"></td>\n\
                  </tr>';
          tr1 += '<tr>\n\
                    <td>Versión:</td>\n\
                    <td colspan="3"><input type="text" name="versionLlave" id="versionLlave"></td>\n\
                  </tr>';
          tr1 += '<tr>\n\
                    <th colspan="5">CERTIFICADOS</th>\n\
                  </tr>';
          tr1 += '<tr>\n\
                    <td rowspan="3"><input type="radio" name="criterio" value="cert"></td>\n\
                    <td>Nombre:</td>\n\
                    <td colspan="3"><input type="text" name="nombreCert" id="nombreCert"></td>\n\
                  </tr>';
          tr1 += '<tr>\n\
                    <td>Owner:</td>\n\
                    <td colspan="3"><input type="text" name="ownerCert" id="ownerCert"></td>\n\
                  </tr>';
          tr1 += '<tr>\n\
                    <td>Versión:</td>\n\
                    <td colspan="3"><input type="text" name="versionCert" id="versionCert"></td>\n\
                  </tr>';
          tr1 += '<tr>\n\
                    <td colspan="5" class="pieTabla"><input type="button" class="btn-success" name="consultar" id="realizarBusqueda" value="Consultar"></td>\n\
                  </tr>';
          tr1 += '</table>';
          tablaCriterios += tr1;
          var cargarCriterios = '';
          cargarCriterios += tituloCriterios;
          cargarCriterios += tablaCriterios;

          var cargar = '';
          var encabezado = '<h2>Total de registros: '+total+'</h2>';
          var subencabezado = '';

          var tabla = '<form name="resultadoBusqueda" id="resultadoBusqueda" action="exportar.php" method="post" class="exportarForm">\n\
                        <table id="resultado" name="resultado" class="tabla2">';
          var tr = '';

          switch(radio) {
            case 'motivo':tr += '<tr><th colspan="3" class="tituloTabla">RESULTADO</th></tr>';
                          tr +='<tr>\n\
                                  <th>Id</th>\n\
                                  <th>Fecha</th>\n\
                                  <th>Motivo</th>\n\
                                </tr>';
                          subencabezado = '<h3>(Búsqueda de actividades según motivo)</h3>';
                          for(var index in datos) {
                            var registro = datos[index];
                            var item = parseInt(index, 10) + 1;
                            //alert("id: "+registro["idactividades"]+"\nfecha: "+registro["fecha"]+"\nmotivo: "+registro["motivo"]);
                            tr += '<tr>';
                            if (index == (total - 1)) {
                              tr += '<td>';
                            }
                            else {
                              tr += '<td>';
                            }
                            tr += item+'</td>\n\
                                  <td><a href="#titulo" class="detailActivity" id="'+registro.idactividades+'" >'+registro["fecha"]+'</a></td>';
                            if (index == (total - 1)) {
                              tr += '<td>';
                            }
                            else {
                              tr += '<td>';
                            }
                            tr += registro["motivo"]+'</td>\n\
                                 </tr>';
                          }
                          break;
            case 'fecha': tr += '<tr><th colspan="3" class="tituloTabla">RESULTADO</th></tr>';
                          tr += '<tr>\n\
                                  <th>Id</th>\n\
                                  <th>Fecha</th>\n\
                                  <th>Motivo</th>\n\
                                </tr>';
                          subencabezado = '<h3>(Búsqueda de actividades según fechas)</h3>';
                          for(var index in datos) {
                            var registro = datos[index];
                            var item = parseInt(index, 10) + 1;
                            //alert("id: "+registro["idactividades"]+"\nfecha: "+registro["fecha"]+"\nmotivo: "+registro["motivo"]);
                            tr += '<tr>';
                            if (index == (total - 1)) {
                              tr += '<td>';
                            }
                            else {
                              tr += '<td>';
                            }
                            tr +=    item+'</td>\n\
                                     <td><a href="#titulo" class="detailActivity" id="'+registro.idactividades+'" >'+registro["fecha"]+'</a></td>';
                            if (index == (total - 1)) {
                              tr += '<td>';
                            }
                            else {
                              tr += '<td>';
                            }
                            tr += registro["motivo"]+'</td>\n\
                                   </tr>';
                          }
                          break;
            case 'codigo':tr += '<tr><th colspan="3" class="tituloTabla">RESULTADO</th></tr>';
                          tr += '<tr>\n\
                                   <th>Id</th>\n\
                                   <th>Código</th>\n\
                                   <th>Detalles</th>\n\
                                 </tr>';
                          subencabezado = '<h3>(Búsqueda de referencias según códigos)</h3>';
                          for(var index in datos) {
                            var registro = datos[index];
                            var item = parseInt(index, 10) + 1;
                            //alert("id: "+registro["idactividades"]+"\nfecha: "+registro["fecha"]+"\nmotivo: "+registro["motivo"]);
                            tr += '<tr>';
                            if (index == (total - 1)) {
                              tr += '<td>';
                            }
                            else {
                              tr += '<td>';
                            }
                            tr += item+'</td>\n\
                                  <td><a href="referencia.php?idreferencia='+registro.idreferencias+'">'+registro["codigo"]+'</a></td>';
                            if (index == (total - 1)) {
                              tr += '<td>';
                            }
                            else {
                              tr += '<td>';
                            }
                            tr += registro["detalles"]+'</td>\n\
                                   </tr>';
                          } 
                          break;
            case 'slot':tr += '<tr><th colspan="4" class="tituloTabla">RESULTADO</th></tr>';
                        tr +='<tr>\n\
                                <th>Id</th>\n\
                                <th>Nombre</th>\n\
                                <th>HSM</th>\n\
                                <th>Estado</th>\n\
                              </tr>';
                        subencabezado = '<h3>(Búsqueda de slots)</h3>';
                        for(var index in datos) {
                          var registro = datos[index];
                          var item = parseInt(index, 10) + 1;
                          //alert("id: "+registro["idactividades"]+"\nfecha: "+registro["fecha"]+"\nmotivo: "+registro["motivo"]);
                          tr += '<tr>';
                          if (index == (total - 1)) {
                            tr += '<td>';
                          }
                          else {
                            tr += '<td>';
                          }
                          tr += item+'</td>\n\
                                <td><a href="#" id="'+registro["idslots"]+'" name="'+registro["nombre"]+'" class="detailSlot">'+registro["nombre"]+'</a></td>\n\
                                <td>'+registro["hsm"]+'</td>';
                          if (index == (total - 1)) {
                            tr += '<td>';
                          }
                          else {
                            tr += '<td>';
                          }
                          tr += registro["estado"]+'</td>\n\
                                </tr>';
                        }  
                        break;
            case 'usuario': tr += '<tr><th colspan="5" class="tituloTabla">RESULTADO</th></tr>';
                            tr +='<tr>\n\
                                    <th>Id</th>\n\
                                    <th>Apellido</th>\n\
                                    <th>Nombre</th>\n\
                                    <th>Empresa</th>\n\
                                    <th>Estado</th>\n\
                                  </tr>';
                            subencabezado = '<h3>(Búsqueda de usuarios)</h3>';
                            for(var index in datos) {
                              var registro = datos[index];
                              var item = parseInt(index, 10) + 1;
                              tr += '<tr>';
                              if (index == (total - 1)) {
                                tr += '<td>';
                              }
                              else {
                                tr += '<td>';
                              }
                              tr += item+'</td>\n\
                                    <td><a href="#" id="'+registro["idusuarios"]+'" class="detailUser">'+registro["apellido"]+'</a></td>\n\
                                    <td><a href="#" id="'+registro["idusuarios"]+'" class="detailUser">'+registro["nombre"]+'</a></td>\n\
                                    <td>'+registro["empresa"]+'</td>';
                              if (index == (total - 1)) {
                                tr += '<td>';
                              }
                              else {
                                tr += '<td>';
                              }
                              tr += registro["estado"]+'</td>\n\
                                    </tr>';
                            }
                          break;
            case 'llave': tr += '<tr><th colspan="8" class="tituloTabla">RESULTADO</th></tr>';
                          tr +='<tr>\n\
                                  <th>Id</th>\n\
                                  <th>Nombre</th>\n\
                                  <th>Owner</th>\n\
                                  <th>Version</th>\n\
                                  <th>KCV</th>\n\
                                  <th>HSM</th>\n\
                                  <th>Slot</th>\n\
                                  <th>Estado</th>\n\
                                </tr>';
                          subencabezado = '<h3>(Búsqueda de llaves)</h3>';
                          for(var index in datos) {
                            var registro = datos[index];
                            var item = parseInt(index, 10) + 1;
                            tr += '<tr>';
                            if (index == (total - 1)) {
                              tr += '<td>';
                            }
                            else {
                              tr += '<td>';
                            }
                            tr += item+'</td>\n\
                                  <td style="text-align: left"><a href="llave.php?idkey='+registro["idkeys"]+'&ref='+registro["referencia"]+'" id="'+registro["idkeys"]+'" class="detailObject">'+registro["nombre"]+'</a></td>\n\
                                  <td>'+registro["owner"]+'</td>\n\
                                  <td>'+registro["version"]+'</td>\n\
                                  <td>'+registro["kcv"]+'</td>\n\
                                  <td>'+registro["hsm"]+'</td>\n\
                                  <td>'+registro["slot"]+'</td>';
                            if (index == (total - 1)) {
                              tr += '<td>';
                            }
                            else {
                              tr += '<td>';
                            }
                            tr += registro["estado"]+'</td>\n\
                                  </tr>';
                          }
                          break;
            case 'cert':tr += '<tr><th colspan="9" class="tituloTabla">RESULTADO</th></tr>';
                        tr +='<tr>\n\
                                <th>Id</th>\n\
                                <th>Nombre</th>\n\
                                <th>Owner</th>\n\
                                <th>Version</th>\n\
                                <th>Bandera</th>\n\
                                <th>Vencimiento</th>\n\
                                <th>HSM</th>\n\
                                <th>Slot</th>\n\
                                <th>Estado</th>\n\
                              </tr>';
                        subencabezado = '<h3>(Búsqueda de certificados)</h3>';
                        for(var index in datos) {
                          var registro = datos[index];
                          var item = parseInt(index, 10) + 1;
                          //var temp = registro["vencimiento"].split("-");
                          //var fecha = temp[2]+"/"+temp[1]+"/"+temp[0];
                          tr += '<tr>';
                          if (index == (total - 1)) {
                            tr += '<td>';
                          }
                          else {
                            tr += '<td>';
                          }
                          tr += item+'</td>\n\
                                <td nowrap style="text-align: left"><a href="certificado.php?idkey='+registro["idcertificados"]+'&ref='+registro["referencia"]+'" id="'+registro["idcertificados"]+'" class="detailObject">'+registro["nombre"]+'</a></td>\n\
                                <td>'+registro["owner"]+'</td>\n\
                                <td>'+registro["version"]+'</td>\n\
                                <td>'+registro["bandera"]+'</td>\n\
                                <td>'+registro["vencimiento"]+'</td>\n\
                                <td>'+registro["hsm"]+'</td>\n\
                                <td>'+registro["slot"]+'</td>';
                          if (index == (total - 1)) {
                            tr += '<td>';
                          }
                          else {
                            tr += '<td>';
                          }
                          tr += registro["estado"]+'</td>\n\
                                </tr>';
                        }  
                        break;
            default: break;
          }
          tr += '<tr><td style="display:none"><input type="text" id="query" name="consulta" value="'+consulta+'"></td>\n\
                    <td style="display:none"><input type="text" id="campos" name="campos" value="'+campos+'"></td>\n\
                    <td style="display:none"><input type="text" id="largos" name="largos" value="'+largos+'"></td>\n\
                    <td style="display:none"><input type="text" id="param" name="param" value=""></td>\n\
                    <td style="display:none"><input type="text" id="tipoConsulta" name="tipoConsulta" value="'+tipoConsulta+'"></td>\n\
                    <td style="display:none"><input type="text" id="x" name="x" value="'+x+'"></td></tr>';
          tr += '<tr>\n\
                  <td colspan="9" class="pieTabla">\n\
                    <input type="button" id="6" name="exportarBusqueda" value="EXPORTAR" class="btn-info exportar">\n\
                  </td>\n\
                </tr>';
          tr += '</table></form>';
          tabla += tr;
          cargar += encabezado;
          cargar += subencabezado;
          cargar += tabla;
          $("#main-content").empty();
          $("#main-content").append(divs);
          $("#criterios").html(cargarCriterios);
          $("#resultado").html(cargar);
        }
        else {
          alert("NO existen registros que coincidan con los criterios de búsqueda establecidos. Por favor verifique.");
        }
      });
    }
    else {
      alert('NO validado');//Igualmente no llega a esta etapa dado que al no ser válida retorna falso y sale.
    }
  }
});

///Disparar función al hacer click en botón de exportar.
///Esto hace que se recupere el id que corresponde a este tipo de exportación (listado de actividades) y se 
///redirija a la página exportar.php pasando ese parámetro:
$(document).on("click", ".exportar", function (){
  //alert('Está a punto de exportar el listado con las actividades. ¿Desea continuar?.');
  ///Levanto el id que identifica lo que se va a exportar, a saber:
  /// 1- listado de actividades.
  /// 2- detalle de una actividad.
  /// 3- detalle de una referencia.
  /// 4- detalle de una llave.
  /// 5- detalle de un certificado.
  /// 6- resultado de una búsqueda.
  /// 7- detalle de un usuario.
  /// 8- detalle de un slot.
  var id = $(this).attr("id");
  ///recupero el nombre del formulario que hace el llamado (en realidad, el nombre del botón exportar que hace 
  ///referencia al form:
  var nombreFormu = $(this).attr("name");
    
  var param = "id:"+id+"";
  var x = $("#x").val();
  
  var enviarMail = confirm('¿Desea enviar por correo electrónico el pdf?');
  var continuar = true;
  if (enviarMail === true) {
    var dir = prompt('Dirección/es: (SEPARADAS POR COMAS)');
    if (dir === '') {
      alert('Error, la dirección no puede quedar vacía. Por favor verifique.');
      continuar = false;
    }
    else {
      if (dir !== null) {
        alert('Se enviará el reporte a: '+dir);
        param += "&mails:"+dir+"";
      }
      else {
        alert('Error, se debe ingresar la dirección a la cual enviar el reporte y dar aceptar.');
        continuar = false;
      }
    }
  }  
  else {
    alert('Se optó por no enviar el mail. Se sigue con el guardado en disco y muestra en pantalla.');
  }
  
  ///En base al id, veo si es necesario o no enviar parámetros:
  switch (id) {
    case "1": param += '&x:55';
              $("#paramActivity").val(param);
              break;
    case "2": var actividad = $("#activity").val();
              param += '&actividad:'+actividad+'&x:55';
              $("#paramDetail").val(param);
              break;
    case "3": var idref = $("#idref").val();
              var idslot = $("#idslot").val();
              param += '&idref:'+idref+'&idslot:'+idslot;
              $("#param").val(param);
              break;
    case "4": var idref = $("#idref").val();
              var idkey = $("#idkey").val();
              param += '&idref:'+idref+'&idkey:'+idkey;
              $("#param").val(param);
              break;
    case "5": var idref = $("#idref").val();
              var idcert = $("#idcert").val();
              param += '&idref:'+idref+'&idcert:'+idcert;
              $("#param").val(param);
              break;
    case "6": var query = $("#query").val();
              var campos = $("#campos").val();
              var largos = $("#largos").val();
              param += '&query:'+query+'&campos:'+campos+'&largos:'+largos+'&x:'+x;
              $("#param").val(param);
              break;
    case "7": var iduser = $("#iduser").val();
              param += '&iduser:'+iduser;
              $("#param").val(param);
              break;
    case "8": var idslot = $("#idslot").val();
              param += '&idslot:'+idslot;
              $("#param").val(param);
              break;
    default: break;
  } 
  //alert ($("#param").val());
  if (continuar) {
    if (nombreFormu === 'exportarActividades') {
      $("#listadoActividades").submit();
    }
    else {
      $(".exportarForm").submit();
    }
  }
});//*** fin del click .exportar ***

/**************************************************************************************************************************
/// *************************************************** FIN BÚSQUEDAS *****************************************************
***************************************************************************************************************************
*/

}

/**
 * \brief Función que envuelve todos los eventos JQUERY con sus respectivos handlers.
 */
$(document).on("ready", todo());//*** fin del ready ***
