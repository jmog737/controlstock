/**
///  \file script.js
///  \brief Archivo que contiene todas las funciones de Javascript.
///  \author Juan Martín Ortega
///  \version 1.0
///  \date Setiembre 2017
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
        $("#usuarioSesion").val(user);
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
 * @param {String} id String con el id del campo luego del cual se tienen que agregar los datos.
 * \brief Función que muestra las sugerencias de los productos disponibles.
 */
function showHint(str, id) {
  if (str.length === 0) { 
    document.getElementById("producto").innerHTML = "";
    return;
  } else {
    var url = "data/selectQuery.php";
    var query = "select idprod, entidad, nombre_plastico, codigo_emsa, bin, snapshot, stock, alarma from productos where (productos.nombre_plastico like '%"+str+"%' or productos.codigo_emsa like '%"+str+"%' or productos.bin like '%"+str+"%' or productos.entidad like '%"+str+"%') order by productos.nombre_plastico asc";
    //alert(query);
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
          var snapshot = sugerencias[i]["snapshot"];
          if ((snapshot === null)||(snapshot === '')) {
            snapshot = 'NADA';
          }
          var codigo_emsa = sugerencias[i]["codigo_emsa"];
          if ((codigo_emsa === null) || (codigo_emsa === " ")) {
            codigo_emsa = 'SIN CODIGO AÚN';
          }
          mostrar += '<option value="'+sugerencias[i]["idprod"]+'" name="'+snapshot+'" stock='+sugerencias[i]["stock"]+' alarma='+sugerencias[i]["alarma"]+'>(' + sugerencias[i]["entidad"]+') '+sugerencias[i]["nombre_plastico"] + ' (' +bin+') --'+ codigo_emsa +'--</option>';
        }
        mostrar += '</select>';
      }
      else {
        mostrar = '<p name="hint" value="">No se encontraron sugerencias!</p>';
      }
      $(id).after(mostrar);
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
  var usuarioBoveda = document.getElementById("usuarioBoveda").value;
  var usuarioGrabaciones = document.getElementById("usuarioGrabaciones").value;
  
  if (fecha === "") {
    alert('Se debe seleccionar la Fecha');
    document.getElementById("fecha").focus();
    seguir = false;
  }
  else {
    if (($("#hint").find('option:selected').val() === "NADA") || ($("#producto").val() === ''))
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
            if (usuarioBoveda === "ninguno")
              {
              alert('Se debe seleccionar al representante del sector Bóveda. Por favor verifique!.');
              document.getElementById("usuarioBoveda").focus();
              seguir = false;
            } 
            else
              {
              if (usuarioGrabaciones === "ninguno")
                {
                alert('Se debe seleccionar al representante del sector Grabaciones. Por favor verifique!.');
                document.getElementById("usuarioGrabaciones").focus();
                seguir = false;
              } 
              else
                {
                  seguir = true;
              }// usuarioGrabaciones
            }// usuarioBoveda  
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
  var usuarioBoveda = document.getElementById("usuarioBoveda").value;
  var usuarioGrabaciones = document.getElementById("usuarioGrabaciones").value;
  
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
            if (usuarioBoveda === "ninguno")
              {
              alert('Se debe seleccionar al representante del sector Bóveda. Por favor verifique!.');
              document.getElementById("usuarioBoveda").focus();
              seguir = false;
            } 
            else
              {
              if (usuarioGrabaciones === "ninguno")
                {
                alert('Se debe seleccionar al representante del sector Grabaciones. Por favor verifique!.');
                document.getElementById("usuarioGrabaciones").focus();
                seguir = false;
              } 
              else
                {
                seguir = true;
              }// usuarioGrabaciones
            }// usuarioBoveda
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
///Cambia el color de fondo para resaltarlo, carga un snapshot del plástico si está disponible, y muestra
///el stock actual.
$(document).on("change", "#hint", function (){
  var rutaFoto = 'images/snapshots/';
  var nombreFoto = $(this).find('option:selected').attr("name");
  $(this).css('background-color', '#ffffff');
  
  $("#snapshot").remove();
  $("#stock").remove();
  
  //if (nombreFoto !== "NADA"){
    var mostrar = '<img id="snapshot" name="hint" src="'+rutaFoto+nombreFoto+'" alt="No se cargó la foto aún." height="127" width="200"></img>';
    mostrar += '<p id="stock" name="hint" style="padding-top: 10px"><b>Stock actual: <b><font class="resaltado" style="font-size:1.6em">'+$("#hint").find('option:selected').attr("stock")+'</font></p>';
    $(this).css('background-color', '#efe473');
  //}
  
  $("#hint").after(mostrar);
});

///Disparar funcion al hacer clic en el botón para agregar el movimiento.
$(document).on("click", "#agregarMovimiento", function (){
  //$(this).css('background-color', '#efe473');
  var seguir = true;
  seguir = validarMovimiento();
  if (seguir) {
    var fecha = $("#fecha").val();
    var temp = fecha.split('-');
    var fechaMostrar = temp[2]+"/"+temp[1]+"/"+temp[0];
    var idProd = $("#hint").val();
    var nombreProducto = $("#hint").find('option:selected').text( );
    var stockActual = parseInt($("#hint").find('option:selected').attr("stock"), 10);
    var alarma = parseInt($("#hint").find('option:selected').attr("alarma"), 10);
    var tipo = $("#tipo").val();
    var cantidad = parseInt($("#cantidad").val(), 10);
    var comentarios = $("#comentarios").val();
    var idUserBoveda = $("#usuarioBoveda").val();
    var userBoveda = $("#usuarioBoveda").find('option:selected').attr("name");
    var idUserGrabaciones = $("#usuarioGrabaciones").val();
    var userGrabaciones = $("#usuarioGrabaciones").find('option:selected').attr("name");
    var confirmar = confirm("¿Confirma el ingreso de los siguientes datos? \n\nFecha: "+fechaMostrar+"\nProducto: "+nombreProducto+"\nTipo: "+tipo+"\nCantidad: "+cantidad+"\nControl Bóveda: "+userBoveda+"\nControl Grabaciones: "+userGrabaciones+"\nComentarios: "+comentarios);
    var tempDate = new Date();
    var hora = tempDate.getHours()+":"+tempDate.getMinutes();
    
    var nuevoStock = stockActual - cantidad;
    
    var avisarAlarma = false;
    var avisarInsuficiente = false;
    
    /// Si el nuevoStock es menor a 0, siginifica que no hay stock suficiente. Se alerta y se descuenta sólo la cantidad disponible.
    if (nuevoStock <0) {
      cantidad = stockActual;
      avisarInsuficiente = true;
      nuevoStock = 0;
    }
    else {
      if (nuevoStock < alarma) {
        avisarAlarma = true;
      }
    }
    
    if (confirmar) {
      var url = "data/updateQuery.php";
      var query = "insert into movimientos (producto, fecha, hora, tipo, cantidad, control1, control2, comentarios) values ("+idProd+", '"+fecha+"', '"+hora+"', '"+tipo+"', "+cantidad+", "+idUserBoveda+", "+idUserGrabaciones+", '"+comentarios+"')";
      //alert(document.getElementById("usuarioSesion").value); --- USUARIO QUE REGISTRA!!!
       
      $.getJSON(url, {query: ""+query+""}).done(function(request) {
        var resultado = request["resultado"];
        if (resultado === "OK") {
          var url = "data/updateQuery.php";
          var query = "update productos set stock="+nuevoStock+" where idprod="+idProd;
                    
          $.getJSON(url, {query: ""+query+""}).done(function(request) {
            var resultado = request["resultado"];
            if (resultado === "OK") {  
              if (avisarAlarma) {
                alert('El stock quedó por debajo del límite definido!. Stock actual: ' + nuevoStock);
                location.reload();
              }
              else {
                if (avisarInsuficiente) {
                  alert('Stock insuficiente!. Se descuenta sólo la cantidad existente. Stock 0!!.');
                  location.reload();
                }
                else {
                  alert('Registro agregado correctamente!. Stock actual: '+nuevoStock);
                  location.reload();
                }
              }
            }
            else {
              alert('Hubo un error. Por favor verifique.');
            }
          });
        }
        else {
          alert('Hubo un error. Por favor verifique.');
        }
      });
    }
    else {
      alert('no hacer el insert');
    }
  }
});

///Disparar funcion cuando algún elemento de la clase agrandar reciba el foco.
///Se usa para resaltar el elemento seleccionado.
$(document).on("focus", ".agrandar", function (){
  $(this).css("font-size", 28);
  $(this).css("background-color", "#e7f128");
  $(this).css("font-weight", "bolder");
  $(this).css("color", "red");
  $(this).parent().prev().prev().children().prop("checked", true);
});

///Disparar funcion cuando algún elemento de la clase agrandar pierda el foco.
///Se usa para volver al estado "normal" el elemento que dejó de estar seleccionado.
$(document).on("blur", ".agrandar", function (){
  $(this).css("font-size", "inherit");
  $(this).css("background-color", "#ffffff");
  $(this).css("font-weight", "inherit");
  $(this).css("color", "inherit");
});

/*******************************************************************************************************************************
/// ***************************************************** FIN MOVIMIENTOS ******************************************************
********************************************************************************************************************************
*/

/*****************************************************************************************************************************
/// Comienzan las funciones que manejan los eventos relacionados a las IMPORTACIONES como ser creación, edición y eliminación.
******************************************************************************************************************************
*/

///Disparar funcion al hacer clic en el botón para agregar la importación.
$(document).on("click", "#agregarImportacion", function (){
  //$(this).css('background-color', '#efe473');
  var seguir = true;
  seguir = validarImportacion();
  if (seguir) {
    var fecha = $("#fecha").val();
    var temp = fecha.split('-');
    var fechaMostrar = temp[2]+"/"+temp[1]+"/"+temp[0];
    var idProd = $("#hint").val();
    var nombreProducto = $("#hint").find('option:selected').text( );
    var stockActual = parseInt($("#hint").find('option:selected').attr("stock"), 10);
    //var alarma = parseInt($("#hint").find('option:selected').attr("alarma"), 10);
    var fabricante = $("#fabricante").val();
    var cantidad = parseInt($("#cantidad").val(), 10);
    var comentarios = $("#comentarios").val();
    var idUserBoveda = $("#usuarioBoveda").val();
    var userBoveda = $("#usuarioBoveda").find('option:selected').attr("name");
    var idUserGrabaciones = $("#usuarioGrabaciones").val();
    var userGrabaciones = $("#usuarioGrabaciones").find('option:selected').attr("name");
    var tempDate = new Date();
    var hora = tempDate.getHours()+":"+tempDate.getMinutes();
    var confirmar = confirm("¿Confirma el ingreso de los siguientes datos? \n\nFecha: "+fechaMostrar+"\nProducto: "+nombreProducto+"\nFabricante: "+fabricante+"\nCantidad: "+cantidad+"\nControl Bóveda: "+userBoveda+"\nControl Grabaciones: "+userGrabaciones+"\nComentarios: "+comentarios);
     
    var nuevoStock = stockActual + cantidad;
    
    if (confirmar) {
      var url = "data/updateQuery.php";
      var query = "insert into movimientos (producto, fecha, hora, fabricante, tipo, cantidad, control1, control2, comentarios) values ("+idProd+", '"+fecha+"', '"+hora+"', '"+fabricante+"', 'Importación', "+cantidad+", "+idUserBoveda+", "+idUserGrabaciones+", '"+comentarios+"')";
      //alert(document.getElementById("usuarioSesion").value); --- USUARIO QUE REGISTRA!!!
      
      $.getJSON(url, {query: ""+query+""}).done(function(request) {
        var resultado = request["resultado"];
        if (resultado === "OK") {
          var url = "data/updateQuery.php";
          var query = "update productos set stock="+nuevoStock+" where idprod="+idProd;
                    
          $.getJSON(url, {query: ""+query+""}).done(function(request) {
            var resultado = request["resultado"];
            if (resultado === "OK") {  
              alert('Registro agregado correctamente!. Stock actual: '+nuevoStock);
              location.reload();
            }
            else {
              alert('Hubo un error (update). Por favor verifique.');
            }
          });
        }
        else {
          alert('Hubo un error. Por favor verifique.');
        }
      });
    }
    else {
      alert('no hacer el insert');
    }
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
    window.location.href = "http://localhost/consultastock/index.php";
  }
  else {
    verificarSesion();
    var radio = $('input:radio[name=criterio]:checked').val();
    var inicio = document.getElementById("inicio").value;
    var fin = document.getElementById("fin").value;
    var entidadStock = $("#entidadStock").find('option:selected').val( );
    var entidadMovimiento = document.getElementById("entidadMovimiento").value;
    var idProd = $("#hint").val();
    var nombreProducto = $("#hint").find('option:selected').text( );
    var tipo = $("#tipo").find('option:selected').val( );
    var idUser = $("#usuario").val();
    var nombreUsuario = $("#usuario").find('option:selected').text( );
    var radioFecha = $('input:radio[name=criterioFecha]:checked').val();
    var mes = $("#mes").val();
    var año = $("#año").val();
    var rangoFecha = null;
    
    var query = 'select productos.entidad, productos.nombre_plastico, productos.bin, productos.codigo_emsa, productos.snapshot, productos.stock, productos.alarma';
    var tipoConsulta = '';
    var mensajeFecha = '';
    var consulta = query;
    var campos;
    var largos;
    var mostrarCampos = "1-1-1-1-1-1-1-0";;
    var x = 55;
    
    var validado = true;
    var validarFecha = false;
    var validarTipo = false;
    var validarUser = false;
    var ordenFecha = false;
    var rutaFoto = 'images/snapshots/';
    
    switch (radio) {
      case 'entidadStock': if (entidadStock !== 'todos') {
                             query += " from productos where entidad='"+entidadStock+"'";
                             tipoConsulta = 'del stock de '+entidadStock;
                           } 
                           else {
                             query += ' from productos';
                             tipoConsulta = 'del stock de todas las entidades';
                           }
                           campos = "Id-Entidad-Nombre-BIN-C&oacute;digo-Snapshot-Stock";
                           largos = "0.8-1.2-2.5-0.8-2-1-1";
                           x = 20;
                           break;
      case 'productoStock':  if ((idProd === 'NADA') || (nombreProducto === '')){
                               alert('Debe seleccionar un producto. Por favor verifique.');
                               document.getElementById("productoStock").focus();
                               validado = false;
                               return false;
                             }
                             else {
                               query += " from productos where idProd="+idProd;
                             }
                             tipoConsulta = 'de stock del producto '+nombreProducto;
                             campos = "Id-Entidad-Nombre-BIN-C&oacute;digo-Snapshot-Stock";
                             largos = "0.8-1.2-2.5-0.8-2-1-1";
                             x = 40;
                             break;
      case 'totalStock':  query = 'select entidad, sum(stock) as subtotal from productos group by entidad';
                          tipoConsulta = 'del total de plásticos en bóveda';
                          campos = 'Id-Entidad-Stock';
                          largos = '1-3.0-1';
                          x = 65;
                          break;                    
      case 'entidadMovimiento': query += ", movimientos.fecha, movimientos.hora, movimientos.cantidad, movimientos.tipo, movimientos.comentarios from productos inner join movimientos on productos.idprod=movimientos.producto where ";
                                if (entidadMovimiento !== 'todos') {
                                  query += "entidad='"+entidadMovimiento+"' and ";
                                  tipoConsulta = 'de los movimientos de '+entidadMovimiento;
                                } 
                                else {
                                  tipoConsulta = 'de los movimientos de todas las entidades';
                                }
                                validarFecha = true;
                                validarTipo = true;
                                validarUser = true;
                                ordenFecha = true;
                                campos = 'Id-Fecha-Hora-Entidad-Nombre-BIN-C&oacute;digo-Snapshot-Tipo-Cantidad-Comentarios';
                                largos = '1-1.5-1-1.5-2-1-1.5-2-1.5-1-3';
                                x = 40;
                                break;                       
      case 'productoMovimiento':  query += ", movimientos.fecha, movimientos.hora, movimientos.cantidad, movimientos.tipo, movimientos.comentarios from productos inner join movimientos on productos.idprod=movimientos.producto where ";
                                  if ((idProd === 'NADA') || (nombreProducto === '')){
                                    alert('Debe seleccionar un producto. Por favor verifique.');
                                    document.getElementById("productoMovimiento").focus();
                                    validado = false;
                                    return false;
                                  }
                                  else {
                                    query += "idProd="+idProd+' and ';
                                    validarFecha = true;
                                    validarTipo = true;
                                    validarUser = true;
                                    ordenFecha = true;
                                  }
                                  tipoConsulta = 'de los movimientos del producto '+nombreProducto;
                                  campos = 'Id-Fecha-Hora-Entidad-Nombre-BIN-C&oacute;digo-Snapshot-Tipo-Cantidad-Comentarios';
                                  largos = '1-1.5-1-1.5-2-1-1.5-2-1.5-1-3';
                                  break;
      default: break;
    }
    
    if (validarFecha) {
      if (radioFecha === 'intervalo') {
        ///Comienzo la validación de las fechas:  
        if ((inicio === '') && (fin === '')) 
          {
          alert('Debe seleccionar al menos una de las dos fechas. Por favor verifique!.');
          document.getElementById("inicio").focus();
          validado = false;
          return false;
        }
        else 
          {
          if (inicio === '') 
            {
            inicio = $("#inicio" ).attr("min");
            }
          if (fin === '') 
            {
            var temp = new Date();
            var dia = temp.getDate();
            var mes = temp.getMonth()+1;
            if (dia < 10) 
              {
              dia = '0'+dia;
            }                     
            if (mes < 10) 
              {
              mes = '0'+mes;
            }
            fin = temp.getFullYear()+'-'+mes+'-'+dia;
          }
          //alert("inicio: "+inicio+"\nfin: "+fin);
          if (inicio>fin) 
            {
            alert('Error. La fecha inicial NO puede ser mayor que la fecha final. Por favor verifique.');
            validado = false;
            return false;
          }
          else 
            {
            validado = true;
            var inicioTemp = inicio.split('-');
            var inicioMostrar = inicioTemp[2]+"/"+inicioTemp[1]+"/"+inicioTemp[0];
            var finTemp = fin.split('-');
            var finMostrar = finTemp[2]+"/"+finTemp[1]+"/"+finTemp[0];
            rangoFecha = "(fecha >='"+inicio+"') and (fecha<='"+fin+"')";
            mensajeFecha = "entre las fechas: "+inicioMostrar+" y "+finMostrar;
          }
        } /// FIN validación de las fechas.
      }  
      else {
        if (mes === 'todos') {
          inicio = año+"-01-01";
          fin = año+"-12-31";
          mensajeFecha = "del año "+año;
        }
        else {
          inicio = año+"-"+mes+"-01";
          var mesSiguiente = parseInt(mes, 10) + 1;
          fin = año+"-"+mesSiguiente+"-01";
          var mesMostrar = '';
          switch (mes) {
            case '01': mesMostrar = "Enero";
                       break;
            case '02': mesMostrar = "Febrero";
                       break;
            case '03': mesMostrar = "Marzo";
                       break;
            case '04': mesMostrar = "Abril";
                       break;
            case '05': mesMostrar = "Mayo";
                       break;
            case '06': mesMostrar = "Junio";
                       break;
            case '07': mesMostrar = "Julio";
                       break;
            case '08': mesMostrar = "Agosto";
                       break;
            case '09': mesMostrar = "Setiembre";
                       break;
            case '10': mesMostrar = "Octubre";
                       break;
            case '11': mesMostrar = "Noviembre";
                       break;
            case '12': mesMostrar = "Diciembre";
                       break;
            default: break;         
          }
          mensajeFecha = "del mes de "+mesMostrar+" de "+año;
        }
        validado = true;
        rangoFecha = "(fecha >='"+inicio+"') and (fecha<'"+fin+"')";
      }
      query += rangoFecha;
    }
    
    
    if (validado) 
      {
      var mensajeTipo = null;  
      if (validarTipo) {    
        if (tipo !== 'todos') {
          query += " and tipo='"+tipo+"'";
          mensajeTipo = " del tipo "+tipo;
        }
        else {
          mensajeTipo = " de todos los tipos";
        };
      }
      
      var mensajeUsuario = null;
      if (validarUser) {
        if (idUser !== 'todos') {
          query += " and (control1="+idUser+" or control2="+idUser+")";
          mensajeUsuario = " en los que está involucrado el usuario "+nombreUsuario;
        }
      }
      
      if (ordenFecha) {
        query += " order by fecha desc, hora desc, entidad asc, nombre_plastico asc, idprod";
      }
      else {
        query += " order by entidad asc, nombre_plastico asc, idprod asc";
      }
      
      var mensajeConsulta = "<h3>Consulta "+tipoConsulta;
      if (mensajeTipo !== null) {
        mensajeConsulta += " "+mensajeTipo;
      }
      mensajeConsulta += " "+mensajeFecha;
      if (mensajeUsuario !== null) {
        mensajeConsulta += mensajeUsuario;
      }
      mensajeConsulta += "</h3>";
      var mostrar = "<h2>Resultado de la búsqueda</h2>";
      mostrar += mensajeConsulta;

      var url = "data/selectQuery.php";
      
      $.getJSON(url, {query: ""+query+""}).done(function(request){
        var datos = request.resultado;
        var totalDatos = request.rows;
        
        if (totalDatos >= 1) 
          {
          $("#main-content").empty();  
          var tabla = '<form name="resultadoBusqueda" id="resultadoBusqueda" action="exportar.php" method="post" class="exportarForm">';
          tabla += '<table name="producto" class="tabla2">';
          switch(radio) {
            case 'entidadStock':  tabla += '<tr><th class="tituloTabla" colspan="7">CONSULTA DE STOCK</th></tr>';
                                  tabla += '<tr>\n\
                                              <th>Item</th>\n\
                                              <th>Entidad</th>\n\
                                              <th>Nombre</th>\n\
                                              <th>BIN</th>\n\
                                              <th>Código</th>\n\
                                              <th>Snapshot</th>\n\
                                              <th>Stock</th>\n\
                                           </tr>';
                                  var indice = 1;
                                  var total = 0;
                                  for (var i in datos) { 
                                    var entidad = datos[i]["entidad"];
                                    var nombre = datos[i]['nombre_plastico'];
                                    var bin = datos[i]['bin'];
                                    var snapshot = datos[i]['snapshot'];
                                    var codigo_emsa = datos[i]['codigo_emsa'];
                                    var stock = parseInt(datos[i]['stock'], 10);
                                    var alarma = parseInt(datos[i]['alarma'], 10);
                                    var claseResaltado = '';
                                    if (stock <= alarma) {
                                      claseResaltado = "alarma";
                                    }
                                    else {
                                      claseResaltado = "resaltado";
                                    }
                                    if ((bin === 'SIN BIN')||(bin === null)) 
                                      {
                                      bin = 'N/D o N/C';
                                    }
                                    tabla += '<tr>\n\
                                                <td>'+indice+'</td>\n\
                                                <td style="text-align: left">'+entidad+'</td>\n\
                                                <td>'+nombre+'</td>\n\
                                                <td>'+bin+'</td>\n\
                                                <td>'+codigo_emsa+'</td>\n\
                                                <td><img id="snapshot" name="hint" src="'+rutaFoto+snapshot+'" alt="No se cargó aún." height="76" width="120"></img></td>\n\
                                                <td class="'+claseResaltado+'">'+stock+'</td>\n\
                                              </tr>';
                                    indice++;
                                    total += stock;
                                  }
                                  tabla += '<tr><th colspan="6">TOTAL:</th><td class="resaltado1">'+total+'</td></tr>';
                                  tabla += '<tr><td style="display:none"><input type="text" id="query" name="consulta" value="'+query+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="campos" name="campos" value="'+campos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="largos" name="largos" value="'+largos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="param" name="param" value=""></td>\n\
                                                    <td style="display:none"><input type="text" id="entidad" value="'+entidadStock+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="mostrar" value="'+mostrarCampos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="tipoConsulta" name="tipoConsulta" value="'+mensajeConsulta+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="x" name="x" value="'+x+'"></td>\n\
                                                  </tr>';
                                  tabla += '<tr>\n\
                                              <th class="pieTabla" colspan="7">\n\
                                                <input type="button" id="1" name="exportarBusqueda" value="EXPORTAR" class="btn-info exportar">\n\
                                              </th>\n\
                                            </tr>\n\
                                          </table>\n\
                                        </form>';
                                  break;
            case 'productoStock': var bin = datos[0]['bin'];
                                  var produ = datos[0]["idProd"];
                                  if ((bin === 'SIN BIN')||(bin === null)) 
                                      {
                                      bin = 'N/D o N/C';
                                    }
                                  var alarma = parseInt(datos[0]['alarma'], 10);
                                  var stock = parseInt(datos[0]['stock'], 10);
                                  var snapshot = datos[0]['snapshot'];
                                  var claseResaltado = '';
                                  if (stock <= alarma) {
                                    claseResaltado = "alarma";
                                  }
                                  else {
                                    claseResaltado = "resaltado";
                                  }  
                                  tabla += '<tr>\n\
                                              <th colspan="2" class="tituloTabla">DETALLES</th>\n\
                                           </tr>';                       
                                  tabla += '<tr><th>Nombre:</th><td>'+datos[0]['nombre_plastico']+'</td></tr>';
                                  tabla += '<tr><th>Entidad:</th><td>'+datos[0]['entidad']+'</td></tr>';
                                  tabla += '<tr><th>C&oacute;digo:</th><td>'+datos[0]['codigo_emsa']+'</td></tr>';
                                  tabla += '<tr><th>BIN:</th><td>'+bin+'</td></tr>';
                                  tabla += '<tr><th>Snapshot:</th><td><img id="snapshot" name="hint" src="'+rutaFoto+snapshot+'" alt="No se cargó aún." height="125" width="200"></img></td></tr>';
                                  tabla += '<tr><th>Stock:</th><td class="'+claseResaltado+'">'+stock+'</td></tr>';
                                  tabla += '<tr><td style="display:none"><input type="text" id="query" name="consulta" value="'+query+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="campos" name="campos" value="'+campos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="largos" name="largos" value="'+largos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="param" name="param" value=""></td>\n\
                                                    <td style="display:none"><input type="text" id="idProd" name="largos" value="'+produ+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="mostrar" value="'+mostrarCampos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="tipoConsulta" name="tipoConsulta" value="'+tipoConsulta+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="x" name="x" value="'+x+'"></td>\n\
                                                  </tr>';
                                  tabla += '<tr>\n\
                                              <th class="pieTabla" colspan="2">\n\
                                                <input type="button" id="2" name="exportarBusqueda" value="EXPORTAR" class="btn-info exportar">\n\
                                              </th>\n\
                                            </tr>\n\
                                          </table>\n\
                                        </form>';
                                  break;
            case 'totalStock':  tabla += '<tr>\n\
                                            <th colspan="3" class="tituloTabla">DETALLES</th>\n\
                                          </tr>';
                                tabla += '<tr>\n\
                                              <th>Item</th>\n\
                                              <th>Entidad</th>\n\
                                              <th>Stock</th>\n\
                                           </tr>';          
                                var indice = 1;
                                var total = 0;
                                for (var i in datos) { 
                                  //var produ = datos[i]["idProd"];
                                  var entidad = datos[i]["entidad"];
                                  //var nombre = datos[i]['nombre_plastico'];
                                  var bin = datos[i]['bin'];
                                  var codigo_emsa = datos[i]['codigo_emsa'];
                                  var stock = datos[i]['stock'];
                                  var subtotal = parseInt(datos[i]['subtotal'], 10);
                                  if ((bin === 'SIN BIN')||(bin === null)) 
                                    {
                                    bin = 'N/D o N/C';
                                  }
                                  tabla += '<tr>\n\
                                              <td>'+indice+'</td>\n\
                                              <td style="text-align: left">'+entidad+'</td>\n\
                                              <td class="resaltado">'+subtotal+'</td>\n\
                                            </tr>';
                                  indice++;  
                                  total += subtotal;
                                }
                                tabla += '<tr><th colspan="2">TOTAL:</th><td class="resaltado1">'+total+'</td></tr>';
                                tabla += '<tr><td style="display:none"><input type="text" id="query" name="consulta" value="'+query+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="campos" name="campos" value="'+campos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="largos" name="largos" value="'+largos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="param" name="param" value=""></td>\n\
                                                    <td style="display:none"><input type="text" id="tipoConsulta" name="tipoConsulta" value="'+tipoConsulta+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="x" name="x" value="'+x+'"></td>\n\
                                                  </tr>';
                                tabla += '<tr>\n\
                                            <th class="pieTabla" colspan="3">\n\
                                              <input type="button" id="3" name="exportarBusqueda" value="EXPORTAR" class="btn-info exportar">\n\
                                            </th>\n\
                                          </tr>\n\
                                        </table>\n\
                                      </form>';              
                                break;
            case 'entidadMovimiento': tabla += '<tr><th class="tituloTabla" colspan="11">CONSULTA DE MOVIMIENTOS</th></tr>';
                                      tabla += '<tr>\n\
                                                  <th>Item</th>\n\
                                                  <th>Fecha</th>\n\
                                                  <th>Hora</th>\n\
                                                  <th>Entidad</th>\n\
                                                  <th>Nombre</th>\n\
                                                  <th>BIN</th>\n\
                                                  <th>Código</th>\n\
                                                  <th>Snapshot</th>\n\
                                                  <th>Tipo</th>\n\
                                                  <th>Cantidad</th>\n\
                                                  <th>Comentarios</th>\n\
                                               </tr>';
                                      var indice = 1;
                                      for (var i in datos) { 
                                        //var produ = datos[i]["idProd"];
                                        var entidad = datos[i]["entidad"];
                                        var nombre = datos[i]['nombre_plastico'];
                                        var bin = datos[i]['bin'];
                                        var codigo_emsa = datos[i]['codigo_emsa'];
                                        var tipo1 = datos[i]['tipo'];
                                        var snapshot = datos[i]['snapshot'];
                                        var fecha = datos[i]['fecha'];
                                        var fechaTemp = fecha.split('-');
                                        var fechaMostrar = fechaTemp[2]+"/"+fechaTemp[1]+"/"+fechaTemp[0];
                                        var hora = datos[i]["hora"];
                                        var horaTemp = hora.split(':');
                                        var horaMostrar = horaTemp[0]+":"+horaTemp[1];      
                                        var cantidad = datos[i]['cantidad'];
                                        var alarma = parseInt(datos[i]['alarma'], 10);
                                        var stock = parseInt(datos[i]['stock'], 10);
                                        var claseResaltado = '';
                                        if (stock <= alarma) {
                                          claseResaltado = "alarma";
                                        }
                                        else {
                                          claseResaltado = "resaltado";
                                        }  
                                        var comentarios = datos[i]['comentarios'];
                                        if ((comentarios === "undefined")||(comentarios === null)) {
                                            comentarios = "";
                                          }
                                        if ((bin === 'SIN BIN')||(bin === null)) 
                                          {
                                          bin = 'N/D o N/C';
                                        }
                                        tabla += '<tr>\n\
                                                    <td>'+indice+'</td>\n\
                                                    <td>'+fechaMostrar+'</td>\n\
                                                    <td>'+horaMostrar+'</td>\n\
                                                    <td>'+entidad+'</td>\n\
                                                    <td>'+nombre+'</td>\n\
                                                    <td>'+bin+'</td>\n\
                                                    <td>'+codigo_emsa+'</td>\n\
                                                    <td><img id="snapshot" name="hint" src="'+rutaFoto+snapshot+'" alt="No se cargó aún." height="75" width="120"></img></td>\n\
                                                    <td>'+tipo1+'</td>\n\
                                                    <td class="'+claseResaltado+'">'+cantidad+'</td>\n\
                                                    <td>'+comentarios+'</td>\n\
                                                  </tr>';
                                        indice++;  
                                        }
                                      tabla += '<tr><td style="display:none"><input type="text" id="query" name="consulta" value="'+query+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="campos" name="campos" value="'+campos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="largos" name="largos" value="'+largos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="param" name="param" value=""></td>\n\
                                                    <td style="display:none"><input type="text" id="tipo" name="tipo" value="'+tipo+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="usuario" name="usuario" value="'+idUser+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="mostrar" value="'+mostrarCampos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="criterioFecha" name="criterioFecha" value="'+radioFecha+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="inicio" name="inicio" value="'+inicio+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="fin" name="fin" value="'+fin+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="mes" name="mes" value="'+mes+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="año" name="año" value="'+año+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="entidad" value="'+entidadMovimiento+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="tipoConsulta" name="tipoConsulta" value="'+tipoConsulta+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="x" name="x" value="'+x+'"></td>\n\
                                                  </tr>';
              
                                      tabla += '<tr>\n\
                                                  <th class="pieTabla" colspan="11">\n\
                                                    <input type="button" id="4" name="exportarBusqueda" value="EXPORTAR" class="btn-info exportar">\n\
                                                  </th>\n\
                                                </tr>\n\
                                              </table>\n\
                                            </form>';  
                                      break;
            case 'productoMovimiento':  var bin = datos[0]['bin'];
                                        var produ = datos[0]['produ'];
                                        if ((bin === 'SIN BIN')||(bin === null)) 
                                            {
                                            bin = 'N/D o N/C';
                                          }
                                        var snapshot = datos[0]['snapshot'];  
                                        tabla += '<tr>\n\
                                                    <th colspan="2" class="tituloTabla">PRODUCTO</th>\n\
                                                 </tr>';                       
                                        tabla += '<tr><th>Nombre:</th><td>'+datos[0]['nombre_plastico']+'</td></tr>';
                                        tabla += '<tr><th>Entidad:</th><td>'+datos[0]['entidad']+'</td></tr>';
                                        tabla += '<tr><th>C&oacute;digo:</th><td>'+datos[0]['codigo_emsa']+'</td></tr>';
                                        tabla += '<tr><th>BIN:</th><td>'+bin+'</td></tr>';
                                        tabla += '<tr><th>Snapshot:</th><td><img id="snapshot" name="hint" src="'+rutaFoto+snapshot+'" alt="No se cargó aún." height="125" width="200"></img></td></tr>';
                                        tabla += '<tr><th>Stock:</th><td class="resaltado">'+datos[0]['stock']+'</td></tr>';
                                        tabla += '<tr><th colspan="2" class="pieTabla">FIN</th></tr></table>';
                                        
                                        tabla += '<br>';
                                        tabla += '<table name="movimientos" class="tabla2">';
                                        tabla += '<tr><th class="tituloTabla" colspan="6">CONSULTA DE MOVIMIENTOS</th></tr>';
                                        tabla += '<tr>\n\
                                                    <th>Item</th>\n\
                                                    <th>Fecha</th>\n\
                                                    <th>Hora</th>\n\
                                                    <th>Tipo</th>\n\
                                                    <th>Cantidad</th>\n\
                                                    <th>Comentarios</th>\n\
                                                 </tr>';
                                        var indice = 1;
                                        for (var i in datos) { 
                                          var tipo2 = datos[i]['tipo'];
                                          var fecha = datos[i]['fecha'];
                                          var fechaTemp = fecha.split('-');
                                          var fechaMostrar = fechaTemp[2]+"/"+fechaTemp[1]+"/"+fechaTemp[0];
                                          var hora = datos[i]["hora"];
                                          var horaTemp = hora.split(':');
                                          var horaMostrar = horaTemp[0]+":"+horaTemp[1];
                                          var cantidad = datos[i]['cantidad'];
                                          var alarma = parseInt(datos[i]['alarma'], 10);
                                          var stock = parseInt(datos[i]['stock'], 10);
                                          var claseResaltado = '';
                                          if (stock <= alarma) {
                                            claseResaltado = "alarma";
                                          }
                                          else {
                                            claseResaltado = "resaltado";
                                          }  
                                          var comentarios = datos[i]['comentarios'];
                                          if ((comentarios === "undefined")||(comentarios === null)) {
                                            comentarios = "";
                                          }
                                          tabla += '<tr>\n\
                                                      <td>'+indice+'</td>\n\
                                                      <td>'+fechaMostrar+'</td>\n\
                                                      <td>'+horaMostrar+'</td>\n\
                                                      <td>'+tipo2+'</td>\n\
                                                      <td class="'+claseResaltado+'">'+cantidad+'</td>\n\
                                                      <td>'+comentarios+'</td>\n\
                                                    </tr>';
                                          indice++;  
                                          }
                                        tabla += '<tr><td style="display:none"><input type="text" id="query" name="consulta" value="'+query+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="campos" name="campos" value="'+campos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="largos" name="largos" value="'+largos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="param" name="param" value=""></td>\n\
                                                    <td style="display:none"><input type="text" id="tipo" name="tipo" value="'+tipo+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="mostrar" value="'+mostrarCampos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="criterioFecha" name="criterioFecha" value="'+radioFecha+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="inicio" name="inicio" value="'+inicio+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="fin" name="fin" value="'+fin+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="mes" name="mes" value="'+mes+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="año" name="año" value="'+año+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="usuario" name="usuario" value="'+idUser+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="idProd" name="largos" value="'+produ+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="tipoConsulta" name="tipoConsulta" value="'+tipoConsulta+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="x" name="x" value="'+x+'"></td>\n\
                                                  </tr>';
              
                                        tabla += '<tr>\n\
                                                    <th class="pieTabla" colspan="6">\n\
                                                      <input type="button" id="5" name="exportarBusqueda" value="EXPORTAR" class="btn-info exportar">\n\
                                                    </th>\n\
                                                  </tr>\n\
                                                </table>\n\
                                              </form>';
                                        break;
            default: break;
          }   
        }/// FIN del if de totalDatos>1  
        else {
          alert("NO existen registros que coincidan con los criterios de búsqueda establecidos. Por favor verifique.");
          return false;
        }                     
        
        mostrar += "<h3>Total de registros afectados: <font class='naranja'>"+totalDatos+"</font></h3>";
        mostrar += tabla;
        var volver = '<br><a href="#" name="volver" id="volverBusqueda" onclick="location.reload()">Volver</a>';
        mostrar += volver;
        $("#main-content").append(mostrar);
      });    
    }/// Fin del IF de validado
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
  /// 1- stock por entidad.
  /// 2- stock por producto.
  /// 3- stock de plásticos en bóveda.
  /// 4- movimientos por enttidad.
  /// 5- movimientos por producto.
  var id = $(this).attr("id");
  var x = $("#x").val();
  var query = $("#query").val();
  var largos = $("#largos").val();
  var campos = $("#campos").val();
  var mostrar = $("#mostrar").val();
    
  var param = "id:"+id+"&x:"+x+"&largos:"+largos+"&campos:"+campos+"&query:"+query+"&mostrar:"+mostrar;
  var criterioFecha = $("#criterioFecha").val();
  if (criterioFecha === 'intervalo') {
    var inicio = $("#inicio").val();
    var fin = $("#fin").val();
  }
  else {
    var mes = $("#mes").val();
    var año = $("#año").val();
  }
  var entidad = $("#entidad").val();
  var idProd = $("#idProd").val();
  var tipo = $("#tipo").val();
  var usuario = $("#usuario").val();
  
  var continuar = true;
  /*
  var enviarMail = confirm('¿Desea enviar por correo electrónico el pdf?');
  
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
  */
 
  ///En base al id, veo si es necesario o no enviar parámetros:
  switch (id) {
    case "1": param += '&entidad:'+entidad;
              break;
    case "2": param += '&idProd:'+idProd;
              break;
    case "3": break;
    case "4": param += '&entidad:'+entidad+'&tipo:'+tipo+'&usuario:'+usuario;
              if (criterioFecha === 'intervalo') {
                param += '&inicio:'+inicio+'&fin:'+fin;
              }
              else {
                param += '&mes:'+mes+'&año:'+año;
              }
              break;
    case "5": param += '&entidad:'+entidad+'&tipo:'+tipo+'&usuario:'+usuario;
              if (criterioFecha === 'intervalo') {
                param += '&inicio:'+inicio+'&fin:'+fin;
              }
              else {
                param += '&mes:'+mes+'&año:'+año;
              }              
              break;
    default: break;
  }
  $("#param").val(param);alert($("#param").val());
  if (continuar) {
      $(".exportarForm").submit();
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
