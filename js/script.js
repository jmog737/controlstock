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

/**
 * \brief Función que chequea las variables de sesión para saber si la misma aún está activa o si ya expiró el tiempo.
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
 * @param {String} seleccionado String que indica, si es que es disitinto de nulo, el producto seleccionado.
 * \brief Función que muestra las sugerencias de los productos disponibles.
 */
function showHint(str, id, seleccionado) {
  if (str.length === 0) { 
    $("#hint").remove();
    $("#snapshot").remove();
    $("#stock").remove();
    document.getElementById("producto").innerHTML = "";
    return;
  } else {
    var url = "data/selectQuery.php";
    var query = "select idprod, entidad, nombre_plastico, codigo_emsa, codigo_origen, bin, snapshot, stock, alarma1, alarma2 from productos where (productos.nombre_plastico like '%"+str+"%' or productos.codigo_emsa like '%"+str+"%' or productos.codigo_origen like '%"+str+"%' or productos.bin like '%"+str+"%' or productos.entidad like '%"+str+"%') and estado='activo' order by productos.entidad asc, productos.nombre_plastico asc";
    //alert(query);
    $.getJSON(url, {query: ""+query+""}).done(function(request) {
      var sugerencias = request.resultado;
      var totalSugerencias = request.rows;
      $("[name='hint']").remove();
      
      var mostrar = '';
      
      if (totalSugerencias >= 1) {
        mostrar = '<select name="hint" id="hint" class="hint" tabindex="2">';
        if (totalSugerencias > 1) {
          mostrar += '<option value="NADA" name="NADA" selected>--Seleccionar--</option>';
        }
        for (var i in sugerencias) {
          var bin = sugerencias[i]["bin"];
          if ((bin === null)||(bin === '')) {
            bin = 'SIN BIN';
          }
          var snapshot = sugerencias[i]["snapshot"];
          if ((snapshot === null)||(snapshot === '')) {
            snapshot = 'NADA';
          }
          var codigo_emsa = sugerencias[i]["codigo_emsa"];
          if ((codigo_emsa === null) || (codigo_emsa === "")) {
            codigo_emsa = 'SIN CODIGO AÚN';
          }
          var sel = "";
          if (parseInt(sugerencias[i]["idprod"], 10) === parseInt(seleccionado, 10)) {
            sel = 'selected="yes"';
          }
          //mostrar += '<option value="'+sugerencias[i]["idprod"]+'" name="'+snapshot+'" stock='+sugerencias[i]["stock"]+' alarma='+sugerencias[i]["alarma"]+' '+sel+ '>[' + sugerencias[i]["entidad"]+'] '+sugerencias[i]["nombre_plastico"] + ' {' +bin+'} --'+ codigo_emsa +'--</option>';
          mostrar += '<option value="'+sugerencias[i]["idprod"]+'" name="'+snapshot+'" stock='+sugerencias[i]["stock"]+' alarma1='+sugerencias[i]["alarma1"]+' alarma2='+sugerencias[i]["alarma2"]+' '+sel+ '>[' + sugerencias[i]["entidad"]+': '+codigo_emsa+'] --- '+sugerencias[i]["nombre_plastico"] + '</option>';
        }
        mostrar += '</select>';
      }
      else {
        mostrar = '<p name="hint" value="">No se encontraron sugerencias!</p>';
      }
      $(id).after(mostrar);
      
      /// Agregado a pedido de Diego para que se abra el select automáticamente:
      var length = $('#hint> option').length;
      if (length > 10) {
        length = 10;
      }
      else {
        length++;
      }
      //open dropdown
      $("#hint").attr('size',length);
      
      if (seleccionado !== '')
        {
        $("#hint").focus();
      }
      else {
        
        switch(id) {
          case '#producto': $("#producto").focus();
                            break;
          case '#productoStock': $("#productoStock").focus();
            break;
          case '#productoMovimiento': $("#productoMovimiento").focus(); 
            break;
          default: break;  
        }     
      }
      
      if (totalSugerencias === 1){
        $("#cantidad").focus();
      }      
    });
  }
}

/**
 * 
 * @param {String} str String con la cadena de texto a buscar como parte del producto.
 * @param {String} id String con el id del campo luego del cual se tienen que agregar los datos.
 * @param {String} seleccionado String que indica, si es que es disitinto de nulo, el producto seleccionado.
 * \brief Función que muestra las sugerencias de los productos disponibles.
 */
function showHintProd(str, id, seleccionado) {
  if (str.length === 0) { 
    $("#hintProd").remove();
    $("#snapshot").remove();
    $("#stock").remove();
    document.getElementById("producto").innerHTML = "";
    return;
  } else {
    var url = "data/selectQuery.php";
    var query = "select idprod, entidad, nombre_plastico, codigo_emsa, codigo_origen, bin, snapshot, stock, alarma1, alarma2 from productos where (productos.nombre_plastico like '%"+str+"%' or productos.codigo_emsa like '%"+str+"%' or productos.codigo_origen like '%"+str+"%' or productos.bin like '%"+str+"%' or productos.entidad like '%"+str+"%') and estado='activo' order by productos.nombre_plastico asc";
    //alert(query);
    $.getJSON(url, {query: ""+query+""}).done(function(request) {
      var sugerencias = request.resultado;
      var totalSugerencias = request.rows;
      $("[name='hintProd']").remove();
      
      var mostrar = '';
      
      if (totalSugerencias >= 1) {
        mostrar = '<select name="hintProd" id="hintProd" tabindex="2" class="hint">';
        if (totalSugerencias > 1) {
          mostrar += '<option value="NADA" name="NADA" selected>--Seleccionar--</option>';
        }
        for (var i in sugerencias) {
          var bin = sugerencias[i]["bin"];
          if ((bin === null) || (bin === '') ){
            bin = 'SIN BIN';
          }
          var snapshot = sugerencias[i]["snapshot"];
          if ((snapshot === null)||(snapshot === '')) {
            snapshot = 'NADA';
          }
          var codigo_emsa = sugerencias[i]["codigo_emsa"];
          if ((codigo_emsa === null) || (codigo_emsa === "")) {
            codigo_emsa = 'SIN CODIGO AÚN';
          }
          var sel = "";
          if (parseInt(sugerencias[i]["idprod"], 10) === parseInt(seleccionado, 10)) {
            sel = 'selected="yes"';
          }
          //mostrar += '<option value="'+sugerencias[i]["idprod"]+'" name="'+snapshot+'" stock='+sugerencias[i]["stock"]+' alarma='+sugerencias[i]["alarma"]+'>[' + sugerencias[i]["entidad"]+'] '+sugerencias[i]["nombre_plastico"] + ' {' +bin+'} --'+ codigo_emsa +'--</option>';
          mostrar += '<option value="'+sugerencias[i]["idprod"]+'" name="'+snapshot+'" stock='+sugerencias[i]["stock"]+' alarma1='+sugerencias[i]["alarma1"]+' alarma2='+sugerencias[i]["alarma2"]+' '+sel+ '>[' + sugerencias[i]["entidad"]+': '+codigo_emsa+'] --- '+sugerencias[i]["nombre_plastico"] + '</option>';
        }
        mostrar += '</select>';
      }
      else {
        mostrar = '<p name="hintProd" value="">No se encontraron sugerencias!</p>';
      }
      $(id).after(mostrar);
      //inhabilitarProducto();
      //$("#hintProd").focusin();
      /// Agregado a pedido de Diego para que se abra el select automáticamente:
      var length = $('#hintProd> option').length;
      if (length > 10) {
        length = 10;
      }
      else {
        length++;
      }
      //alert('sugerencias: '+totalSugerencias+'\nlength: '+length);
      //open dropdown
      $("#hintProd").attr('size',length);
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
  //var cantidad2 = document.getElementById("cantidad2").value;
  var fecha = document.getElementById("fecha").value;
  //var usuarioBoveda = document.getElementById("usuarioBoveda").value;
  //var usuarioGrabaciones = document.getElementById("usuarioGrabaciones").value;
  
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
          /// Se quitan el doble ingreso de la cantidad, y el ingreso de las personas involucradas a pedido de Diego:
////        var cant2 = validarEntero(document.getElementById("cantidad2").value);  
////
////        if ((cantidad2 <= 0) || (cantidad2 === "null") || !cant2)
////          {
////          alert('La repetición de la cantidad de tarjetas debe ser un entero mayor o igual a 1.');
////          $("#cantidad2").val("");
////          document.getElementById("cantidad2").focus();
////          seguir = false;
////        } 
////        else
////          {
////          if (cantidad !== cantidad2)
////            {
////            alert('Las cantidades de tarjetas ingresadas deben coincidir. Por favor verifique!.');
////            $("#cantidad").val("");
////            $("#cantidad2").val("");
////            document.getElementById("cantidad").focus();
////            seguir = false;
//////          } 
////          else
////            {
//            if (usuarioBoveda === "ninguno")
//              {
//              alert('Se debe seleccionar al controlador 1. Por favor verifique!.');
//              document.getElementById("usuarioBoveda").focus();
//              seguir = false;
//            } 
//            else
//              {
//              if (usuarioGrabaciones === "ninguno")
//                {
//                alert('Se debe seleccionar al controlador 2. Por favor verifique!.');
//                document.getElementById("usuarioGrabaciones").focus();
//                seguir = false;
//              } 
//              else
//                {
//                if (usuarioGrabaciones === usuarioBoveda) {
//                  alert('NO puede estar el mismo usuario en ambos controles. Por favor verifique!.');
//                  document.getElementById("usuarioGrabaciones").focus();
//                  seguir = false;
//                } 
//                else {  
//                  seguir = true;
//                }
//              //}// usuarioGrabaciones
//            //}// usuarioBoveda  
//          //}// cantidad != cantidad2
//        //}// cantidad 2
      seguir = true;
      }// cantidad
    }// nombre_plastico
  }//
  if (seguir) return true;
  else return false;
}

/**
  \brief Función que carga en el selector pasado como parámetro la tabla para agregar un movimiento.
         Además, según desde donde se llame, carga también el "hint" de productos ya hecha así también como el tipo de movimiento recién hecho.
  @param {String} selector String con el selector en donde se debe mostrar la tabla.
  @param {String} hint String con la palabra o palabras a buscar como sugerencia de productos.
  @param {Integer} prod Integer con el id del producto.  
  @param {String} tipo String con el tipo de movimiento ingresado anteriormente.
*/
function cargarMovimiento(selector, hint, prod, tipo){
  var url = "data/selectQuery.php";
  var query = "select iduser, apellido, nombre from usuarios where (estado='activo' and (sector='Bóveda' or sector='Grabaciones')) order by nombre asc, apellido asc";
  
  /// Recupero fecha actual para pre setear en el campo Fecha:
  var actual = new Date();
  var dia = actual.getDate();
  var mes = parseInt(actual.getMonth(), 10);
  mes = mes +1;
  if (mes < 10) {
    mes = "0"+mes;
  }
  if (dia < 10) {
    dia = "0"+dia;
  }
  var año = actual.getFullYear();
  var hoy = año+"-"+mes+"-"+dia;
  
  $.getJSON(url, {query: ""+query+""}).done(function(request) {
    //var usuarios = request.resultado;
    var total = request.rows;
    if (total >= 1) {
      
      var titulo = '<h2 id="titulo" class="encabezado">INGRESO DE MOVIMIENTOS</h2>';
      var formu = '<form method="post" action="movimiento.php">';
      var tabla = '<table class="tabla2" name="movimiento">';
      var tr = '<th colspan="2" class="tituloTabla">MOVIMIENTO</th>';
      tr += '<tr>\n\
              <th><font class="negra">Fecha:</font></th>\n\
              <td align="center"><input type="date" name="fecha" id="fecha" style="width:100%; text-align: center" min="2017-09-01" value="'+hoy+'"></td>\n\
            </tr>';
      tr += '<tr>\n\
              <th align="left"><font class="negra">Producto:</font></th>\n\
              <td align="center">\n\
                <input type="text" id="producto" name="producto" size="9" class="agrandar" tabindex="1" onkeyup=\'showHint(this.value, "#producto", "")\' value="'+hint+'">\n\
              </td>\n\
            </tr>';
      tr += '<tr>\n\
              <th align="left"><font class="negra">Tipo:</font></th>\n\
              <td align="center">\n\
                <select id="tipo" name="tipo" tabindex="5" style="width:100%">\n\
                  <option value="Retiro" selected="yes">Retiro</option>\n\
                  <option value="Ingreso">Ingreso</option>\n\
                  <option value="Renovaci&oacute;n">Renovaci&oacute;n</option>\n\
                  <option value="Destrucci&oacute;n">Destrucci&oacute;n</option>\n\
                </select>\n\
              </td>\n\
            </tr>';
      tr += '<tr>\n\
              <th align="left"><font class="negra">Cantidad:</font></th>\n\
              <td align="center"><input type="text" id="cantidad" name="cantidad" class="agrandar" tabindex="2" maxlength="35" size="9"></td>\n\
            </tr>';
//      tr += '<tr>\n\
//              <th align="left"><font class="negra">Repetir Cantidad:</font></th>\n\
//              <td align="center"><input type="text" id="cantidad2" name="cantidad2" class="agrandar" maxlength="35" size="9"></td>\n\
//            </tr>';
      tr += '<tr>\n\
              <th align="left"><font class="negra">Comentarios:</font></th>\n\
              <td align="center"><input type="textarea" id="comentarios" name="comentarios" tabindex="6" class="agrandar" maxlength="150" size="9"></td>\n\
            </tr>';
//      tr += '<th colspan="2" class="centrado">CONTROL</th>';
//      tr += '<tr>\n\
//              <th align="left"><font class="negra">Control 1:</font></th>\n\
//              <td>\n\
//                <select id="usuarioBoveda" name="usuarioBoveda" style="width:100%">\n\
//                  <option value="ninguno" selected="yes">---Seleccionar---</option>';
//      for (var index in usuarios) 
//        {
//        tr += '<option value="'+usuarios[index]["iduser"]+'" name="'+usuarios[index]["nombre"]+' '+usuarios[index]['apellido']+'">'+usuarios[index]['nombre']+' '+usuarios[index]['apellido']+'</option>';
//      }
//      tr += '</select>\n\
//            </td>\n\
//          </tr>';
//      tr += '<tr>\n\
//              <th align="left"><font class="negra">Control 2:</font></th>\n\
//              <td>\n\
//                <select id="usuarioGrabaciones" name="usuarioGrabaciones" style="width: 100%">\n\
//                  <option value="ninguno" selected="yes">---Seleccionar---</option>';
//      for (var index in usuarios) 
//        {
//        tr += '<option value="'+usuarios[index]["iduser"]+'" name="'+usuarios[index]["nombre"]+' '+usuarios[index]['apellido']+'">'+usuarios[index]['nombre']+' '+usuarios[index]['apellido']+'</option>';
//      }
//      tr += '</select>\n\
//            </td>\n\
//          </tr>';
      tr += '<tr>\n\
              <td colspan="2" class="pieTabla">\n\
                <input type="button" value="ACEPTAR" id="agregarMovimiento" name="agregarMovimiento" tabindex="3" class="btn btn-success" align="center"/>\n\
              </td>\n\
              <td style="display:none">\n\
                <input type="text" id="idPasado" name="idPasado" value="<?php echo $idPasado ?>">\n\
              </td>\n\
            </tr>';
      tabla += tr;
      tabla += '</table>';
      formu += titulo;
      formu += tabla;
      formu += '</form><br>';
      $(selector).html(formu);
      
      
      if ((tipo !== '') && (tipo !== undefined)){
        $("#tipo option[value="+ tipo +"]").attr("selected",true);
      }
      
      if (prod !== "-1") {
        showHint(hint, "#producto", prod);
      }
      else {
        ///showHint(hint, "#producto", "");
        $("#producto").focus();
      }   
    }
  });    
}

/**
 * \brief Función que hace el agregado del movimiento en la base de datos. 
 *        Se separó del evento agregarMoviemiento para poder hacer el agregado al detectar el ENTER en el elemento cantidad.
 */
function agregarMovimiento(){
  var fecha = $("#fecha").val();
  var idProd = $("#hint").val();
  var busqueda = $("#producto").val();
  var stockActual = parseInt($("#hint").find('option:selected').attr("stock"), 10);
  var alarma1 = parseInt($("#hint").find('option:selected').attr("alarma1"), 10);
  var alarma2 = parseInt($("#hint").find('option:selected').attr("alarma2"), 10);
  var tipo = $("#tipo").val();
  var cantidad = parseInt($("#cantidad").val(), 10);
  var comentarios = $("#comentarios").val();
  //var idUserBoveda = $("#usuarioBoveda").val();
  //var idUserGrabaciones = $("#usuarioGrabaciones").val();
  var tempDate = new Date();
  var hora = tempDate.getHours()+":"+tempDate.getMinutes();
  var nuevoStock = stockActual;

  /// Elimino el pop up de confirmación a pedido de Diego: 
  /// Esto elimina también la necesidad de chequear la variable confirmar en el if más abajo
  //var confirmar = confirm("¿Confirma el ingreso de los siguientes datos? \n\nFecha: "+fechaMostrar+"\nProducto: "+nombreProducto+"\nTipo: "+tipo+"\nCantidad: "+cantidad+"\nControl 1: "+userBoveda+"\nControl 2: "+userGrabaciones+"\nComentarios: "+comentarios);

  /// Si el movimiento NO es una devolución, calculo el nuevo stock. De serlo, NO se quita de stock pues las tarjetas se reponen.
  if (tipo !== 'Ingreso') {
    nuevoStock = stockActual - cantidad;
  }
  if (tipo === 'Ingreso') {
    nuevoStock = stockActual + cantidad;
  }

  var avisarAlarma1 = false;
  var avisarAlarma2 = false;
  var avisarInsuficiente = false;

  /// Si el nuevoStock es menor a 0, siginifica que no hay stock suficiente. Se alerta y se descuenta sólo la cantidad disponible.
  /// CAMBIO: La política actual es DEJAR en suspenso el movimiento hasta que haya stock suficiente. NO HAY QUE HACER EL MOVIMIENTO!!
  var seguir = true;
  if (nuevoStock <0) {
    //cantidad = stockActual;
    avisarInsuficiente = true;
    seguir = false;
    //nuevoStock = 0;
  }
  else {
    if ((nuevoStock < alarma1) && (nuevoStock > alarma2)) {
      avisarAlarma1 = true;
    }
    if (nuevoStock < alarma2) {
      avisarAlarma2 = true;
    }
  }

  if (seguir) {
    /// Agrego el movimiento según los datos pasados:
    var url = "data/updateQuery.php";
    var query = "insert into movimientos (producto, fecha, hora, tipo, cantidad, control1, control2, comentarios) values ("+idProd+", '"+fecha+"', '"+hora+"', '"+tipo+"', "+cantidad+", "+2+", "+3+", '"+comentarios+"')";
    //alert(document.getElementById("usuarioSesion").value); --- USUARIO QUE REGISTRA!!!

    $.getJSON(url, {query: ""+query+""}).done(function(request) {
      var resultado = request["resultado"];
      /// Si el agregado es exitoso, actualizo el stock y la fecha de la última modificación en la tabla Productos:
      if (resultado === "OK") {
        var url = "data/updateQuery.php";
        var query = "update productos set stock="+nuevoStock+", ultimoMovimiento='"+fecha+"' where idprod="+idProd;

        $.getJSON(url, {query: ""+query+""}).done(function(request) {
          var resultado = request["resultado"];
          if (resultado === "OK") {
            if (avisarAlarma1) {
              //alert('El stock quedó por debajo de la alarma1 definida!. \n\nStock actual: ' + nuevoStock);
            }
            else {
              if (avisarAlarma2) {
                //alert('El stock quedó por debajo de la alarma2 definida!. \n\nStock actual: ' + nuevoStock);
              }
              else {
                if (avisarInsuficiente) {
                  //alert('Stock insuficiente!. \nSe descuenta sólo la cantidad existente. \n\nStock 0!!.');
                }
                else {
                  //alert('Registro agregado correctamente!. \n\nStock actual: '+nuevoStock);
                }
              }
            }
            var tipo1 = encodeURI(tipo);
            window.location.href = "../controlstock/movimiento.php?h="+busqueda+"&id="+idProd+"&t="+tipo1;
          }
          else {
            alert('Hubo un error en la actualizacion del producto. Por favor verifique.');
          }
        });
      }
      else {
        alert('Hubo un error en el agregado del movimiento. Por favor verifique.');
      }
    });  
  }
  else {
    alert('No hay stock suficiente del producto como para realizar el retiro.\n\n NO SE REALIZA!.');
  }
}

/**
 * \brief Función que hace la actualización del movimiento en la base de datos. 
 *        Se separó del evento actualizarMoviemiento para poder hacer el agregado al detectar el ENTER en el elemento comentarios.
 */
function actualizarMovimiento(){
  var idmov = $("input[name='idMov']").val();
  var comentarios = $("#comentarios").val();
  
  ///Se comenta la validación del movimiento pues por el momento SÓLO se puede EDITAR el comentario el cual es opcional.
  ///De en un futuro querer editar algo más habrá que crear la función validarMovimiento. Se setea la variable validar a TRUE
  //var validar = validarMovimiento();
  var validar = true;
  
  if (validar) {
    //var confirmar = confirm('¿Confirma la modificación del movimiento con los siguientes datos?\n\nFecha: '+fecha+'\nHora: '+hora+'\nProducto: '+nombre+'\nTipo: '+tipo+'\nCantidad: '+cantidad+'\nComentarios: '+comentarios+"\n?");
    var confirmar = true;
    if (confirmar) {
      var url = "data/updateQuery.php";
      var query = 'update movimientos set comentarios="'+comentarios+'" where idmov='+idmov;
      //alert(query);
      $.getJSON(url, {query: ""+query+""}).done(function(request) {
        var resultado = request["resultado"];
        if (resultado === "OK") {
          //alert('Los datos del movimiento se actualizaron correctamente!.');
          cargarEditarMovimiento(idmov, "main-content");
          inhabilitarMovimiento();
        }
        else {
          alert('Hubo un problema en la actualización. Por favor verifique.');
        }
      });
    }
    else {
      alert('Se optó por no actualizar el movimiento!.');
    }
  }
}

/**
  \brief Función que deshabilita los input del form editarMovimiento.
*/
function inhabilitarMovimiento(){
  document.getElementById("fecha").disabled = true;
  document.getElementById("hora").disabled = true;
  document.getElementById("nombre").disabled = true;
  document.getElementById("tipo").disabled = true;
  document.getElementById("cantidad").disabled = true;
  document.getElementById("comentarios").disabled = true;
  document.getElementById("editarMovimiento").value = "EDITAR";
  document.getElementById("actualizarMovimiento").disabled = true;
}

/**
  \brief Función que habilita los input del form editarMovimiento.
*/
function habilitarMovimiento(){
  ///******* Queda hecho para poder editar el resto de los campos, pero la idea es SOLO poder editar los comentarios. **********
  //document.getElementById("fecha").disabled = false;
  //document.getElementById("hora").disabled = false;
  //document.getElementById("nombre").disabled = false;
  //document.getElementById("tipo").disabled = false;
  //document.getElementById("cantidad").disabled = false;
  document.getElementById("comentarios").disabled = false;
  document.getElementById("editarMovimiento").value = "BLOQUEAR";
  document.getElementById("actualizarMovimiento").disabled = false;
}

/**
  \brief Función que carga en el selector pasado como parámetro la tabla para ver el movimiento.
  @param {String} selector String con el selector en donde se debe mostrar la tabla.
  @param {Integer} idMov Entero con el identificador del movimiento a cargar.
*/
function cargarEditarMovimiento(idMov, selector){
  var url = "data/selectQuery.php";
  var query = 'select movimientos.fecha, movimientos.hora, movimientos.cantidad, movimientos.comentarios, movimientos.tipo, productos.nombre_plastico from movimientos inner join productos on movimientos.producto=productos.idprod where movimientos.idmov='+idMov;
  
  $.getJSON(url, {query: ""+query+""}).done(function(request) {
    var resultado = request["resultado"];
    var total = request["rows"];
    if (total >= 1) {
      var cantidad = parseInt(resultado[0]['cantidad'], 10);
      var fechaTemp = resultado[0]['fecha'];
      var fecha = '';
      var hora = '';
      if (fechaTemp !== null) {
        var temp = fechaTemp.split('-');
        fecha = temp[2]+"/"+temp[1]+"/"+temp[0];
      }
      var horaTemp = resultado[0]['hora'];
      if (horaTemp !== null) {
        var temp = horaTemp.split(':');
        hora = temp[0]+":"+temp[1];
      }
      var tipo = resultado[0]['tipo'];
      var comentarios = resultado[0]['comentarios'];
      var producto = resultado[0]['nombre_plastico'];
    }
    var mostrar = "";
    var titulo = '<h2 id="titulo" class="encabezado">EDICIÓN DE MOVIMIENTOS</h2>';
    var formu = '<form method="post" action="editarMovimiento.php">';
    var tabla = '<table class="tabla2" name="editarMovimiento">';
    var tr = '<th colspan="3" class="centrado tituloTabla">DATOS DEL MOVIMIENTO</th>';

    tr += '<tr>\n\
            <th align="left" width="15"><font class="negra">Fecha:</font></th>\n\
            <td align="center" colspan="2"><input type="text" name="fecha" id="fecha" class="agrandar" style="width:100%; text-align: center" disabled></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Hora:</font></th>\n\
              <td align="center" colspan="2"><input type="text" name="hora" id="hora" class="agrandar" maxlength="35" style="width:100%; text-align: center" disabled></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Nombre:</font></th>\n\
              <td align="center" colspan="2"><input type="text" name="nombre" id="nombre" class="agrandar" maxlength="75" style="width:100%; text-align: center" disabled></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Tipo:</font></th>\n\
              <td align="center" colspan="2"><input type="text" name="tipo" id="tipo" class="agrandar" maxlength="35" style="width:100%; text-align: center" disabled></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Cantidad:</font></th>\n\
              <td align="center" colspan="2"><input type="text" id="cantidad" name="cantidad" class="agrandar" maxlength="35" size="9" disabled></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Comentarios:</font></th>\n\
              <td align="center" colspan="2"><input type="textarea" id="comentarios" name="comentarios" tabindex="1" class="agrandar" maxlength="35" size="9"></td>\n\
          </tr>';
    tr += '<tr>\n\
              <td class="pieTablaIzquierdo" style="width: 50%;border-right: 0px;"><input type="button" value="BLOQUEAR" id="editarMovimiento" name="editarMovimiento" class="btn btn-primary" align="center"/></td>\n\
              <td class="pieTablaDerecho" style="width: 50%;border-left: 0px;"><input type="button" value="ACTUALIZAR" id="actualizarMovimiento" name="actualizarMovimiento" tabindex="2" class="btn btn-warning" align="center"/></td>\n\
              <td style="display:none"><input type="text" name="idMov" value="'+idMov+'"></td>\n\
          </tr>';
    tabla += tr;
    tabla += '</table>';
    
    formu += tabla;
    formu += '</form>';
    mostrar += titulo;
    mostrar += formu;
    var volver = '<br><a href="../controlstock/busquedas.php" name="volver" id="volverEdicionMovimiento" >Volver</a><br><br>';
    mostrar += volver;
    $(selector).html(mostrar);
  
    if (total >=1) {
      $("#fecha").val(fecha);
      $("#hora").val(hora);
      $("#tipo").val(tipo);
      $("#nombre").val(producto);
      $("#cantidad").val(cantidad.toLocaleString());
      $("#comentarios").val(comentarios);
    }
    $("#comentarios").focus();
  }); 
}

/***********************************************************************************************************************
/// ********************************************* FIN FUNCIONES MOVIMIENTOS ********************************************
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

/**
 * \brief Función que valida los datos pasados para la búsqueda.
 * @returns {Boolean} Devuelve un booleano que indica si se pasó o no la validación de los datos para la búsqueda.
 */
function validarBusqueda() {
  
}

/**
 * \brief Función que ejecuta la búsqueda y muestra el resultado.
 */
function realizarBusqueda(){  
  var timestamp = Math.round(Date.now() / 1000);
      
  if(timestamp - $("#timestampSesion").val() > $("#duracionSesion").val()) {
    window.location.href = "../consultastock/index.php";
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
    
    if ((nombreProducto !== "undefined") && (nombreProducto !== '') && (nombreProducto !== '--Seleccionar--')) {
      ///Separo en partes el nombreProducto que contiene [entidad: codigo] --- nombreProducto
      var tempo = nombreProducto.split("- ");
      var nombreSolo = tempo[1].trim();
      //var tempo2 = tempo1.split("{");
      //var nombreSolo = tempo2[0].trim();
    }
    
    var tipo = $("#tipo").find('option:selected').val( );
    var idUser = $("#usuario").val();
    var nombreUsuario = $("#usuario").find('option:selected').text( );
    var radioFecha = $('input:radio[name=criterioFecha]:checked').val();
    var mes = $("#mes").val();
    var año = $("#año").val();
    var rangoFecha = null;
    var prodHint = '';
    var tipMov = '';
    var ent = '';
    
    var query = 'select productos.entidad, productos.nombre_plastico, productos.bin, productos.codigo_emsa, productos.contacto, productos.snapshot, DATE_FORMAT(productos.ultimoMovimiento, \'%d/%m/%Y\') as ultimoMovimiento, productos.stock, productos.alarma1, productos.alarma2, productos.comentarios as prodcom';
    var consultaCSV = 'select productos.entidad as entidad, productos.nombre_plastico as nombre, productos.bin as BIN, productos.stock as stock, productos.alarma1, productos.alarma2';
    var tipoConsulta = '';
    var mensajeFecha = '';
    var campos;
    var largos;
    var mostrarCamposQuery = "1-1-1-1-1-1-1-1-0-0";;
    var x = 55;
    
    var validado = true;
    var validarFecha = false;
    var validarTipo = false;
    var validarUser = false;
    var ordenFecha = false;
    var rutaFoto = 'images/snapshots/';
    
    switch (radio) {
      case 'entidadStock': if (entidadStock !== 'todos') {
                             ent = entidadStock;
                             query += " from productos where entidad='"+entidadStock+"' and estado='activo'";
                             consultaCSV += " from productos where entidad='"+entidadStock+"' and estado='activo'";
                             tipoConsulta = 'del stock de '+entidadStock;
                           } 
                           else {
                             query += " from productos where estado='activo'";
                             consultaCSV += " from productos where estado='activo'";
                             tipoConsulta = 'del stock de todas las entidades';
                           }
                           campos = "Id-Entidad-Nombre-BIN-C&oacute;digo-Contacto-Snapshot-&Uacute;lt. Mov.-Stock-Alarma1-Alarma2-Comentarios";
                           largos = "0.8-1.2-2.5-0.8-2-1-1-1.4-1.2-1-2-1";
                           mostrarCamposQuery = "1-1-1-1-1-0-0-1-1-0-0-0";
                           tipMov = 'entStock';
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
                               consultaCSV += " from productos where idProd="+idProd;
                             }
                             prodHint = $("#productoStock").val();
                             tipMov = 'prodStock';
                             tipoConsulta = 'de stock del producto '+nombreSolo;
                             campos = "Id-Entidad-Nombre-BIN-C&oacute;digo-Contacto-Snapshot-Stock-Alarma1-Alarma2-Comentarios-&Uacute;ltimo Movimiento";
                             largos = "0.8-1.2-2.5-0.8-2-2.5-1-1-1-1-2-1";
                             mostrarCamposQuery = "1-1-1-1-1-1-1-1-0-0-1-1";;
                             x = 22;
                             break;
      case 'totalStock':  query = "select entidad, sum(stock) as subtotal from productos where estado='activo' group by entidad";
                          consultaCSV = "select entidad as Entidad, sum(stock) as Subtotal from productos where estado='activo' group by entidad";
                          tipoConsulta = 'del total de plásticos en bóveda';
                          campos = 'Id-Entidad-Stock';
                          largos = '1-3.0-1.8';
                          mostrarCamposQuery = "1-1-1";
                          tipMov = 'totalStock';
                          x = 60;
                          break;                    
      case 'entidadMovimiento': query += ", DATE_FORMAT(movimientos.fecha, '%d/%m/%Y') as fecha, DATE_FORMAT(movimientos.hora, '%H:%i') as hora, movimientos.cantidad, movimientos.tipo, movimientos.comentarios, movimientos.idmov from productos inner join movimientos on productos.idprod=movimientos.producto where productos.estado='activo' ";
                                consultaCSV = "select DATE_FORMAT(movimientos.fecha, '%d/%m/%Y'), DATE_FORMAT(movimientos.hora, '%H:%i') as hora, productos.entidad, productos.nombre_plastico, productos.bin, movimientos.tipo, movimientos.cantidad, movimientos.comentarios from productos inner join movimientos on productos.idprod=movimientos.producto where productos.estado='activo' ";
                                if (entidadMovimiento !== 'todos') {
                                  ent = entidadMovimiento;
                                  query += "and entidad='"+entidadMovimiento+"' ";
                                  consultaCSV += "and entidad='"+entidadMovimiento+"' ";
                                  tipoConsulta = 'de los movimientos de '+entidadMovimiento;
                                } 
                                else {
                                  tipoConsulta = 'de los movimientos de todas las entidades';
                                }
                                validarFecha = true;
                                validarTipo = true;
                                validarUser = true;
                                ordenFecha = true;
                                tipMov = 'entMov';
                                campos = 'Id-Entidad-Nombre-BIN-Código-Contacto-Snapshot-Stock-Alarma1-Alarma2-ComentariosProd-&Uacute;ltimo Movimiento-Fecha-Hora-Cantidad-Tipo-Comentarios';
                                //Orden de la consulta: entidad - nombre - bin - codigo - contacto - snapshot - stock - alarma1 - alarma2 - prodcom - fecha - hora - cantidad - tipo - comentarios
                                largos = '0.6-1.6-1.9-1-1-1-1-1-1-1-1.1-1.5-1.5-0.8-1.2-1.4-2';
                                mostrarCamposQuery = '1-1-1-0-0-0-0-0-0-0-0-0-1-1-1-1-1';
                                x = 40;
                                break;                       
      case 'productoMovimiento':  query += ", DATE_FORMAT(movimientos.fecha, '%d/%m/%Y') as fecha, DATE_FORMAT(movimientos.hora, '%H:%i') as hora, movimientos.cantidad, movimientos.tipo, movimientos.comentarios, movimientos.idmov from productos inner join movimientos on productos.idprod=movimientos.producto where ";
                                  consultaCSV = "select DATE_FORMAT(movimientos.fecha, '%d/%m/%Y'), DATE_FORMAT(movimientos.hora, '%H:%i') as hora, productos.entidad, productos.nombre_plastico, productos.bin, movimientos.tipo, movimientos.cantidad, movimientos.comentarios from productos inner join movimientos on productos.idprod=movimientos.producto where productos.estado='activo' ";
                                  if ((idProd === 'NADA') || (nombreProducto === '')){
                                    alert('Debe seleccionar un producto. Por favor verifique.');
                                    document.getElementById("productoMovimiento").focus();
                                    validado = false;
                                    return false;
                                  }
                                  else {
                                    query += "idProd="+idProd;
                                    consultaCSV += "idProd="+idProd;
                                    validarFecha = true;
                                    validarTipo = true;
                                    validarUser = true;
                                    ordenFecha = true;
                                  }
                                  prodHint = $("#productoMovimiento").val();
                                  tipMov = 'prodMov';
                                  tipoConsulta = 'de los movimientos del producto '+nombreSolo;
                                  campos = 'Id-Entidad-Nombre-BIN-Código-Contacto-Snapshot-Stock-Alarma1-Alarma2-ComentariosProd-&Uacute;ltimo Movimiento-Fecha-Hora-Cantidad-Tipo-Comentarios';
                                  //Orden de la consulta: entidad - nombre - bin - codigo - snapshot - stock - alarma - prodcom - fecha - hora - cantidad - tipo - comentarios
                                  largos = '0.4-1.5-1.8-1-1-1-1-1-1-1-1.1-1.5-1.5-0.8-1.2-1.4-2';
                                  mostrarCamposQuery = '1-0-0-0-0-0-0-0-0-0-0-0-1-1-1-1-1';
                                  break;
      default: break;
    }
    
    if (validarFecha) {
      switch (radioFecha) {
        case 'intervalo': ///Comienzo la validación de las fechas:  
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
                              rangoFecha = "and (fecha >='"+inicio+"') and (fecha<='"+fin+"')";
                              mensajeFecha = "entre las fechas: "+inicioMostrar+" y "+finMostrar;
                            }
                          } /// FIN validación de las fechas.
                          break;
        case 'mes': if (mes === 'todos') {
                      inicio = año+"-01-01";
                      fin = año+"-12-31";
                      mensajeFecha = "del año "+año;
                    }
                    else {
                      inicio = año+"-"+mes+"-01";
                      var añoFin = parseInt(año, 10);
                      var mesSiguiente = parseInt(mes, 10) + 1;
                      if (mesSiguiente === 13) {
                        mesSiguiente = 1;
                        añoFin = parseInt(año, 10) + 1;
                      }
                      fin = añoFin+"-"+mesSiguiente+"-01";
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
                    rangoFecha = "and (fecha >='"+inicio+"') and (fecha<'"+fin+"')";
                    break;
        case 'todos': rangoFecha = '';
                      break;
        default: break;
      }
      query += rangoFecha;
      consultaCSV += rangoFecha;
    }
    
    if (validado) 
      {
      var mensajeTipo = null;  
      if (validarTipo) {    
        if (tipo !== 'Todos') {
          query += " and tipo='"+tipo+"'";
          consultaCSV += " and tipo='"+tipo+"'";
          mensajeTipo = "del tipo "+tipo;
        }
        else {
          mensajeTipo = "de todos los tipos";
        };
      }
      
      var mensajeUsuario = null;
      if (validarUser) {
        if (idUser !== 'todos') {
          query += " and (control1="+idUser+" or control2="+idUser+")";
          consultaCSV += " and (control1="+idUser+" or control2="+idUser+")";
          mensajeUsuario = " en los que está involucrado el usuario "+nombreUsuario;
        }
      }
      
      if (ordenFecha) {
        query += " order by entidad asc, nombre_plastico asc, movimientos.fecha desc, hora desc,  idprod";
        consultaCSV += " order by entidad asc, nombre_plastico asc, movimientos.fecha desc, hora desc, idprod";
      }
      else {
        query += " order by entidad asc, nombre_plastico asc, idprod asc";
        consultaCSV += " order by entidad asc, nombre_plastico asc, idprod asc";
      }
      //consultaCSV += ")";
      var mensajeConsulta = "Consulta "+tipoConsulta;
      if (mensajeTipo !== null) {
        mensajeConsulta += " "+mensajeTipo;
      }
      mensajeConsulta += " "+mensajeFecha;
      if (mensajeUsuario !== null) {
        mensajeConsulta += mensajeUsuario;
      }
      var mostrar = "<h2>Resultado de la búsqueda</h2>";
      mostrar += "<h3>"+mensajeConsulta+"</h3>";

      var url = "data/selectQuery.php";
      //alert(query);
      $.getJSON(url, {query: ""+query+""}).done(function(request){
        var datos = request.resultado;
        var totalDatos = request.rows;
        
        if (totalDatos >= 1) 
          {
          $("#main-content").empty();  
          var tabla = '<form name="resultadoBusqueda" id="resultadoBusqueda" target="_blank" action="exportar.php" method="post" class="exportarForm">';
          tabla += '<table name="producto" class="tabla2">';
          switch(radio) {
            case 'entidadStock':  tabla += '<tr><th class="tituloTabla" colspan="8">CONSULTA DE STOCK</th></tr>';
                                  tabla += '<tr>\n\
                                              <th>Item</th>\n\
                                              <th>Entidad</th>\n\
                                              <th>Nombre</th>\n\
                                              <th>BIN</th>\n\
                                              <th>Código</th>\n\
                                              <th>Snapshot</th>\n\
                                              <th>&Uacute;ltimo Movimiento</th>\n\
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
                                    var alarma1 = parseInt(datos[i]['alarma1'], 10);
                                    var alarma2 = parseInt(datos[i]['alarma2'], 10);
                                    var ultimoMovimiento = datos[i]['ultimoMovimiento'];
                                    if (ultimoMovimiento === null) {
                                      ultimoMovimiento = '';
                                    }
                                    var claseResaltado = "";
                                    if ((stock < alarma1) && (stock > alarma2)){
                                      claseResaltado = "alarma1";
                                    }
                                    else {
                                      if (stock < alarma2) {
                                        claseResaltado = "alarma2";
                                      }
                                      else {
                                        claseResaltado = "resaltado italica";
                                      }
                                    }  
                                    if ((bin === 'SIN BIN')||(bin === null)) 
                                      {
                                      bin = 'N/D o N/C';
                                    }
                                    tabla += '<tr>\n\
                                                <td>'+indice+'</td>\n\
                                                <td style="text-align: left">'+entidad+'</td>\n\
                                                <td>'+nombre+'</td>\n\
                                                <td nowrap>'+bin+'</td>\n\
                                                <td>'+codigo_emsa+'</td>\n\
                                                <td><img id="snapshot" name="hint" src="'+rutaFoto+snapshot+'" alt="No se cargó aún." height="76" width="120"></img></td>\n\
                                                <td>'+ultimoMovimiento+'</td>\n\
                                                <td class="'+claseResaltado+'" style="text-align: right">'+stock.toLocaleString()+'</td>\n\
                                              </tr>';
                                    indice++;
                                    total += stock;
                                  }
                                  tabla += '<tr><th colspan="7" class="centrado">TOTAL:</th><td class="resaltado1 italica" style="text-align: right">'+total.toLocaleString()+'</td></tr>';
                                  
                                  tabla += '<tr><td style="display:none"><input type="text" id="query" name="consulta" value="'+query+'"></td>\n\
                                                <td style="display:none"><input type="text" id="consultaCSV" name="consultaCSV" value="'+consultaCSV+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="campos" name="campos" value="'+campos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="largos" name="largos" value="'+largos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="param" name="param" value=""></td>\n\
                                                    <td style="display:none"><input type="text" id="entidad" value="'+entidadStock+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="nombreProducto" name="nombreProducto" value="'+nombreProducto+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="mostrar" value="'+mostrarCamposQuery+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="tipoConsulta" name="tipoConsulta" value="'+mensajeConsulta+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="x" name="x" value="'+x+'"></td>\n\
                                                  </tr>';
                                  tabla += '<tr>\n\
                                              <td class="pieTabla" colspan="8">\n\
                                                <input type="button" id="1" name="exportarBusqueda" value="EXPORTAR" class="btn btn-primary exportar">\n\
                                              </td>\n\
                                            </tr>\n\
                                          </table>\n\
                                        </form>';
                                  break;
            case 'productoStock': var bin = datos[0]['bin'];
                                  //var produ = datos[0]["idProd"];
                                  if ((bin === 'SIN BIN')||(bin === null)) 
                                      {
                                      bin = 'N/D o N/C';
                                    }
                                  var alarma1 = parseInt(datos[0]['alarma1'], 10);
                                  var alarma2 = parseInt(datos[0]['alarma2'], 10);
                                  var stock = parseInt(datos[0]['stock'], 10);
                                  var snapshot = datos[0]['snapshot'];
                                  var ultimoMovimiento = datos[0]['ultimoMovimiento'];
                                    if (ultimoMovimiento === null) {
                                      ultimoMovimiento = '';
                                    }
                                  var contacto = datos[0]['contacto'];
                                  if (contacto === null) 
                                      {
                                      contacto = '';
                                    }
                                  var prodcom = datos[0]['prodcom'];
                                  if (prodcom === null) 
                                      {
                                      prodcom = '';
                                    }
                                  var claseResaltado = "italica";
                                  if ((stock < alarma1) && (stock > alarma2)){
                                    claseResaltado = "alarma1";
                                  }
                                  else {
                                    if (stock < alarma2) {
                                      claseResaltado = "alarma2";
                                    }
                                    else {
                                      claseResaltado = "resaltado italica";
                                    }
                                  } 
                                  tabla += '<tr>\n\
                                              <th colspan="2" class="tituloTabla">DETALLES</th>\n\
                                           </tr>';                       
                                  tabla += '<tr><th style="text-align:left">Nombre:</th><td>'+datos[0]['nombre_plastico']+'</td></tr>';
                                  tabla += '<tr><th style="text-align:left">Entidad:</th><td>'+datos[0]['entidad']+'</td></tr>';
                                  tabla += '<tr><th style="text-align:left">C&oacute;digo:</th><td>'+datos[0]['codigo_emsa']+'</td></tr>';
                                  tabla += '<tr><th style="text-align:left">BIN:</th><td nowrap>'+bin+'</td></tr>';
                                  tabla += '<tr><th style="text-align:left">Snapshot:</th><td><img id="snapshot" name="hint" src="'+rutaFoto+snapshot+'" alt="No se cargó aún." height="125" width="200"></img></td></tr>';
                                  tabla += '<tr><th style="text-align:left">Contacto:</th><td>'+contacto+'</td></tr>';
                                  tabla += '<tr><th style="text-align:left">Comentarios:</th><td>'+prodcom+'</td></tr>';
                                  tabla += '<tr><th style="text-align:left">&Uacute;ltimo Movimiento:</th><td>'+ultimoMovimiento+'</td></tr>';
                                  tabla += '<tr><th style="text-align:left">Stock:</th><td class="'+claseResaltado+'">'+stock.toLocaleString()+'</td></tr>';
                                  tabla += '<tr><td style="display:none"><input type="text" id="query" name="consulta" value="'+query+'"></td>\n\
                                                <td style="display:none"><input type="text" id="consultaCSV" name="consultaCSV" value="'+consultaCSV+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="campos" name="campos" value="'+campos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="largos" name="largos" value="'+largos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="nombreProducto" name="nombreProducto" value="'+nombreProducto+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="param" name="param" value=""></td>\n\
                                                    <td style="display:none"><input type="text" id="idProd" name="idProd" value="'+idProd+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="mostrar" value="'+mostrarCamposQuery+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="tipoConsulta" name="tipoConsulta" value="'+mensajeConsulta+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="x" name="x" value="'+x+'"></td>\n\
                                                  </tr>';
                                  tabla += '<tr>\n\
                                              <td class="pieTabla" colspan="2">\n\
                                                <input type="button" id="2" name="exportarBusqueda" value="EXPORTAR" class="btn btn-primary exportar">\n\
                                              </td>\n\
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
                                              <td class="resaltado italica" style="text-align: right">'+subtotal.toLocaleString()+'</td>\n\
                                            </tr>';
                                  indice++;  
                                  total += subtotal;
                                }
                                tabla += '<tr><th colspan="2" class="centrado">TOTAL:</th><td class="resaltado1 italica" style="text-align: right">'+total.toLocaleString()+'</td></tr>';
                                tabla += '<tr><td style="display:none"><input type="text" id="query" name="consulta" value="'+query+'"></td>\n\
                                              <td style="display:none"><input type="text" id="consultaCSV" name="consultaCSV" value="'+consultaCSV+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="campos" name="campos" value="'+campos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="mostrar" value="'+mostrarCamposQuery+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="largos" name="largos" value="'+largos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="param" name="param" value=""></td>\n\
                                                    <td style="display:none"><input type="text" id="tipoConsulta" name="tipoConsulta" value="'+mensajeConsulta+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="x" name="x" value="'+x+'"></td>\n\
                                                  </tr>';
                                tabla += '<tr>\n\
                                            <td class="pieTabla" colspan="3">\n\
                                              <input type="button" id="3" name="exportarBusqueda" value="EXPORTAR" class="btn btn-primary exportar">\n\
                                            </td>\n\
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
                                      var productoViejo = datos[0]['nombre_plastico'];
                                      var subtotalRetiro = 0;
                                      var subtotalIngreso = 0;
                                      var subtotalReno = 0;
                                      var subtotalDestruccion = 0;
                                      var totalConsumos = 0;
                                      
                                      for (var i in datos) { 
                                        //var produ = datos[i]["idProd"];
                                        var idmov = datos[i]["idmov"];
                                        var entidad = datos[i]["entidad"];
                                        var nombre = datos[i]['nombre_plastico'];
                                        var cantidad = parseInt(datos[i]['cantidad'], 10);
                                        var bin = datos[i]['bin'];
                                        var codigo_emsa = datos[i]['codigo_emsa'];
                                        var tipo1 = datos[i]['tipo'];
                                        var snapshot = datos[i]['snapshot'];
                                        var fecha = datos[i]['fecha'];
                                        var hora = datos[i]["hora"];    
                                        
                                        var alarma1 = parseInt(datos[i]['alarma1'], 10);
                                        var alarma2 = parseInt(datos[i]['alarma2'], 10);
                                        var stock = parseInt(datos[i]['stock'], 10);
                                        var claseResaltado = '';
                                        if ((stock < alarma1) && (stock > alarma2)){
                                          claseResaltado = "alarma1";
                                        }
                                        else {
                                          if (stock < alarma2) {
                                            claseResaltado = "alarma2";
                                          }
                                          else {
                                            claseResaltado = "resaltado";
                                          }
                                        }  
                                        var comentarios = datos[i]['comentarios'];
                                        if ((comentarios === "undefined")||(comentarios === null)) {
                                            comentarios = "";
                                          }
                                        if ((bin === 'SIN BIN')||(bin === null)) 
                                          {
                                          bin = 'N/D o N/C';
                                        }
                                        
                                        if (productoViejo !== nombre) {
                                          productoViejo = nombre;
                                          if (subtotalRetiro > 0) {
                                            tabla += '<tr>\n\
                                                        <td colspan="9" class="negrita">Total Retiros:</td>\n\
                                                        <td class="subtotal" colspan="2">'+subtotalRetiro.toLocaleString()+'</td>\n\
                                                      </tr>';
                                            subtotalRetiro = 0;
                                          }
                                          if (subtotalReno > 0) {
                                            tabla += '<tr>\n\
                                                        <td colspan="9" class="negrita">Total Renovaciones:</td>\n\
                                                        <td class="subtotal" colspan="2">'+subtotalReno.toLocaleString()+'</td>\n\
                                                      </tr>';
                                            subtotalReno = 0;
                                          }
                                          if (subtotalDestruccion > 0) {
                                            tabla += '<tr>\n\
                                                        <td colspan="9" class="negrita">Total Destrucciones:</td>\n\
                                                        <td class="subtotal" colspan="2">'+subtotalDestruccion.toLocaleString()+'</td>\n\
                                                      </tr>';
                                            subtotalDestruccion = 0;
                                          }
                                          if (totalConsumos > 0) {
                                            tabla += '<tr>\n\
                                                        <td colspan="9" class="negrita">Total de Consumos:</td>\n\
                                                        <td class="totalConsumos" colspan="2">'+totalConsumos.toLocaleString()+'</td>\n\
                                                      </tr>';
                                            totalConsumos = 0;
                                          }
                                          if (subtotalIngreso > 0) {
                                            tabla += '<tr>\n\
                                                        <td colspan="9" class="negrita">Total de Ingresos:</td>\n\
                                                        <td class="totalIngresos" colspan="2">'+subtotalIngreso.toLocaleString()+'</td>\n\
                                                      </tr>';
                                            subtotalIngreso = 0;
                                          }
                                          
                                          tabla += '<th colspan="11">&nbsp;\n\
                                                    </th>';
                                          switch (tipo1){
                                            case "Retiro": subtotalRetiro = cantidad;
                                                           break;
                                            case "Ingreso": subtotalIngreso = cantidad;
                                                            break;
                                            case "Renovación": subtotalReno = cantidad;
                                                                break;
                                            case "Destrucción": subtotalDestruccion = cantidad;
                                                                break;
                                            default: break;
                                          }
                                          totalConsumos = subtotalRetiro + subtotalReno + subtotalDestruccion;
                                        }
                                        else {
                                          switch (tipo1){
                                            case "Retiro": subtotalRetiro = subtotalRetiro + cantidad;
                                                           break;
                                            case "Ingreso": subtotalIngreso = subtotalIngreso + cantidad;
                                                            break;
                                            case "Renovación": subtotalReno = subtotalReno + cantidad;
                                                                break;
                                            case "Destrucción": subtotalDestruccion = subtotalDestruccion + cantidad;
                                                                break;
                                            default: break;
                                          }
                                          if (tipo1 !== 'Ingreso') {
                                            totalConsumos = totalConsumos + cantidad;
                                          }
                                        }
                                        
                                        tabla += '<tr>\n\
                                                    <td style="display:none"><input type="text" name="idmov" value="'+idmov+'"></td>\n\
                                                    <td>'+indice+'</td>\n\
                                                    <td>'+fecha+'</td>\n\
                                                    <td>'+hora+'</td>\n\
                                                    <td>'+entidad+'</td>\n\
                                                    <td>'+nombre+'</td>\n\
                                                    <td nowrap>'+bin+'</td>\n\
                                                    <td>'+codigo_emsa+'</td>\n\
                                                    <td><img id="snapshot" name="hint" src="'+rutaFoto+snapshot+'" alt="No se cargó aún." height="75" width="120"></img></td>\n\
                                                    <td>'+tipo1+'</td>\n\
                                                    <td class="'+claseResaltado+'"><a href="editarMovimiento.php?id='+idmov+'" target="_blank">'+cantidad.toLocaleString()+'</a></td>\n\
                                                    <td>'+comentarios+'</td>\n\
                                                  </tr>';        
                                        indice++;  
                                      }
                                      if (subtotalRetiro > 0) {
                                        tabla += '<tr>\n\
                                                    <td colspan="9" class="negrita">Total Retiros:</td>\n\
                                                    <td class="subtotal" colspan="2">'+subtotalRetiro.toLocaleString()+'</td>\n\
                                                  </tr>';
                                        subtotalRetiro = 0;
                                      }
                                      if (subtotalReno > 0) {
                                        tabla += '<tr>\n\
                                                    <td colspan="9" class="negrita">Total Renovaciones:</td>\n\
                                                    <td class="subtotal" colspan="2">'+subtotalReno.toLocaleString()+'</td>\n\
                                                  </tr>';
                                        subtotalReno = 0;
                                      }
                                      if (subtotalDestruccion > 0) {
                                        tabla += '<tr>\n\
                                                    <td colspan="9" class="negrita">Total Destrucciones:</td>\n\
                                                    <td class="subtotal" colspan="2">'+subtotalDestruccion.toLocaleString()+'</td>\n\
                                                  </tr>';
                                        subtotalDestruccion = 0;  
                                      }
                                      if (totalConsumos > 0) {
                                        tabla += '<tr>\n\
                                                    <td colspan="9" class="negrita">Total de Consumos:</td>\n\
                                                    <td class="totalConsumos" colspan="2">'+totalConsumos.toLocaleString()+'</td>\n\
                                                  </tr>';
                                        totalConsumos = 0;  
                                      }
                                      if (subtotalIngreso > 0) {
                                        tabla += '<tr>\n\
                                                    <td colspan="9" class="negrita">Total de Ingresos:</td>\n\
                                                    <td class="totalIngresos" colspan="2">'+subtotalIngreso.toLocaleString()+'</td>\n\
                                                  </tr>';
                                        subtotalIngreso = 0;
                                      }
                                      
                                      tabla += '<th colspan="11">&nbsp;\n\
                                                </th>';
                                      tabla += '<tr><td style="display:none"><input type="text" id="query" name="consulta" value="'+query+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="consultaCSV" name="consultaCSV" value="'+consultaCSV+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="campos" name="campos" value="'+campos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="largos" name="largos" value="'+largos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="param" name="param" value=""></td>\n\
                                                    <td style="display:none"><input type="text" id="tipo" name="tipo" value="'+tipo+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="usuario" name="usuario" value="'+idUser+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="mostrar" value="'+mostrarCamposQuery+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="nombreProducto" name="nombreProducto" value="'+nombreProducto+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="criterioFecha" name="criterioFecha" value="'+radioFecha+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="inicio" name="inicio" value="'+inicio+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="fin" name="fin" value="'+fin+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="mes" name="mes" value="'+mes+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="año" name="año" value="'+año+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="entidad" value="'+entidadMovimiento+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="tipoConsulta" name="tipoConsulta" value="'+mensajeConsulta+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="x" name="x" value="'+x+'"></td>\n\
                                                  </tr>';
              
                                      tabla += '<tr>\n\
                                                  <td class="pieTabla" colspan="11">\n\
                                                    <input type="button" id="4" name="exportarBusqueda" value="EXPORTAR" class="btn btn-primary exportar">\n\
                                                  </td>\n\
                                                </tr>\n\
                                              </table>\n\
                                            </form>';  
                                      break;
            case 'productoMovimiento':  var bin = datos[0]['bin'];
                                        var produ = datos[0]['produ'];
                                        var ultimoMovimiento = datos[0]['ultimoMovimiento'];
                                        if (ultimoMovimiento === null) {
                                          ultimoMovimiento = '';
                                        }
                                        var contacto = datos[0]['contacto'];
                                        if (contacto === null) 
                                            {
                                            contacto = '';
                                          }
                                        var comentarios = datos[0]['prodcom'];
                                        if (comentarios === null) 
                                            {
                                            comentarios = '';
                                          }  
                                        if ((bin === 'SIN BIN')||(bin === null)) 
                                            {
                                            bin = 'N/D o N/C';
                                          }
                                        var snapshot = datos[0]['snapshot'];
                                        var alarma1 = parseInt(datos[0]['alarma1'], 10);
                                        var alarma2 = parseInt(datos[0]['alarma2'], 10);
                                        var stock = parseInt(datos[0]['stock'],10);
                                        if ((stock < alarma1) && (stock > alarma2)){
                                          claseResaltado = "alarma1";
                                        }
                                        else {
                                          if (stock < alarma2) {
                                            claseResaltado = "alarma2";
                                          }
                                          else {
                                            claseResaltado = "resaltado italica";
                                          }
                                        } 
                                  
                                        tabla += '<tr>\n\
                                                    <th colspan="2" class="tituloTabla">PRODUCTO</th>\n\
                                                 </tr>';                       
                                        tabla += '<tr><th>Nombre:</th><td>'+datos[0]['nombre_plastico']+'</td></tr>';
                                        tabla += '<tr><th>Entidad:</th><td>'+datos[0]['entidad']+'</td></tr>';
                                        tabla += '<tr><th>C&oacute;digo:</th><td>'+datos[0]['codigo_emsa']+'</td></tr>';
                                        tabla += '<tr><th>BIN:</th><td nowrap>'+bin+'</td></tr>';
                                        tabla += '<tr><th>Snapshot:</th><td><img id="snapshot" name="hint" src="'+rutaFoto+snapshot+'" alt="No se cargó aún." height="125" width="200"></img></td></tr>';
                                        tabla += '<tr><th>Contacto:</th><td>'+contacto+'</td></tr>';
                                        tabla += '<tr><th>Comentarios:</th><td>'+comentarios+'</td></tr>';
                                        tabla += '<tr><th>&Uacute;ltimo Moviemiento:</th><td>'+ultimoMovimiento+'</td></tr>';
                                        tabla += '<tr><th>Stock:</th><td class="'+claseResaltado+'">'+stock.toLocaleString()+'</td></tr>';
                                        tabla += '<tr><th colspan="2" class="pieTabla centrado">FIN</th></tr></table>';
                                        
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
                                        var subtotalRetiro = 0;
                                        var subtotalIngreso = 0;
                                        var subtotalReno = 0;
                                        var subtotalDestruccion = 0;
                                        var totalConsumos = 0;
                                      
                                        for (var i in datos) { 
                                          var tipo2 = datos[i]['tipo'];
                                          var fecha = datos[i]['fecha'];
                                          var hora = datos[i]["hora"];
                                          var idmov = datos[i]["idmov"];
                                          var cantidad = parseInt(datos[i]['cantidad'], 10);
                                          var alarma1 = parseInt(datos[i]['alarma1'], 10);
                                          var alarma2 = parseInt(datos[i]['alarma2'], 10);
                                          var stock = parseInt(datos[i]['stock'], 10);
                                          var claseResaltado = '';
                                          if ((stock < alarma1) && (stock > alarma2)){
                                            claseResaltado = "alarma1";
                                          }
                                          else {
                                            if (stock < alarma2) {
                                              claseResaltado = "alarma2";
                                            }
                                            else {
                                              claseResaltado = "resaltado";
                                            }
                                          } 
                                          var comentarios = datos[i]['comentarios'];
                                          if ((comentarios === "undefined")||(comentarios === null)) {
                                            comentarios = "";
                                          }
                                          switch (tipo2){
                                            case "Retiro": subtotalRetiro = subtotalRetiro + cantidad;
                                                           break;
                                            case "Ingreso": subtotalIngreso = subtotalIngreso + cantidad;
                                                            break;
                                            case "Renovación": subtotalReno = subtotalReno + cantidad;
                                                                break;
                                            case "Destrucción": subtotalDestruccion = subtotalDestruccion + cantidad;
                                                                break;
                                            default: break;
                                          }
                                          if (tipo2 !== 'Ingreso') {
                                            totalConsumos = totalConsumos + cantidad;
                                          }
                                          
                                          tabla += '<tr>\n\
                                                      <td style="display:none"><input type="text" name="idmov" value="'+idmov+'"></td>\n\
                                                      <td>'+indice+'</td>\n\
                                                      <td>'+fecha+'</td>\n\
                                                      <td>'+hora+'</td>\n\
                                                      <td>'+tipo2+'</td>\n\
                                                      <td class="'+claseResaltado+'"><a href="editarMovimiento.php?id='+idmov+'">'+cantidad.toLocaleString()+'</a></td>\n\
                                                      <td>'+comentarios+'</td>\n\
                                                    </tr>';
                                          indice++;  
                                          }
                                          
                                          if (subtotalRetiro > 0) {
                                            tabla += '<tr>\n\
                                                        <td colspan="4" class="negrita">Total Retiros:</td>\n\
                                                        <td class="subtotal" colspan="2">'+subtotalRetiro.toLocaleString()+'</td>\n\
                                                      </tr>';
                                            subtotalRetiro = 0;
                                          }
                                          if (subtotalReno > 0) {
                                            tabla += '<tr>\n\
                                                        <td colspan="4" class="negrita">Total Renovaciones:</td>\n\
                                                        <td class="subtotal" colspan="2">'+subtotalReno.toLocaleString()+'</td>\n\
                                                      </tr>';
                                            subtotalReno = 0;
                                          }
                                          if (subtotalDestruccion > 0) {
                                            tabla += '<tr>\n\
                                                        <td colspan="4" class="negrita">Total Destrucciones:</td>\n\
                                                        <td class="subtotal" colspan="2">'+subtotalDestruccion.toLocaleString()+'</td>\n\
                                                      </tr>';
                                            subtotalDestruccion = 0;  
                                          }
                                          if (totalConsumos > 0) {
                                            tabla += '<tr>\n\
                                                        <td colspan="4" class="negrita">Total de Consumos:</td>\n\
                                                        <td class="totalConsumos" colspan="2">'+totalConsumos.toLocaleString()+'</td>\n\
                                                      </tr>';
                                            totalConsumos = 0;  
                                          }
                                          if (subtotalIngreso > 0) {
                                            tabla += '<tr>\n\
                                                        <td colspan="4" class="negrita">Total de Ingresos:</td>\n\
                                                        <td class="totalIngresos" colspan="2">'+subtotalIngreso.toLocaleString()+'</td>\n\
                                                      </tr>';
                                            subtotalIngreso = 0;
                                          }
                                          
                                          tabla += '<tr><td style="display:none"><input type="text" id="query" name="consulta" value="'+query+'"></td>\n\
                                                        <td style="display:none"><input type="text" id="consultaCSV" name="consultaCSV" value="'+consultaCSV+'"></td>\n\
                                                      <td style="display:none"><input type="text" id="campos" name="campos" value="'+campos+'"></td>\n\
                                                      <td style="display:none"><input type="text" id="largos" name="largos" value="'+largos+'"></td>\n\
                                                      <td style="display:none"><input type="text" id="param" name="param" value=""></td>\n\
                                                      <td style="display:none"><input type="text" id="tipo" name="tipo" value="'+tipo+'"></td>\n\
                                                      <td style="display:none"><input type="text" id="nombreProducto" name="nombreProducto" value="'+nombreProducto+'"></td>\n\
                                                      <td style="display:none"><input type="text" id="mostrar" value="'+mostrarCamposQuery+'"></td>\n\
                                                      <td style="display:none"><input type="text" id="criterioFecha" name="criterioFecha" value="'+radioFecha+'"></td>\n\
                                                      <td style="display:none"><input type="text" id="inicio" name="inicio" value="'+inicio+'"></td>\n\
                                                      <td style="display:none"><input type="text" id="fin" name="fin" value="'+fin+'"></td>\n\
                                                      <td style="display:none"><input type="text" id="mes" name="mes" value="'+mes+'"></td>\n\
                                                      <td style="display:none"><input type="text" id="año" name="año" value="'+año+'"></td>\n\
                                                      <td style="display:none"><input type="text" id="usuario" name="usuario" value="'+idUser+'"></td>\n\
                                                      <td style="display:none"><input type="text" id="idProd" name="largos" value="'+idProd+'"></td>\n\
                                                      <td style="display:none"><input type="text" id="tipoConsulta" name="tipoConsulta" value="'+mensajeConsulta+'"></td>\n\
                                                      <td style="display:none"><input type="text" id="x" name="x" value="'+x+'"></td>\n\
                                                    </tr>';

                                          tabla += '<tr>\n\
                                                      <td class="pieTabla" colspan="6">\n\
                                                        <input type="button" id="5" name="exportarBusqueda" value="EXPORTAR" class="btn btn-primary exportar">\n\
                                                      </td>\n\
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

        if (idProd === undefined) {
          idProd = '';
        }
        var volver = '<br><a href="../controlstock/busquedas.php?h='+prodHint+'&t='+tipMov+'&id='+idProd+'&ent='+ent+'" name="volver" id="volverBusqueda" >Volver</a><br><br>';
        mostrar += volver;
        $("#main-content").append(mostrar);
      });    
    }/// Fin del IF de validado
    else {
      alert('NO validado');//Igualmente no llega a esta etapa dado que al no ser válida retorna falso y sale.    
    } 
  }
}

/**
 * \brief Función que genera el formulario para realizar las consultas.
 * @param {String} selector String con el nombre del DIV donde cargar el form.
 * @param {String} hint String con la sugerencia pasada.
 * @param {String} tipo String que indica si la consulta es de stock o de movimientos.
 * @param {String} idProd String con el identificador del producto previamente seleccionado (si corresponde).
 * @param {String} entidad String con de la entidad previamente seleccionada (si corresponde).
 */
function cargarFormBusqueda(selector, hint, tipo, idProd, entidad){
  var url = "data/selectQuery.php";
  var consultarProductos = "select idprod, nombre_plastico as nombre from productos order by nombre_plastico asc";
  
  $.getJSON(url, {query: ""+consultarProductos+""}).done(function(request){
    var resultadoProductos = request["resultado"];
    var productos = new Array();
    var idprods = new Array();
    for (var i in resultadoProductos) {
      productos.push(resultadoProductos[i]["nombre"]);
      idprods.push(resultadoProductos[i]["idprod"]);
    }
    
    var consultarEntidades = "select distinct entidad from productos order by entidad asc, nombre_plastico asc";
    
    $.getJSON(url, {query: ""+consultarEntidades+""}).done(function(request){
      var resultadoEntidades = request["resultado"];
      var entidades = new Array();
      for (var i in resultadoEntidades) {
        entidades.push(resultadoEntidades[i]["entidad"]);
      }
      
      var consultarUsuarios = "select iduser, apellido, nombre from usuarios order by sector asc, apellido asc, nombre asc";
    
      $.getJSON(url, {query: ""+consultarUsuarios+""}).done(function(request){
        var resultadoUsuarios = request["resultado"];
        var idusers = new Array();
        var nombresUsuarios = new Array();
        var apellidosUsuarios = new Array();
        for (var i in resultadoUsuarios) {
          idusers.push(resultadoUsuarios[i]["iduser"]);
          apellidosUsuarios.push(resultadoUsuarios[i]["apellido"]);
          nombresUsuarios.push(resultadoUsuarios[i]["nombre"]);
        }
        
        var tabla = '<table id="parametros" name="parametros" class="tabla2">';
        var tr = '<tr>\n\
                    <th colspan="5" class="tituloTabla">STOCK</th>\n\
                  </tr>';
        tr += '<tr>\n\
                <td class="fondoVerde">\n\
                  <input type="radio" name="criterio" value="entidadStock" checked="checked">\n\
                </td>\n\
                <th>Entidad:</th>\n\
                  <td colspan="3">\n\
                    <select name="entidad" id="entidadStock" style="width: 100%">\n\
                      <option value="todos" selected="yes">---TODOS---</option>';
        for (var j in entidades) {
          tr += '<option value="'+entidades[j]+'">'+entidades[j]+'</option>';
        }  
        tr += '   </select>\n\
                </td>\n\
              </tr>';
        tr += '<tr>\n\
                <td class="fondoVerde">\n\
                  <input type="radio" name="criterio" value="productoStock">\n\
                </td>\n\
                <th>Producto:</th>\n\
                <td align="center" colspan="3">\n\
                  <input type="text" id="productoStock" name="producto" class="agrandar size="9" onkeyup=\'showHint(this.value, "#productoStock", "")\'>\n\
                </td>\n\
              </tr>';
        tr += '<tr>\n\
                <td class="fondoVerde">\n\
                  <input type="radio" name="criterio" value="totalStock">\n\
                </td>\n\
                <td colspan="4" class="negrita" style="text-align: left">Total de plásticos en bóveda</td>\n\
              </tr>';
        tr += '<tr>\n\
                <th colspan="5" class="centrado">MOVIMIENTOS</th>\n\
              </tr>';
        tr += '<tr>\n\
                <td class="fondoVerde">\n\
                  <input type="radio" name="criterio" value="entidadMovimiento">\n\
                </td>\n\
                <th>Entidad:</th>\n\
                  <td colspan="3">\n\
                    <select name="entidad" id="entidadMovimiento" style="width: 100%">\n\
                      <option value="todos" selected="yes">---TODOS---</option>';
        for (var j in entidades) {
          tr += '<option value="'+entidades[j]+'">'+entidades[j]+'</option>';
        }   
        tr += '   </select>\n\
                </td>\n\
              </tr>';
        tr += '<tr>\n\
                <td class="fondoVerde">\n\
                  <input type="radio" name="criterio" value="productoMovimiento">\n\
                </td>\n\
                <th>Producto:</th>\n\
                <td align="center" colspan="3">\n\
                  <input type="text" id="productoMovimiento" name="producto" class="agrandar" size="9" onkeyup=\'showHint(this.value, "#productoMovimiento", "")\'>\n\
                </td>\n\
              </tr>';
        tr += '<tr>\n\
                  <td class="fondoNaranja">\n\
                    <input type="radio" name="criterioFecha" value="intervalo">\n\
                  </td>\n\
                  <th>Entre:</th>\n\
                  <td>\n\
                    <input type="date" name="inicio" id="inicio" style="width:100%; text-align: center" min="2016-07-01">\n\
                  </td>\n\
                  <td>y:</td>\n\
                  <td>\n\
                    <input type="date" name="fin" id="fin" style="width:100%; text-align: center" min="2016-10-01">\n\
                  </td>\n\
                </tr>';
        tr += '<tr>\n\
                <td class="fondoNaranja">\n\
                  <input type="radio" name="criterioFecha" value="mes">\n\
                </td>\n\
                <th>Mes:</th>\n\
                <td>\n\
                  <select id="mes" name="mes" style="width:100%">\n\
                    <option value="todos" selected="yes">--Seleccionar--</option>\n\
                    <option value="01">Enero</option>\n\
                    <option value="02">Febrero</option>\n\
                    <option value="03">Marzo</option>\n\
                    <option value="04">Abril</option>\n\
                    <option value="05">Mayo</option>\n\
                    <option value="06">Junio</option>\n\
                    <option value="07">Julio</option>\n\
                    <option value="08">Agosto</option>\n\
                    <option value="09">Setiembre</option>\n\
                    <option value="10">Octubre</option>\n\
                    <option value="11">Noviembre</option>\n\
                    <option value="12">Diciembre</option>\n\
                  </select>\n\
                </td>\n\
                <th>Año:</th>\n\
                <td>\n\
                  <select id="año" name="año" style="width:100%">\n\
                    <option value="2017">2017</option>\n\
                    <option value="2018" selected="yes">2018</option>\n\
                    <option value="2019">2019</option>\n\
                    <option value="2020">2020</option>\n\
                    <option value="2021">2021</option>\n\
                  </select>\n\
                </td>\n\
              </tr>';
        tr += '<tr>\n\
                <td class="fondoNaranja">\n\
                  <input type="radio" name="criterioFecha" value="todos" checked="checked">\n\
                </td>\n\
                <th>TODOS</th>';
        tr += '<tr>\n\
                <th>Tipo:</th>\n\
                <td>\n\
                  <select id="tipo" name="tipo" style="width:100%">\n\
                    <option value="Todos" selected="yes">---TODOS---</option>\n\
                    <option value="Retiro">Retiro</option>\n\
                    <option value="Ingreso">Ingreso</option>\n\
                    <option value="Renovaci&oacute;n">Reno</option>\n\
                    <option value="Destrucci&oacute;n">Destrucci&oacute;n</option>\n\
                  </select>\n\
                </td>\n\
              </tr>';
        tr += '<tr>\n\
                <th>Usuario:</th>\n\
                <td colspan="3">\n\
                  <select name="usuario" id="usuario" style="width: 100%">\n\
                    <option value="todos">---TODOS---</option>';
        for (var j in nombresUsuarios) {
            tr += '<option value="'+idusers[j]+'">'+nombresUsuarios[j]+' '+apellidosUsuarios[j]+'</option>';
          }      
        tr += '   </select>\n\
                </td>\n\
              </tr>';
        tr += '<tr>\n\
                <td colspan="5" class="pieTabla">\n\
                  <input type="button" class="btn btn-success" name="consultar" id="realizarBusqueda" value="Consultar" align="center">\n\
                </td>\n\
              </tr>';
        tabla += tr;
        tabla += '</table>';
        
        var mostrar = '';
        //var volver = '<a href="estadisticas.php">Volver</a>';
        mostrar += tabla;
        mostrar += '<br><br>';
        //mostrar += volver;
        mostrar += '<br><br>';
        $(selector).html(mostrar);
        
        if (hint !== '') {
          if (tipo === 'prodStock') {
            $("#productoStock").val(hint);
            showHint(hint, "#productoStock", idProd);
            $("#productoStock").parent().prev().prev().children().prop("checked", true);
            $("#productoMovimiento").val('');
            $("#productoStock").focus();
          }
          else {
            $("#productoMovimiento").val(hint);
            showHint(hint, "#productoMovimiento", idProd);
            $("#productoMovimiento").parent().prev().prev().children().prop("checked", true);
            $("#productoStock").val('');
            $("#productoMovimiento").focus();
          }    
        }
        else {
          var sel = '';
          if (tipo === 'entStock') {
            $('#entidadStock option[value="'+entidad+'"]').attr("selected", true);
            sel = '#entidadStock';
          }
          else {
            $('#entidadMovimiento option[value="'+entidad+'"]').attr("selected", true);
            sel = '#entidadMovimiento';
          }
          $(sel).parent().prev().prev().children().prop("checked", true);
          $(sel).focus();
        }
        if (tipo === 'totalStock') {
          $('input:radio [value="totalStock"]').attr('checked', true);
        }
      });  
    });  
  });
}

/***********************************************************************************************************************
/// *********************************************** FIN FUNCIONES BÚSQUEDAS *********************************************
************************************************************************************************************************
**/


/***********************************************************************************************************************
/// ************************************************* FUNCIONES PRODUCTOS **********************************************
************************************************************************************************************************
*/

/**
  \brief Función que carga en el selector pasado como parámetro la tabla para ver los productos.
  @param {String} selector String con el selector en donde se debe mostrar la tabla.
  @param {Integer} idProd Entero con el identificador del producto a cargar.
*/
function cargarProducto(idProd, selector){
  var url = "data/selectQuery.php";
  var query = 'select idprod, nombre_plastico, entidad, codigo_emsa, bin, codigo_origen, contacto, snapshot, stock, alarma1, alarma2, ultimoMovimiento, comentarios from productos where idprod='+idProd;
  
  $.getJSON(url, {query: ""+query+""}).done(function(request) {
    var resultado = request["resultado"];
    var total = request["rows"];
    if (total >= 1) {
    var stock = parseInt(resultado[0]['stock'], 10);
    var alarma1 = parseInt(resultado[0]['alarma1'], 10);
    var alarma2 = parseInt(resultado[0]['alarma2'], 10);
    var fecha = resultado[0]['ultimoMovimiento'];
    var ultimoMovimiento = '';
    if (fecha !== null) {
      var temp = fecha.split('-');
      ultimoMovimiento = temp[2]+"/"+temp[1]+"/"+temp[0];
    }
    }
    var mostrar = "";
    var formu = '<form method="post" id="productUpdate" name="productUpdate" action="producto.php">';
    var tabla = '<table class="tabla2" name="producto">';
    var tr = '<th colspan="3" class="centrado tituloTabla">DATOS DEL PRODUCTO</th>';

    tr += '<tr>\n\
            <th align="left"><font class="negra">Entidad:</font></th><td align="center" colspan="2"><input type="text" name="entidad" id="entidad" tabindex="4" class="agrandar" style="width:100%; text-align: center"></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Nombre:</font></th>\n\
              <td align="center" colspan="2"><input type="text" name="nombre" id="nombre" tabindex="5" class="agrandar" maxlength="35" style="width:100%; text-align: center"></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Código EMSA:</font></th>\n\
              <td align="center" colspan="2"><input type="text" name="codigo_emsa" id="codigo_emsa" tabindex="6" class="agrandar" maxlength="35" style="width:100%; text-align: center"></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Código Origen:</font></th>\n\
              <td align="center" colspan="2"><input type="text" name="codigo_origen" id="codigo_origen" tabindex="7" class="agrandar" maxlength="35" style="width:100%; text-align: center"></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Contacto:</font></th>\n\
              <td align="center" colspan="2"><input type="text" id="contacto" name="contacto2" tabindex="8" class="agrandar" maxlength="35" size="9"></td>\n\
           </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Foto:</font></th>\n\
              <td align="center" colspan="2"><input type="text" id="nombreFoto" name="nombreFoto" tabindex="9" class="agrandar" maxlength="35" size="9"></td>\n\
           </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">BIN:</font></th>\n\
              <td align="center" colspan="2"><input type="text" name="bin" id="bin" tabindex="10" class="agrandar" maxlength="35" style="width:100%; text-align: center"></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Stock:</font></th>\n\
              <td align="center" colspan="2"><input type="text" id="stockProducto" name="stockProducto" tabindex="-1" class="agrandar" maxlength="35" size="9"></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Alarma 1:</font></th>\n\
              <td align="center" colspan="2"><input type="text" id="alarma1" name="alarma1" tabindex="11" class="agrandar" maxlength="35" size="9"></td>\n\
           </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Alarma 2:</font></th>\n\
              <td align="center" colspan="2"><input type="text" id="alarma2" name="alarma2" tabindex="12" class="agrandar" maxlength="35" size="9"></td>\n\
           </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Último Movimiento:</font></th>\n\
              <td align="center" colspan="2"><input type="text" id="ultimoMovimiento" name="ultimoMovimiento" class="agrandar" maxlength="35" size="9"></td>\n\
           </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Comentarios:</font></th>\n\
              <td align="center" colspan="2"><input type="textarea" id="comentarios" name="comentarios" tabindex="13" class="agrandar" maxlength="35" size="9"></td>\n\
          </tr>';
    tr += '<tr>\n\
              <td style="width: 33%;border-right: 0px;"><input type="button" value="EDITAR" id="editarProducto" name="editarProducto" tabindex="3" class="btn btn-primary" align="center"/></td>\n\
              <td style="width: 33%;border-left: 0px;border-right: 0px;"><input type="button" value="ACTUALIZAR" id="actualizarProducto" name="actualizarProducto" tabindex="14" class="btn btn-warning" align="center"/></td>\n\
              <td style="width: 33%;border-left: 0px;"><input type="button" value="ELIMINAR" id="eliminarProducto" name="eliminarProducto" class="btn btn-danger" align="center"/></td>\n\
          </tr>';
    tr += '<tr>\n\
              <td colspan="3" class="pieTabla"><input type="button" value="NUEVO" id="agregarProducto" name="agregarProducto" class="btn btn-success" align="center"/></td>\n\
          </tr>';
    tabla += tr;
    tabla += '</table>';
    formu += tabla;
    formu += '</form>';
    mostrar += formu;
    $(selector).html(mostrar);
  
    if (total >=1) {
    $("#entidad").val(resultado[0]['entidad']);
    $("#nombre").val(resultado[0]['nombre_plastico']);
    $("#codigo_emsa").val(resultado[0]['codigo_emsa']);
    $("#codigo_origen").val(resultado[0]['codigo_origen']);
    $("#contacto").val(resultado[0]['contacto']);
    $("#nombreFoto").val(resultado[0]['snapshot']);
    $("#stockProducto").val(stock.toLocaleString());
    $("#alarma1").val(alarma1);
    $("#alarma2").val(alarma2);
    $("#comentarios").val(resultado[0]['comentarios']);
    $("#ultimoMovimiento").val(ultimoMovimiento);
    $("#bin").val(resultado[0]['bin']); 
    
    //$(selector).html(mostrar);

    if ((stock < alarma1) && (stock > alarma2)) {
      $("#stockProducto").addClass('alarma1');
    }
    else {
      if (stock < alarma2) {
        $("#stockProducto").addClass('alarma2');
      }
      else {
        $("#stockProducto").addClass('resaltado');
      }
    }
    }
    inhabilitarProducto();
  }); 
}

/**
  \brief Función que carga en el selector pasado como parámetro la tabla con el campo para la búsqueda de productos.
  @param {String} selector String con el selector en donde se debe mostrar la tabla.
*/
function cargarBusquedaProductos(selector) {
  var mostrar = '';
  var tabla = '<table class="tabla2" name="busquedaProducto" style="width: 60%;">\n\
                <tr>\n\
                  <th align="left" class="tituloTabla" colspan="5"><font class="negra">BUSCAR:</font></th>\n\
                </tr>\n\
                <tr>\n\
                  <td align="center" colspan="5" class="pieTabla"><input type="text" id="productoBusqueda" name="productoBusqueda" tabindex="1" class="agrandar" size="9" onkeyup=\'showHintProd(this.value, "#productoBusqueda", ""), ""\'></td>\n\
                </tr>\n\
              </table><br>';
  mostrar += tabla;
  $(selector).html(mostrar);
  $("#productoBusqueda").focus();
}

/**
 * \brief Función que valida los datos pasados para el producto
 * @param {Boolean} nuevo Booleano que indica si estoy validando un producto nuevo a agregar, o editando uno ya ingresado.
 * @returns {Boolean} Devuelve un booleano que indica si se pasó o no la validación de los datos para el producto.
 */
function validarProducto(nuevo) {
  var entidad = $("#entidad").val();
  var nombre = $("#nombre").val();
  var stockProducto1 = $("#stcokProducto").val();
  var alarma1 = parseInt($("#alarma1").val(), 10);
  var alarma2 = parseInt($("#alarma2").val(), 10);
  var seguir = true;
  var al1Ingresada = false;
  var al2Ingresada = false;
    
  if ((entidad === '') || (entidad === null)) {
    alert('El campo Entidad NO puede estar vacío. Por favor verifique.');
    $("#entidad").focus();
    seguir = false;
    return false;
  }
  
  if (seguir){
    if ((nombre === '') || (nombre === null)) {
      alert('El Nombre del producto NO puede estar vacío. Por favor verifique.');
      $("#nombre").focus();
      seguir = false;
      return false;
    }
  }
  
  /// Si estoy agregando un producto nuevo, y si se ingresa el stock inicial, debo validarlo:
  if (nuevo) {
    if (seguir) {
      if (stockProducto1 !== undefined){
        var stockProducto = parseInt(stockProducto1, 10);
        var stock1 = validarEntero(stockProducto);
        if ((!stock1) || (stockProducto <= 0)) {
          alert('El stock inicial debe ser un entero mayor que 0. Por favor verifique.');
          $("#stockProducto").val('');
          $("#stockProducto").focus();
          seguir = false;
          return false;
        }
      }
      else {
        $("#stockProducto").val(0);
      }
    }
  }
  
  if (seguir) {
    if ((alarma1 === '') || (alarma1 === null))
      {
      alert('La alarma 1 no puede ser nula ni estar vacía.\nPor favor verifique');
      $("#alarma1").focus();
      seguir = false;
      return false;
    }
    else {
      var al1 = validarEntero(alarma1);
      if ((!al1) || (alarma1 <= 0)) {
        alert('La alarma 1 debe ser un entero mayor que 0. Por favor verifique.');
        $("#alarma1").val('');
        $("#alarma1").focus();
        seguir = false;
        return false;
      }
      else {
        al1Ingresada = true;
      }
    }
  }
  
  if (seguir) {
    if ((alarma2 === '') || (alarma2 === null))
      {
      alert('La alarma 2 no puede ser nula ni estar vacía.\nPor favor verifique');
      $("#alarma2").focus();
      seguir = false;
      return false;
    }
    else {
      var al2 = validarEntero(alarma2);
      if ((!al2) || (alarma2 <= 0)) {
        alert('La alarma 2 debe ser un entero mayor que 0. Por favor verifique.');
        $("#alarma2").val('');
        $("#alarma2").focus();
        seguir = false;
        return false;
      }
      else {
        al2Ingresada = true;
      }
    }
  }
  
  if (seguir) {
    if (al1Ingresada && al2Ingresada) {
      if (alarma2 >= alarma1) {
        alert('La alarma 2 (nivel crítico) DEBE ser menor que la alarma 1 (nivel advertencia).\nPor favor verifique.');
        $("#alarma2").val('');
        $("#alarma2").focus();
        seguir = false;
        return false;
      }
    }
  }
  
  if (seguir) {
    return true;
  }
  else {
    return false;
  }
  
}

/**
  \brief Función que deshabilita los input del form Producto.
*/
function inhabilitarProducto(){
  document.getElementById("nombre").disabled = true;
  document.getElementById("entidad").disabled = true;
  document.getElementById("codigo_origen").disabled = true;
  document.getElementById("codigo_emsa").disabled = true;
  document.getElementById("contacto").disabled = true;
  document.getElementById("nombreFoto").disabled = true;
  document.getElementById("bin").disabled = true;
  document.getElementById("alarma1").disabled = true;
  document.getElementById("alarma2").disabled = true;
  document.getElementById("ultimoMovimiento").disabled = true;
  document.getElementById("comentarios").disabled = true;
  document.getElementById("stockProducto").disabled = true;
  document.getElementById("editarProducto").value = "EDITAR";
  document.getElementById("actualizarProducto").disabled = true;
}

/**
  \brief Función que habilita los input del form Producto.
*/
function habilitarProducto(){
  document.getElementById("nombre").disabled = false;
  document.getElementById("entidad").disabled = false;
  document.getElementById("codigo_origen").disabled = false;
  document.getElementById("codigo_emsa").disabled = false;
  document.getElementById("contacto").disabled = false;
  document.getElementById("nombreFoto").disabled = false;
  document.getElementById("bin").disabled = false;
  document.getElementById("alarma1").disabled = false;
  document.getElementById("alarma2").disabled = false;
  //document.getElementById("ultimoMovimiento").disabled = false;
  document.getElementById("comentarios").disabled = false;
  //document.getElementById("stockProducto").disabled = false;
  document.getElementById("editarProducto").value = "BLOQUEAR";
  document.getElementById("actualizarProducto").disabled = false;
}

/***********************************************************************************************************************
/// ********************************************** FIN FUNCIONES PRODUCTOS *********************************************
************************************************************************************************************************
**/



/**************************************************************************************************************************
/// ************************************************* FUNCIONES ESTADISTICAS **********************************************
***************************************************************************************************************************
*/

/**
  \brief Función que carga en el selector pasado como parámetro el formulario para realizar las estadisticas.
  @param {String} selector String con el selector en donde se debe mostrar el formulario.
*/
function cargarFormEstadisticas(selector){
  var url = "data/selectQuery.php";
  var query = "select distinct entidad from productos order by entidad asc, nombre_plastico asc";

  $.getJSON(url, {query: ""+query+""}).done(function(request) {
    var entidades = request["resultado"];
    var mostrar = '';
    var titulo = '<h2 id="titulo" class="encabezado">CONSULTAR ESTAD&Iacute;STICAS</h2>';
    var formu = '<form method="POST" id="graficar" action="graficar.php">';
    var tabla = '<table id="estadisticas" name="estadisticas" class="tabla2">';
    var tr = '<tr>\n\
                <th colspan="5" class="centrado tituloTabla">CRITERIOS</th>\n\
              </tr>';
    tr += '<tr>\n\
            <td class="fondoVerde"><input type="radio" name="criterio" value="entidadMovimiento" checked="true"></td>\n\
            <th>Entidad:</th>\n\
            <td colspan="3">\n\
              <select name="entidad" id="entidadGrafica" style="width: 100%">\n\
                <option value="todos" selected="yes">---TODOS---</option>';
    for (var i in entidades) {
      tr += '<option value="'+entidades[i]['entidad']+'">'+entidades[i]['entidad']+'</option>';
    }
    tr += '   </select>\n\
            </td>\n\
          </tr>';
    tr += '<tr>\n\
            <td class="fondoVerde"><input type="radio" name="criterio" value="productoMovimiento"></td>\n\
            <th>Producto:</th>\n\
            <td align="center" colspan="3"><input type="text" id="productoMovimiento" name="producto" class="agrandar" size="9" onkeyup="showHint(this.value, \'#productoMovimiento\', \'\')"></td>\n\
          </tr>';
    tr += '<th colspan="5" class="centrado">FECHAS</th>';
    tr += '<tr>\n\
            <th colspan="2">Inicio:</th>\n\
            <td>\n\
              <select id="mesInicio" name="mesInicio" style="width:100%">\n\
                <option value="todos" selected="yes">--Seleccionar--</option>\n\
                <option value="01">Enero</option>\n\
                <option value="02">Febrero</option>\n\
                <option value="03">Marzo</option>\n\
                <option value="04">Abril</option>\n\
                <option value="05">Mayo</option>\n\
                <option value="06">Junio</option>\n\
                <option value="07">Julio</option>\n\
                <option value="08">Agosto</option>\n\
                <option value="09">Setiembre</option>\n\
                <option value="10">Octubre</option>\n\
                <option value="11">Noviembre</option>\n\
                <option value="12">Diciembre</option>\n\
              </select>\n\
            </td>\n\
            <th>Año:</th>\n\
            <td>\n\
              <select id="añoInicio" name="añoInicio" style="width:100%">\n\
                <option value="2017" selected="yes">2017</option>\n\
                <option value="2018">2018</option>\n\
                <option value="2019">2019</option>\n\
                <option value="2020">2020</option>\n\
                <option value="2021">2021</option>\n\
              </select>\n\
            </td>\n\
          </tr>';
    tr += '<tr>\n\
            <th colspan="2">Fin:</th>\n\
            <td>\n\
              <select id="mesFin" name="mesFin" style="width:100%">\n\
                <option value="todos" selected="yes">--Seleccionar--</option>\n\
                <option value="01">Enero</option>\n\
                <option value="02">Febrero</option>\n\
                <option value="03">Marzo</option>\n\
                <option value="04">Abril</option>\n\
                <option value="05">Mayo</option>\n\
                <option value="06">Junio</option>\n\
                <option value="07">Julio</option>\n\
                <option value="08">Agosto</option>\n\
                <option value="09">Setiembre</option>\n\
                <option value="10">Octubre</option>\n\
                <option value="11">Noviembre</option>\n\
                <option value="12">Diciembre</option>\n\
              </select>\n\
            </td>\n\
            <th>Año:</th>\n\
            <td>\n\
              <select id="añoFin" name="añoFin" style="width:100%">\n\
                <option value="2017" selected="yes">2017</option>\n\
                <option value="2018">2018</option>\n\
                <option value="2019">2019</option>\n\
                <option value="2020">2020</option>\n\
                <option value="2021">2021</option>\n\
              </select>\n\
            </td>\n\
          </tr>';
    tr += '<tr>\n\
            <th colspan="2">Tipo:</th>\n\
            <td colspan="3">\n\
              <select id="tipo" name="tipo" style="width:100%">\n\
                <option value="Todos" selected="yes">---TODOS---</option>\n\
                <option value="Retiro">Retiro</option>\n\
                <option value="Ingreso">Ingreso</option>\n\
                <option value="Renovaci&oacute;n">Reno</option>\n\
                <option value="Destrucci&oacute;n">Destrucci&oacute;n</option>\n\
              </select>\n\
            </td>\n\
          </tr>';
//    tr += '<tr>\n\
//            <th>Usuario:</th>\n\
//            <td colspan="3">\n\
//              <select name="usuario" id="usuario" style="width: 100%">\n\
//                <option value="todos">---TODOS---</option>\n\
//              </select>\n\
//            </td>\n\
//          </tr>';
    tr += '<tr>\n\
            <td colspan="5" class="pieTabla"><input type="button" class="btn btn-success" name="realizarGrafica" id="realizarGrafica" value="Consultar" align="center"></td>\n\
            <td style="display:none"><input type="text" id="consulta" name="consulta" value=""></td>\n\
            <td style="display:none"><input type="text" id="fechaInicio" name="fechaInicio" value=""></td>\n\
            <td style="display:none"><input type="text" id="fechaFin" name="fechaFin" value=""></td>\n\
            <td style="display:none"><input type="text" id="mensaje" name="mensaje" value=""></td>\n\
            <td style="display:none"><input type="text" id="hacerGrafica" name="hacerGrafica" value=""></td>\n\
          </tr>';
    tabla += tr;
    tabla += '</table>';
    formu += tabla;
    formu += '</form><br>';
    mostrar += titulo;
    mostrar += formu;
    $(selector).html(mostrar);
  });
}

/**
  \brief Función que carga en el selector pasado como parámetro una imágen con la gráfica.
  @param {String} selector String con el selector en donde se debe mostrar la gráfica.
*/
function cargarGrafica(selector){
  var mostrar = '';
  var titulo = '<h2 id="titulo" class="encabezado">RESULTADO ESTAD&Iacute;STICAS</h2>';
  var grafica = '<img src="graficar.php" width="750px" height="350px">';
  var volver = '<a href="estadisticas.php">Volver</a>';
  mostrar += titulo;
  mostrar += grafica;
  mostrar += '<br><br>';
  mostrar += volver;
  mostrar += '<br><br>';
  $(selector).html(mostrar);
}

/**************************************************************************************************************************
/// ********************************************** FIN FUNCIONES ESTADISTICAS *********************************************
***************************************************************************************************************************
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
  var parametros = jQuery(location).attr('search');
  var remplaza = /\+|%20/g; 
  if (parametros) {
    parametros = unescape(parametros);
    parametros = parametros.replace(remplaza, " ");
  }
  ///Según en que url esté, es lo que se carga:
  switch (urlActual) {
    case "/controlstock/movimiento.php":{
                                        if (parametros) {
                                          var temp = parametros.split('?');
                                          var temp1 = temp[1].split('&');
                                          var temp2 = temp1[0].split('=');
                                          var temp3 = temp1[1].split('=');
                                          var temp4 = temp1[2].split('=');
                                          var h = temp2[1]; 
                                          var tipo = decodeURI(temp4[1]);
                                          var idprod = parseInt(temp3[1], 10);
                                          setTimeout(function(){cargarMovimiento("#main-content", h, idprod, tipo)}, 100);                                          
                                        }
                                        else {
                                          setTimeout(function(){cargarMovimiento("#main-content", "", "-1", "")}, 100);
                                        }
                                        break;    
                                      }
    case "/controlstock/index.php": break;
                                    
    case "/controlstock/producto.php":  setTimeout(function(){cargarBusquedaProductos("#selector")}, 100);
                                        setTimeout(function(){cargarProducto(0, "#content")}, 100);
                                        break;                                                                      
    case "/controlstock/busquedas.php": {
                                        if (parametros) {
                                          var temp = parametros.split('?');
                                          var temp1 = temp[1].split('&');
                                          var temp2 = temp1[0].split('=');
                                          var temp3 = temp1[1].split('=');
                                          var temp4 = temp1[2].split('=');
                                          var temp5 = temp1[3].split('=');
                                          var hint = temp2[1];
                                          var tipMov = temp3[1];
                                          var id = temp4[1];
                                          var ent = temp5[1];
                                          setTimeout(function(){cargarFormBusqueda("#fila", hint, tipMov, id, ent)}, 30); 
                                        }
                                        else {
                                          setTimeout(function(){cargarFormBusqueda("#fila", '', '', '', '')}, 30);
                                        }
                                        break;
                                        }
    case "/controlstock/estadisticas.php":  if (parametros) {
                                              var temp = parametros.split('?');
                                              var temp1 = temp[1].split('=');
                                              var hacerGrafica = temp1[1];
                                              if (hacerGrafica ===  '1') {
                                                setTimeout(function(){cargarGrafica("#main-content")}, 100);
                                              }
                                              else {
                                                alert('ver por que llega hasta acá...');
                                              }
                                            }
                                            else {
                                              setTimeout(function(){cargarFormEstadisticas("#main-content")}, 100);
                                            }  
                                            break;  
    case "/controlstock/editarMovimiento.php":  if (parametros) {
                                                  var temp = parametros.split('?');
                                                  var temp1 = temp[1].split('=');
                                                  var idmov = temp1[1];
                                                  setTimeout(function(){cargarEditarMovimiento(idmov, "#main-content")}, 100);
                                                }
                                                else {
                                                  setTimeout(function(){cargarEditarMovimiento(1, "#main-content")}, 100);
                                                }  
                                              break;                                       
    default: break;
  }  
  
///Disparar funcion cuando algún elemento de la clase agrandar reciba el foco.
///Se usa para resaltar el elemento seleccionado.
$(document).on("focus", ".agrandar", function (){
  $(this).css("font-size", 24);
  $(this).css("background-color", "#e7f128");
  $(this).css("font-weight", "bolder");
  $(this).css("color", "red");
  $(this).css("height", "100%");
  //$(this).css("max-width", "100%");
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
  
  
/***************************************************************************************************************************
/// Comienzan las funciones que manejan los eventos relacionados a los MOVIMIENTOS como ser creación, edición y eliminación.
****************************************************************************************************************************
*/

///Disparar funcion al cambiar el elemento elegido en el select con las sugerencias para los productos.
///Cambia el color de fondo para resaltarlo, carga un snapshot del plástico si está disponible, y muestra
///el stock actual.
$(document).on("change focusin", "#hint", function (){
  var rutaFoto = 'images/snapshots/';
  var nombreFoto = $(this).find('option:selected').attr("name");
  $(this).css('background-color', '#ffffff');
  //$(this).find('option:selected').css('background-color', '#ffffff');
  
  $("#snapshot").remove();
  $("#stock").remove();
  var stock = $("#hint").find('option:selected').attr("stock");
  var alarma1 = $("#hint").find('option:selected').attr("alarma1");
  alarma1 = parseInt(alarma1, 10);
  var alarma2 = $("#hint").find('option:selected').attr("alarma2");
  alarma2 = parseInt(alarma2, 10);
  if ((stock === 'undefined') || ($(this).find('option:selected').val() === 'NADA')) {
    stock = '';
  }
  else {
    stock = parseInt(stock, 10);
  }
  var resaltado = '';
  if ((stock < alarma1) && (stock > alarma2)){
    resaltado = 'alarma1';
  }
  else {
    if (stock < alarma2) {
      resaltado = 'alarma2';
    }
    else {
      resaltado = 'resaltado';
    }  
  }
  var mostrar = '<img id="snapshot" name="hint" src="'+rutaFoto+nombreFoto+'" alt="No se cargó la foto aún." height="127" width="200"></img>';
  mostrar += '<p id="stock" name="hint" style="padding-top: 10px"><b>Stock actual: <b><font class="'+resaltado+'" style="font-size:1.6em">'+stock.toLocaleString()+'</font></p>';
  //$(this).css('background-color', '#efe473');
  $(this).css('background-color', '#9db7ef');
  //$(this).find('option:selected').css('background-color', '#79ea52');
  $("#hint").after(mostrar);
  });
  
///Disparar función al hacer CLICK con el mouse sobre alguna de las OPTION del select HINT ó al darle ENTER sobre los mismos. 
///Básicamente, la idea es que al presionar ENTER o al hacer CLICK, se ejecute la opción por defecto cosa de ahorrar tiempo.  
///En el caso de ser llamado desde MOVIMIENTOS, pasa al foco al campo CANTIDAD. Mientras que en el caso de BUSQUEDAS hace el SUBMIT.
$(document).on("click keypress", "#hint", function (e){ 
  //close dropdown
  //alert('en el evento: '+e.which);
  //$("#hint").attr('size',0);
  var temp = "#"+$(this).prev().attr("id");
  var sel = $(this).find('option:selected').val();

  switch (temp) {
    case "#producto": if ((sel !== 'NADA') && ((e.which === 1) ||(e.which === 13))) {
                        $("#cantidad").focus();
                      }
                      break;
    case "#productoStock": if ((sel !== 'todos') && ((e.which === 1) || (e.which === 13))) {
                            realizarBusqueda();
                          } 
                          break;
    case "#productoMovimiento": if ((sel !== 'todos') && ((e.which === 1) || (e.which === 13))) {
                                  realizarBusqueda();
                                } 
                                break;
    default: break;
  }  
  
  
}); 

///Disparar funcion al hacer clic en el botón para agregar el movimiento.
$(document).on("click", "#agregarMovimiento", function (){
  //$(this).css('background-color', '#efe473');
  var seguir = true;
  seguir = validarMovimiento();
  if (seguir) {
    agregarMovimiento();
  }
});

///Disparar función al hacer enter estando en el elemento Cantidad.
///Básicamente, la idea es hacer "el submit" cosa de ahorrar tiempo en el ingreso.
$(document).on("keypress", "#cantidad", function(e) {
  if(e.which === 13) {
    var seguir = true;
    seguir = validarMovimiento();
    if (seguir) {
      agregarMovimiento();
    }
  }  
});

///Disparar función al hacer click en el botón de EDITAR del form para los movimientos.
///Cambia entre habilitar o deshabilitar los input del form cosa de poder hacer la edición del movimiento.
$(document).on("click", "#editarMovimiento", function (){
  var nombre = $(this).val();
  if (nombre === 'EDITAR') {
    habilitarMovimiento();
  }
  else {
    inhabilitarMovimiento();
  }
});

///Dispara función para realizar los cambios con las modificaciones para el movimiento.
$(document).on("click", "#actualizarMovimiento", function (){
  /* *** Por ahora se comentan los otros campos pues solo se puede editar el comentario: ******
  var fecha = $("#fecha").val();
  var nombre = $("#nombre").val();
  var hora = $("#hora").val();
  var tipo = $("#tipo").val();
  var cantidad = $("#cantidad").val();
  */
  actualizarMovimiento();
});

///Disparar función al hacer enter estando en el elemento Comentarios.
///Básicamente, la idea es hacer "el submit" cosa de ahorrar tiempo en la actualización del comentario.
$(document).on("keypress", "#comentarios", function(e) {
  if(e.which === 13) {
    actualizarMovimiento();
  }  
});

///Disparar función al hacer enter estando en el elemento Producto.
///Básicamente, la idea es pasar el foco al select hint cosa de ahorrar tiempo en el ingreso.
$(document).on("keypress", "#producto", function(e) {
  if(e.which === 13) {
    //alert('enter');
    $("#hint").focus();
  }  
});

/*******************************************************************************************************************************
/// ***************************************************** FIN MOVIMIENTOS ******************************************************
********************************************************************************************************************************
*/



/*****************************************************************************************************************************
/// Comienzan las funciones que manejan los eventos relacionados a las PRODUCTOS como ser creación, edición y eliminación.
******************************************************************************************************************************
*/

///Disparar funcion al cambiar el elemento elegido en el select con las sugerencias para los productos.
///Cambia el color de fondo para resaltarlo, carga un snapshot del plástico si está disponible, y muestra
///el stock actual.
$(document).on("change focusin", "#hintProd", function (){
  $("#hintProd").css('background-color', '#9db7ef');
  var rutaFoto = 'images/snapshots/';
  var nombreFoto = $(this).find('option:selected').attr("name");
  $(this).css('background-color', '#ffffff');
  
  $("#snapshot").remove();
  $("#stock").remove();
  $("#stock").removeClass('alarma1');
  $("#stock").removeClass('alarma2');
  $("#stock").removeClass('resaltado');
  
  var idProd = $(this).find('option:selected').val();
  var stock = $("#hintProd").find('option:selected').attr("stock");
  var alarma1 = $("#hintProd").find('option:selected').attr("alarma1");
  alarma1 = parseInt(alarma1, 10);
  var alarma2 = $("#hintProd").find('option:selected').attr("alarma2");
  alarma2 = parseInt(alarma2, 10);
  if ((stock === 'undefined') || ($(this).find('option:selected').val() === 'NADA')) {
    stock = '';
  }
  else {
    stock = parseInt(stock, 10);
  }
  var resaltado = '';
  if ((stock < alarma1) && (stock > alarma2)){
    resaltado = 'alarma1';
  }
  else {
    if (stock < alarma2) {
      resaltado = 'alarma2';
    }
    else {
      resaltado = 'resaltado';
    }  
  }
  
  var mostrar = '<img id="snapshot" name="hintProd" src="'+rutaFoto+nombreFoto+'" alt="No se cargó la foto aún." height="127" width="200"></img>';
  mostrar += '<p id="stock" name="hintProd" style="padding-top: 10px"><b>Stock actual: <b><font class="'+resaltado+'" style="font-size:1.6em">'+stock.toLocaleString()+'</font></p>';
  $(this).css('background-color', '#9db7ef');
  $("#hintProd").after(mostrar);
  if (idProd !== 'NADA') {
    cargarProducto(idProd, "#content");
  }
  else {
    $("#entidad").val('');
    $("#nombre").val('');
    $("#codigo_emsa").val('');
    $("#codigo_origen").val('');
    $("#contacto").val('');
    $("#nombreFoto").val('');
    $("#bin").val(''); 
    $("#stockProducto").val('');
    $("#alarma1").val('');
    $("#alarma2").val('');
    $("#ultimoMovimiento").val('');
    $("#comentarios").val('');
  }
});

///Disparar función al hacer CLICK con el mouse sobre alguna de las OPTION del select HINTPROD ó al darle ENTER sobre los mismos. 
///Básicamente, la idea es que al presionar ENTER o al hacer CLICK, se pase automáticamente al elemento Cantidad cosa de ahorrar tiempo.  
///Además, al hacer click en alguna de las opciones del select se minimiza (por ahora comentado).
/// Por ahora se comenta pues quizás es mejor que quede abierto:
///
///NOTA: POR AHORA SE QUITÓ el evento CLICK. Ver bien si sirve o NO.
$(document).on("keypress", "#hintProd", function (e){ 
  //close dropdown
  //alert('en el evento:'+e.which);
  //$("#hint").attr('size',0);
  if (($("#hintProd").find('option:selected').val() !== 'NADA') && ((e.which === 1) ||(e.which === 13))) {
    //$("#editarProducto").focus();
    habilitarProducto();
    $("#entidad").focus();
  }  
}); 

///Disparar función al hacer enter estando en el elemento productoBusqueda.
///Esto hace que se pase el foco al select hintProd para ahorrar tiempo.
$(document).on("keypress", "#productoBusqueda", function(e) {
  if(e.which === 13) {
    $("#hintProd").focus();
  }  
});

///Dispara función para realizar los cambios con las modificaciones para el producto (luego de validar los datos obviamente).
$(document).on("click", "#actualizarProducto", function (){
  var entidad = $("#entidad").val();
  var nombre = $("#nombre").val();
  var alarma1 = $("#alarma1").val();
  var alarma2 = $("#alarma2").val();
  var codigo_emsa = $("#codigo_emsa").val();
  var contacto = $("#contacto").val();
  var nombreFoto = $("#nombreFoto").val();
  var codigo_origen = $("#codigo_origen").val();
  var idProducto = $("#hintProd").val();
  var comentarios = $("#comentarios").val();
  var bin = $("#bin").val();
  var stock = $("#stockProducto").val();
  
  if ((idProducto === 'NADA')||($("#hintProd").length === 0)) {
    alert('Se debe seleccionar un producto para poder actualizar. Por favor verifique.');
    $("#producto").focus();
  }
  else {
    var validar = validarProducto(false);
    if (validar) {
      /*
      var entero = validarEntero(alarma1);
      if (entero) {
        alarma1 = parseInt(alarma1, 10);
        if (alarma1 < 0) {
          alert('El valor para la alarma1 del producto debe ser un entero mayor o igual a 0. Por favor verifique.');
          $("#alarma1").val('');
          $("#alarma1").focus();
          return false;
        }
      }
      else {
        alert('El valor para la alarma1 del producto debe ser un entero. Por favor verifique.');
        $("#alarma1").val('');
        $("#alarma1").focus();
        return false;
      }
      var entero1 = validarEntero(alarma2);
      if (entero1) {
        alarma2 = parseInt(alarma2, 10);
        if (alarma2 < 0) {
          alert('El valor para la alarma2 del producto debe ser un entero mayor o igual a 0. Por favor verifique.');
          $("#alarma2").val('');
          $("#alarma2").focus();
          return false;
        }
      }
      else {
        alert('El valor para la alarma2 del producto debe ser un entero. Por favor verifique.');
        $("#alarma2").val('');
        $("#alarma2").focus();
        return false;
      }
      */
      var confirmar = confirm('¿Confirma la modificación del producto con los siguientes datos?\n\nEntidad: '+entidad+'\nNombre: '+nombre+'\nCódigo Emsa: '+codigo_emsa+'\nCódigo Origen: '+codigo_origen+'\nContacto: '+contacto+'\nSnapshot: '+nombreFoto+'\nBin: '+bin+'\nAlarma1: '+alarma1+'\nAlarma2: '+alarma2+'\nComentarios: '+comentarios+"\n?");
      if (confirmar) {
        var url = "data/updateQuery.php";
        var query = "update productos set nombre_plastico= '"+nombre+"', entidad = '"+entidad+"', codigo_emsa = '"+codigo_emsa+"', codigo_origen = '"+codigo_origen+"', contacto = '"+contacto+"', snapshot = '"+nombreFoto+"', bin = '"+bin+"', alarma1 = "+alarma1+", alarma2 = "+alarma2+", comentarios = '"+comentarios+"' where idprod = "+idProducto;
        //alert(query);
        $.getJSON(url, {query: ""+query+""}).done(function(request) {
          var resultado = request["resultado"];
          if (resultado === "OK") {
            //alert('Los datos del producto se actualizaron correctamente!.');
            //showHintProd($("#producto").val(), "#producto", idProducto);
            //$("#hintProd option[value='"+idProducto+"']").attr("selected","selected");
            $("#stockProducto").removeClass('alarma1');
            $("#stockProducto").removeClass('alarma2');
            $("#stockProducto").removeClass('resaltado');
            if ((stock < alarma1) && (stock > alarma2)) {
              $("#stockProducto").addClass('alarma1');
            }
            else {
              if (stock < alarma2) {
                $("#stockProducto").addClass('alarma2');
              }
              else {
                $("#stockProducto").addClass('resaltado');
              }
            }
            var productoBusqueda = $("#productoBusqueda").val();
            var elegido = $("#hintProd").find('option:selected').val();
            showHintProd(productoBusqueda, "#productoBusqueda", elegido);
            /*
            $("#entidad").val('');
            $("#nombre").val('');
            $("#codigo_emsa").val('');
            $("#codigo_origen").val('');
            $("#stockProducto").val('');
            $("#comentarios").val('');
            $("#alarma").val('');
            $("#ultimoMovimiento").val('');
            $("#bin").val('');
            $("#producto").val('');
            $("#producto").focus();
            */
            inhabilitarProducto();
            $("#productoBusqueda").focus();
          }
          else {
            alert('Hubo un problema en la actualización. Por favor verifique.');
          }
        });
    }
      else {
      alert('Se optó por no actualizar el producto!.');
    }
    }
  }
});

///Disparar función al hacer enter estando en el elemento productoBusqueda.
///Esto hace que se pase el foco al select hintProd para ahorrar tiempo.
$(document).on("keypress", "#productUpdate", function(e) {
  
  if (e.which === 13) {
    if ($("#editarProducto").is(":focus")){
      alert('no hay que hacer el submit pues estoy en el EDITAR');
    }
    else {
      alert('hice enter en el form');
    }
  }
});

///Dispara función que da de baja el producto. NO lo borra, sino que le cambia su estado a INACTIVO.
$(document).on("click", "#eliminarProducto", function (){
  var nombre = $("#nombre").val();
  var idProducto = $("#hintProd").val();

  if ((idProducto === 'NADA')||($("#hintProd").length === 0)) {
    alert('Se debe seleccionar un producto para poder eliminar. Por favor verifique.');
    $("#producto").focus();
  }
  else {
    var confirmar = confirm('¿Seguro que desea dar de baja el producto: \n\n'+nombre+"\n?");
    if (confirmar) {
      var url = "data/updateQuery.php";
      var query = "update productos set estado = 'inactivo' where idprod = "+idProducto;
      
      $.getJSON(url, {query: ""+query+""}).done(function(request) {
        var resultado = request["resultado"];
        if (resultado === "OK") {
          alert('El producto '+nombre+' se dió de baja correctamente!.');
          $("#entidad").val('');
          $("#nombre").val('');
          $("#codigo_emsa").val('');
          $("#codigo_origen").val('');
          $("#stockProducto").val('');
          $("#comentarios").val('');
          $("#alarma1").val('');
          $("#alarma2").val('');
          $("#ultimoMovimiento").val('');
          $("#bin").val('');
          showHintProd(" ", "producto", "");
          $("#producto").val('');
          $("#producto").focus();
          inhabilitarProducto();
        }
        else {
          alert('Hubo un problema en la eliminación. Por favor verifique.');
        }
      });
    }
    else {
      alert('Se optó por NO dar de baja el producto: \n\n'+nombre);
    }
  }
  
});

///Disparar función al hacer click en el botón de EDITAR del form para los productos.
///Cambia entre habilitar o deshabilitar los input del form cosa de poder hacer la edición del producto.
$(document).on("click", "#editarProducto", function (){
  var nombre = $(this).val();
  if (nombre === 'EDITAR') {
    habilitarProducto();
  }
  else {
    inhabilitarProducto();
  }
});

///Disparar función al hacer click en el botón AGREGAR (o NUEVO) del form productos.
///Según si dice NUEVO o AGREGAR, vacío el form para poder agregar los datos o envío los datos para agregarlo a la base de datos.
$(document).on("click", "#agregarProducto", function (){
  var accion = $("#agregarProducto").val();
  if (accion === "NUEVO") {
    $("#agregarProducto").val("AGREGAR");
    $("#entidad").val('');
    $("#nombre").val('');
    $("#codigo_emsa").val('');
    $("#codigo_origen").val('');
    $("#contacto").val('');
    $("#stockProducto").val('');
    $("#comentarios").val('');
    $("#alarma1").val('');
    $("#alarma2").val('');
    $("#ultimoMovimiento").val('');
    $("#bin").val(''); 
    $("#productoBusqueda").val('');
    $("#producto").val('');
    $("#producto").attr("disabled", true);
    $("#hintProd").remove();
    $("#snapshot").remove();
    $("#stock").remove();
    habilitarProducto();
    $("#stockProducto").attr("disabled", false);
    $("#editarProducto").attr("disabled", true);
    $("#actualizarProducto").attr("disabled", true);
    $("#eliminarProducto").attr("disabled", true);
    $("#entidad").focus();
  }
  else {
    var validar = validarProducto(true);
    if (validar) {
      var entidad = $("#entidad").val();
      var nombre = $("#nombre").val();
      var codigo_emsa = $("#codigo_emsa").val();
      var codigo_origen = $("#codigo_origen").val();
      var contacto = $("#contacto").val();
      var stock = parseInt($("#stockProducto").val(), 10);
      var alarma1 = parseInt($("#alarma1").val(), 10);
      var alarma2 = parseInt($("#alarma2").val(), 10);
      var nombreFoto = $("#nombreFoto").val();
       
      var comentarios = $("#comentarios").val();
      var bin = $("#bin").val();
      
      var url = "data/updateQuery.php";
      var query = "insert into productos (entidad, nombre_plastico, codigo_emsa, codigo_origen, contacto, snapshot, stock, bin, comentarios, alarma1, alarma2, estado) values ('"+entidad+"', '"+nombre+"', '"+codigo_emsa+"', '"+codigo_origen+"', '"+contacto+"', '"+nombreFoto+"', "+stock+", '"+bin+"', '"+comentarios+"', "+alarma1+", "+alarma2+", 'activo')";
      
      var confirmar = confirm("¿Confirma que desea agregar el producto con los siguientes datos: \n\nEntidad: "+entidad+"\nNombre: "+nombre+"\nCódigo Emsa: "+codigo_emsa+"\nCódigo Origen: "+codigo_origen+"\nContacto: "+contacto+"\nSnapshot: "+nombreFoto+"\nBin: "+bin+"\nStock Inicial: "+stock+"\nAlarma1: "+alarma1+"\nAlarma2: "+alarma2+"\nComentarios: "+comentarios+"\n?");
      if (confirmar) {
        $.getJSON(url, {query: ""+query+""}).done(function(request){
          var resultado = request["resultado"];
          if (resultado === "OK") {
            alert('El producto se ingresó correctamente!.');
            inhabilitarProducto();
            $("#editarProducto").attr("disabled", true);
            $("#eliminarProducto").attr("disabled", true);
            $("#agregarProducto").val("NUEVO");
            $("#producto").attr("disabled", false);
          }
          else {
            alert('Hubo un problema en el ingreso del producto. Por favor verifique.');
          }
        });
      }
      else {
        alert('Se optó por no agregar el producto.');
        $("#entidad").val('');
        $("#nombre").val('');
        $("#bin").val(''); 
        $("#codigo_emsa").val('');
        $("#codigo_origen").val('');
        $("#contacto").val('');
        $("#stockProducto").val('');
        $("#alarma").val('');
        $("#comentarios").val('');
      }
    }  
  }   
});

/*******************************************************************************************************************************
/// ***************************************************** FIN PRODUCTOS ******************************************************
********************************************************************************************************************************
*/


/***************************************************************************************************************************
/// Comienzan las funciones que manejan los eventos relacionados a los USUARIOS como ser creación, edición y eliminación.
****************************************************************************************************************************
*/

  
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


$(document).on("click", "#user", function(){
  alert('hice click');
  $("#dialog").dialog({
    autoOpen: true,
    show: "blind",
    hide: "explode"
    });
});
    
/**************************************************************************************************************************
/// **************************************************** FIN USUARIOS *****************************************************
***************************************************************************************************************************
*/

/**************************************************************************************************************************
/// Comienzan las funciones que manejan los eventos relacionados a las BÚSQUEDAS como ser creación, edición y eliminación.
***************************************************************************************************************************
*/

///Disparar función al hacer enter estando en el elemento Producto.
///Básicamente, la idea es pasar el foco al select hint cosa de ahorrar tiempo en el ingreso.
$(document).on("keypress", "#productoStock, #productoMovimiento", function(e) { 
  if(e.which === 13) {
    //alert('enter');
    $("#hint").focus();
  }  
});      
      
///Disparar función al cambiar la entidad elegida en el select ENTIDAD. 
///Lo que hace es seleccionar automáticamente el radio button correspondiente.
$(document).on("change", "[name=entidad]", function (){
  $(this).parent().prev().prev().children().prop("checked", true);
});

///Disparar función al cambiar el mes elegido como parámetro para la búsqueda.
///Si se eligió algún mes quiere decir que la búsqueda es de movimientos y por mes/año 
///Lo que hace es seleccionar automáticamente el radio button correspondiente.
$(document).on("change", "#mes", function (){
  $(this).parent().prev().prev().children().prop("checked", true);
});

///Disparar función al cambiar el año elegido como parámetro para la búsqueda.
///Si se eligió algún año quiere decir que la búsqueda es de movimientos y por mes/año 
///Lo que hace es seleccionar automáticamente el radio button correspondiente.
$(document).on("change", "#año", function (){
  $(this).parent().prev().prev().prev().prev().children().prop("checked", true);
});

///Disparar función al cambiar el mes elegido como parámetro para la búsqueda.
///Si se eligió alguna fecha de inicio quiere decir que la búsqueda es de movimientos y por rango (inicio/fin) 
///Lo que hace es seleccionar automáticamente el radio button correspondiente.
$(document).on("change", "#inicio", function (){
  $(this).parent().prev().prev().children().prop("checked", true);
});

///Disparar función al cambiar el mes elegido como parámetro para la búsqueda.
///Si se eligió alguna fecha de fin quiere decir que la búsqueda es de movimientos y por rango (inicio/fin) 
///Lo que hace es seleccionar automáticamente el radio button correspondiente.
$(document).on("change", "#fin", function (){
  $(this).parent().prev().prev().prev().prev().children().prop("checked", true);
});

///Disparar función al hacer click en el botón de CONSULTAR en la parte de búsquedas.
///Valida y arma la consulta, luego la ejecuta y muestra los resultados con un botón de EXPORTAR
///el cual permite hacer la exportación a PDF de la búsqueda realizada.
$(document).on("click", "#realizarBusqueda", function () {
  realizarBusqueda();
});

///Disparar función darle ENTER sobre los select ENTIDADSTOCK o ENTIDADMOVIMIENTO. 
///Básicamente, la idea es que al presionar ENTER se haga directamente el submit cosa de ahorrar tiempo.  
$(document).on("keypress", "#entidadStock, #entidadMovimiento", function (e){ 
  //close dropdown
  //$("#hint").attr('size',0);
  var temp = "#"+$(this).attr("id");
  var sel = $(temp).find('option:selected').val();
  if ((sel !== 'todos') && ((e.which === 1) || (e.which === 13))) {
    realizarBusqueda();
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
  var consultaCSV = $("#consultaCSV").val();
  var largos = $("#largos").val();
  var campos = $("#campos").val();
  var mostrar = $("#mostrar").val();
  var tipoConsulta = $("#tipoConsulta").val();
    
  var param = "id:"+id+"&x:"+x+"&largos:"+largos+"&campos:"+campos+"&query:"+query+"&consultaCSV:"+consultaCSV+"&mostrar:"+mostrar+"&tipoConsulta:"+tipoConsulta;
  
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
  var nombreProducto = $("#nombreProducto").val();
  
  
  //var enviarMail = confirm('¿Desea enviar por correo electrónico el pdf?');
  
  /// Se quita la pregunta del envío del mail pues, por ahora, NO hay acceso a internet desde donde está instalado y carece de sentido:
  var enviarMail = false;
  
  if (enviarMail === true) {
    var dir = prompt('Dirección/es: (SEPARADAS POR COMAS)');
    if (dir === '') {
      alert('Error, la dirección no puede quedar vacía. Por favor verifique.');
      continuar = false;
    }
    else {
      if (dir !== null) {
        //alert('Se enviará el reporte a: '+dir);
        param += "&mails:"+dir+"";
      }
      else {
        alert('Error, se debe ingresar la dirección a la cual enviar el reporte y dar aceptar.');
        continuar = false;
      }
    }
  }  
  else {
    //alert('Se optó por no enviar el mail. Se sigue con el guardado en disco y muestra en pantalla.');
  }
 
 
  ///En base al id, veo si es necesario o no enviar parámetros:
  switch (id) {
    case "1": param += '&entidad:'+entidad+'&nombreProducto:'+nombreProducto;
              break;
    case "2": param += '&idProd:'+idProd+'&nombreProducto:'+nombreProducto;
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
    case "5": param += '&idProd:'+idProd+'&tipo:'+tipo+'&usuario:'+usuario+'&nombreProducto:'+nombreProducto;
              if (criterioFecha === 'intervalo') {
                param += '&inicio:'+inicio+'&fin:'+fin;
              }
              else {
                param += '&mes:'+mes+'&año:'+año;
              }              
              break;
    default: break;
  }
  $("#param").val(param);
  
  //alert($("#param").val());
  
  $(".exportarForm").submit();
});//*** fin del click .exportar ***

/**************************************************************************************************************************
/// *************************************************** FIN BÚSQUEDAS *****************************************************
***************************************************************************************************************************
*/

/*****************************************************************************************************************************
/// Comienzan las funciones que manejan los eventos relacionados a las GRAFICAS
******************************************************************************************************************************
*/

///Disparar función al hacer click en el botón CONSULTAR del form graficar
///Arma la consulta acorde a los parámetros pasados, setea variables de sesión con la misma,  la fecha de inicio, y con el mensaje de la consulta
///También ambia el atributo ACTION del form (agrega un g=1) cosa de indicar a estadisticas.php que hay que mostrar la gráfica.
///Finalmente, hace el submit del form.
$(document).on("click", "#realizarGrafica", function (){
 var timestamp = Math.round(Date.now() / 1000);
      
  if(timestamp - $("#timestampSesion").val() > $("#duracionSesion").val()) {
    window.location.href = "../consultastock/index.php";
  }
  else {
    verificarSesion();
    var radio = $('input:radio[name=criterio]:checked').val();
    var entidadGrafica = document.getElementById("entidadGrafica").value;
    var idProd = $("#hint").val();
    var nombreProducto = $("#hint").find('option:selected').text( );
    
    if ((nombreProducto !== "undefined") && (nombreProducto !== '')) {
      ///Separo en partes el nombreProducto que contiene [entidad: codigo] --- nombreProducto
      var tempo = nombreProducto.split("- ");
      var nombreSolo = tempo[1].trim();
      //var tempo2 = tempo1.split("{");
      //var nombreSolo = tempo2[0].trim();
    }
    
    var tipo = $("#tipo").find('option:selected').val( );
    var mesInicio = $("#mesInicio").val();
    var añoInicio = $("#añoInicio").val();
    var mesFin = $("#mesFin").val();
    var añoFin = $("#añoFin").val();
    var rangoFecha = null;
    var tipoConsulta = '';
    var mensajeFecha = '';
    var validado = true;
    var inicio = '';
    var fin = '';
    var query = "select productos.nombre_plastico, movimientos.cantidad, movimientos.tipo, fecha from productos inner join movimientos on productos.idprod=movimientos.producto where productos.estado='activo' and ";
    
    switch (radio) {
      case 'entidadMovimiento': if (entidadGrafica !== 'todos') {
                                  query += "entidad='"+entidadGrafica+"' and ";
                                  tipoConsulta = 'de '+entidadGrafica;
                                } 
                                else {
                                  tipoConsulta = 'de todas las entidades';
                                }
                                break;                       
      case 'productoMovimiento':  if ((idProd === 'NADA') || (nombreProducto === '')){
                                    alert('Debe seleccionar un producto. Por favor verifique.');
                                    document.getElementById("productoMovimiento").focus();
                                    validado = false;
                                    return false;
                                  }
                                  else {
                                    query += "idProd="+idProd+' and ';
                                  }
                                  tipoConsulta = 'del producto '+nombreSolo;
                                  break;
    }
    
    ///Comienzo la validación de las fechas:  
    if (mesInicio === 'todos') {
      inicio = añoInicio+"-01-01";
      mesInicio = "01";
      mensajeFecha += "entre Enero de "+añoInicio;
      mesInicioMostrar = "Enero";
    }
    else {
      var mes = parseInt(mesInicio, 10);
      inicio = añoInicio+"-"+mesInicio+"-01";
      var mesInicioMostrar = '';
      switch (mesInicio) {
        case '01': mesInicioMostrar = "Enero";
                   break;
        case '02': mesInicioMostrar = "Febrero";
                   break;
        case '03': mesInicioMostrar = "Marzo";
                   break;
        case '04': mesInicioMostrar = "Abril";
                   break;
        case '05': mesInicioMostrar = "Mayo";
                   break;
        case '06': mesInicioMostrar = "Junio";
                   break;
        case '07': mesInicioMostrar = "Julio";
                   break;
        case '08': mesInicioMostrar = "Agosto";
                   break;
        case '09': mesInicioMostrar = "Setiembre";
                   break;
        case '10': mesInicioMostrar = "Octubre";
                   break;
        case '11': mesInicioMostrar = "Noviembre";
                   break;
        case '12': mesInicioMostrar = "Diciembre";
                   break;
        default: break;         
      }
      mensajeFecha += "entre "+mesInicioMostrar+" de "+añoInicio;
    }
    
    
    if (mesFin === 'todos') {
      var añoSiguiente = parseInt(añoFin, 10) + 1;
      mesFin = "12";
      fin = añoSiguiente+"-01-01";
      mensajeFecha += " y Diciembre de "+añoFin;
      mesFinMostrar = "Diciembre";
    }
    else { 
      var mes = parseInt(mesFin, 10) + 1;
      if (mes === 13) {
        mes = 1;
        añoSiguiente = parseInt(añoFin, 10) + 1;
        fin = añoSiguiente+"-01-01";
      }
      else {
        fin = añoFin+"-"+mes+"-01";
      }
      var mesFinMostrar = '';
      switch (mesFin) {
        case '01': mesFinMostrar = "Enero";
                   break;
        case '02': mesFinMostrar = "Febrero";
                   break;
        case '03': mesFinMostrar = "Marzo";
                   break;
        case '04': mesFinMostrar = "Abril";
                   break;
        case '05': mesFinMostrar = "Mayo";
                   break;
        case '06': mesFinMostrar = "Junio";
                   break;
        case '07': mesFinMostrar = "Julio";
                   break;
        case '08': mesFinMostrar = "Agosto";
                   break;
        case '09': mesFinMostrar = "Setiembre";
                   break;
        case '10': mesFinMostrar = "Octubre";
                   break;
        case '11': mesFinMostrar = "Noviembre";
                   break;
        case '12': mesFinMostrar = "Diciembre";
                   break;
        default: break;         
      }
      mensajeFecha += " y "+mesFinMostrar+" de "+añoFin;
    }
    
    validado = true;
    if (añoInicio > añoFin) {
      alert('El año inicial NO puede ser poseterior al año final.\nPor favor verifique.');
      validado = false;
      $("#inicio").focus();
    }
    else {
      if ((añoInicio === añoFin)&&(mesInicio > mesFin)&&((mesInicio !== 'todos')&&(mesFin !== 'todos'))) {
        validado = false;
        alert('El mes inicial NO puede ser posterior al mes final (del mismo año).\nPor favor verifique.');
        $("#inicio").focus();
      }
      else {
        if ((añoInicio===añoFin)&&(mesInicio===mesFin)&&(mesInicio!=='todos')){
          mensajeFecha = "del mes de "+mesInicioMostrar+" de "+añoInicio;
        }
        else {
          if ((añoInicio===añoFin)&&(mesInicio==='01')&&(mesFin==='12')){
            mensajeFecha = "del año "+añoInicio;
          }
          else {
            if (añoInicio===añoFin){
              mensajeFecha = "entre "+mesInicioMostrar+" y "+mesFinMostrar+" de "+añoInicio;
            }          
          }
        }
      }
      rangoFecha = "(fecha >='"+inicio+"') and (fecha<'"+fin+"')";
    }
    
    if (validado) {
      query += rangoFecha;
      var mensajeTipo = null;
      if (tipo !== 'Todos') 
        {
        query += " and tipo='"+tipo+"'";
        var tipo1 = '';
        switch (tipo) {
          case "Retiro": tipo1 = "Retiros";
                                  break;
          case "Ingreso": tipo1 = "Ingresos";
                                  break;
          case "Renovación": tipo1 = "Renovaciones";
                                  break;
          case "Destrucción": tipo1 = "Destrucciones";
                                  break;
          default: break;
        }
        mensajeTipo = tipo1+" ";
      }
      else {
        mensajeTipo = "Movimientos ";
      };
      
      query += " order by fecha asc, hora desc, entidad asc, nombre_plastico asc,  idprod";
      var mensajeConsulta = "";
      if (mensajeTipo !== null) {
        mensajeConsulta += mensajeTipo;
      }
      mensajeConsulta += tipoConsulta+" "+mensajeFecha;
      
      var url = "data/selectQuery.php";

      $.getJSON(url, {query: ""+query+""}).done(function(request){
        var totalDatos = request.rows;     
        if (totalDatos >= 1) {
          $("#consulta").val(query);
          $("#mensaje").val(mensajeConsulta);
          $("#fechaInicio").val(añoInicio+mesInicio);
          $("#fechaFin").val(fin);
          $("#hacerGrafica").val("yes");
          $('#graficar').attr('action', 'estadisticas.php?g=1');
          $("#graficar").submit();
        }
        else {
          alert("No existen registros que coincidan con esos parámetros.");
        }
      });
      
      
    }  
  }
});


/**************************************************************************************************************************
/// *************************************************** FIN GRAFICAS *****************************************************
***************************************************************************************************************************
*/

}

/**
 * \brief Función que envuelve todos los eventos JQUERY con sus respectivos handlers.
 */
$(document).on("ready", todo());//*** fin del ready ***
