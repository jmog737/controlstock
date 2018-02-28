var limiteSeleccion = 8;
var tamPagina = 15;

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
        var user_id = myObj.user_id;
        var timestamp = myObj.timestamp;
        $("#usuarioSesion").val(user);
        $("#userID").val(user_id);
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
    $("#ultimoMov").remove();
    $("#historial").remove();
    document.getElementById("producto").innerHTML = "";
    return;
  } 
  else {
    var url = "data/selectQuery.php";
    var query = "select idprod, entidad, nombre_plastico, codigo_emsa, codigo_origen, bin, snapshot, stock, alarma1, alarma2, comentarios, ultimoMovimiento from productos where (productos.nombre_plastico like '%"+str+"%' or productos.codigo_emsa like '%"+str+"%' or productos.codigo_origen like '%"+str+"%' or productos.bin like '%"+str+"%' or productos.entidad like '%"+str+"%') and estado='activo' order by productos.entidad asc, productos.nombre_plastico asc";
    //alert(query);
    $.getJSON(url, {query: ""+query+""}).done(function(request) {
      var sugerencias = request.resultado;
      var totalSugerencias = request.rows;
      $("[name='hint']").remove();
      $("#ultimoMov").remove();
      $("#historial").remove();
      
      var mostrar = '';
      var unico = '';
      if (totalSugerencias >= 1) {
        if (($("#productoGrafica").length > 0)||($("#producto").length > 0)){
          mostrar = '<select name="hint" id="hint" class="hint" size="15">';
        }
        else {
          mostrar = '<select name="hint" id="hint" class="hint" multiple size="15">';
        }
        if (totalSugerencias > 1) {
          mostrar += '<option value="NADA" name="NADA" selected>--Seleccionar--</option>';
        }
        for (var i in sugerencias) {
          if (totalSugerencias === 1){
            unico = parseInt(sugerencias[i]["idprod"], 10);
          }
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
          mostrar += '<option data-toggle="popover" title="Popover Header" data-content="Some content inside the popover" value="'+sugerencias[i]["idprod"]+'" name="'+snapshot+'" stock='+sugerencias[i]["stock"]+' alarma1='+sugerencias[i]["alarma1"]+' alarma2='+sugerencias[i]["alarma2"]+' comentarios="'+sugerencias[i]["comentarios"]+'" ultimoMov="'+sugerencias[i]["ultimoMovimiento"]+'" '+sel+ '>[' + sugerencias[i]["entidad"]+': '+codigo_emsa+'] --- '+sugerencias[i]["nombre_plastico"] + '</option>';
        }
        mostrar += '</select>';
      }
      else {
        mostrar = '<p name="hint" value="">No se encontraron sugerencias!</p>';
      }
      $(id).after(mostrar);
      
      /// Agregado a pedido de Diego para que se abra el select automáticamente:
      var length = $('#hint> option').length;
      if (length > 11) {
        length = 11;
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
        ///Comentado por ahora pues Diego prefiere que NO salte de forma automática:
        //$("#comentarios").focus();
        $("#hint option[value='"+unico+"'] ").attr("selected", true);
        //$("#cantidad").focus();
      }      
    });
  }
}

/**
 * \brief Función que muestra en pantalla el botón para disparar el popover con el historial
 *        Básicamente, hace la consulta del historial para el producto y arma el botón con el popover.
 * @param {String} prod String con el id del producto a consultar.
*/
function mostrarHistorial(prod){
  if ($("#historial").length > 0){
    $("#historial").popover('hide');
    $("#historial").remove();
  }
  $("#historial").popover('hide');
    $("#historial").remove();
  var limite = 3;
  var url = "data/selectQuery.php";
  var query = "select productos.nombre_plastico as nombre, DATE_FORMAT(movimientos.fecha, '%d/%m/%Y') as fecha, DATE_FORMAT(movimientos.hora, '%H:%i') as hora, movimientos.cantidad, movimientos.tipo from movimientos inner join productos on productos.idprod=movimientos.producto where productos.idprod="+prod+" order by movimientos.fecha desc, movimientos.hora desc limit "+limite+"";
  //alert(query);
  $.getJSON(url, {query: ""+query+""}).done(function(request){
    var datos = request.resultado;
    var totalDatos = request.rows;
    if (totalDatos > 0){
      var mostrar = '';
      var j = 0;
      for (var i in datos){
        j = parseInt(i, 10)+1;
        mostrar += j+": "+datos[i]["fecha"]+" "+datos[i]["hora"]+" - "+datos[i]["tipo"]+": <b>"+datos[i]["cantidad"]+"</b><br>";
      }
      var popover = '<a role="button" tabindex="0" id="historial" class="btn btn-danger historial" title="Historial de '+datos[i]["nombre"]+'" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="right" data-content="'+mostrar+'">Historial</a>';
      
      if ($("#comentHint").length > 0) {
        $("#comentHint").after(popover);
      }
      else {
        $("#ultimoMov").after(popover);
      } 
      $("#historial").popover({html:true});
    }
    else {
      ///ver si avisar que no hay movimientos.
    }
  });
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
    $("#ultimoMov").remove();
    $("#historial").remove();
    document.getElementById("producto").innerHTML = "";
    return;
  } else {
    var url = "data/selectQuery.php";
    var query = "select idprod, entidad, nombre_plastico, codigo_emsa, codigo_origen, bin, snapshot, stock, alarma1, alarma2, ultimoMovimiento from productos where (productos.nombre_plastico like '%"+str+"%' or productos.codigo_emsa like '%"+str+"%' or productos.codigo_origen like '%"+str+"%' or productos.bin like '%"+str+"%' or productos.entidad like '%"+str+"%') and estado='activo' order by productos.nombre_plastico asc";
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
          mostrar += '<option value="'+sugerencias[i]["idprod"]+'" name="'+snapshot+'" stock='+sugerencias[i]["stock"]+' alarma1='+sugerencias[i]["alarma1"]+' alarma2='+sugerencias[i]["alarma2"]+' ultimoMov="'+sugerencias[i]["ultimoMovimiento"]+'" '+sel+ '>[' + sugerencias[i]["entidad"]+': '+codigo_emsa+'] --- '+sugerencias[i]["nombre_plastico"] + '</option>';
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
  @param {String} fecha String con la fecha del movimiento ingresado anteriormente.
*/
function cargarMovimiento(selector, hint, prod, tipo, fecha){
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
      var tabla = '<table class="tabla2" id="movimiento" name="movimiento">\n\
                    <caption>Formulario para agregar movimientos</caption>';
      var tr = '<th colspan="2" class="tituloTabla">MOVIMIENTO</th>';
      tr += '<tr>\n\
              <th><font class="negra">Fecha:</font></th>\n\
              <td align="center"><input type="date" name="fecha" id="fecha" title="Ingresar la fecha del movimiento" style="width:100%; text-align: center" min="2017-09-01" value="'+hoy+'"></td>\n\
            </tr>';
      tr += '<tr>\n\
              <th align="left"><font class="negra">Producto:</font></th>\n\
              <td align="center">\n\
                <input type="text" id="producto" name="producto" placeholder="Producto" title="Ingresar el producto" size="9" class="agrandar" tabindex="1" onkeyup=\'showHint(this.value, "#producto", "")\' value="'+hint+'">\n\
              </td>\n\
            </tr>';
      tr += '<tr>\n\
              <th align="left"><font class="negra">Tipo:</font></th>\n\
              <td align="center">\n\
                <select id="tipo" name="tipo" tabindex="4" style="width:100%" title="Seleccionar el tipo de movimiento" >\n\
                  <option value="Retiro" selected="yes">Retiro</option>\n\
                  <option value="Ingreso">Ingreso</option>\n\
                  <option value="Renovaci&oacute;n">Renovaci&oacute;n</option>\n\
                  <option value="Destrucci&oacute;n">Destrucci&oacute;n</option>\n\
                </select>\n\
              </td>\n\
            </tr>';
      tr += '<tr>\n\
              <th align="left"><font class="negra">Cantidad:</font></th>\n\
              <td align="center"><input type="text" id="cantidad" name="cantidad" placeholder="Cantidad" title="Ingresar la cantidad" class="agrandar" tabindex="3" maxlength="35" size="9"></td>\n\
            </tr>';
//      tr += '<tr>\n\
//              <th align="left"><font class="negra">Repetir Cantidad:</font></th>\n\
//              <td align="center"><input type="text" id="cantidad2" name="cantidad2" class="agrandar" maxlength="35" size="9"></td>\n\
//            </tr>';
      tr += '<tr>\n\
              <th align="left"><font class="negra">Comentarios:</font></th>\n\
              <td align="center"><input type="textarea" id="comentarios" name="comMov" placeholder="Comentarios" title="Ingresar un comentario" tabindex="2" class="agrandar" maxlength="150" size="9"></td>\n\
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
                <input type="button" value="ACEPTAR" id="agregarMovimiento" name="agregarMovimiento" title="Ejecutar la consulta" tabindex="5" class="btn btn-success" align="center"/>\n\
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
      var mostrar = '';
      mostrar += formu;
      $(selector).html(mostrar);
      
      if ((tipo !== '') && (tipo !== undefined)){
        $("#tipo option[value="+ tipo +"]").attr("selected",true);
      }
      
      if ((fecha !== '') && (fecha !== undefined)){
        $("#fecha").val(fecha);
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
  var timestamp = Math.round(Date.now() / 1000);
      
  if(timestamp - $("#timestampSesion").val() > $("#duracionSesion").val()) {
    alert('Su sesión expiró. Por favor vuelva loguearse.'); 
    window.location.href = "../controlstock/index.php";
  }
  else {
    verificarSesion();
  
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
      var userSesion = $("#userID").val();
      var userControl;
      if (userSesion === 2){
        userControl = 3;
      }
      else {
        userControl = 2;
      }
      /// Agrego el movimiento según los datos pasados:
      var url = "data/updateQuery.php";
      var query = "insert into movimientos (producto, fecha, hora, tipo, cantidad, control1, control2, comentarios) values ("+idProd+", '"+fecha+"', '"+hora+"', '"+tipo+"', "+cantidad+", "+userSesion+", "+userControl+", '"+comentarios+"');";
      //alert(document.getElementById("usuarioSesion").value); --- USUARIO QUE REGISTRA!!!

      var log = "SI";
      $.getJSON(url, {query: ""+query+"", log: log}).done(function(request) {
        var resultado = request["resultado"];
        /// Si el agregado es exitoso, actualizo el stock y la fecha de la última modificación en la tabla Productos:
        if (resultado === "OK") {
          /***** AGREGADO PARA CONOCER ULTIMO ID INGRESADO: ********************************/
          var query = "SELECT MAX(idmov) AS id FROM movimientos where idprod="+idProd+' limit 1';
          /* FIN AGREGADO */
          var url = "data/updateQuery.php";
          var fechaTemp = fecha.split("-");
          var fechaMostrar = fechaTemp[2]+"/"+fechaTemp[1]+"/"+fechaTemp[0];
          var ultimoMovimiento = fechaMostrar+" "+hora+" - "+tipo+": "+cantidad;
          var query = "update productos set stock="+nuevoStock+", ultimoMovimiento='"+ultimoMovimiento+"' where idprod="+idProd;
          log = "SI";
          $.getJSON(url, {query: ""+query+"", log: log}).done(function(request) {
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
              window.location.href = "../controlstock/movimiento.php?h="+busqueda+"&id="+idProd+"&t="+tipo1+"&f="+fecha;
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
}

/**
 * \brief Función que hace la actualización del movimiento en la base de datos. 
 *        Se separó del evento actualizarMoviemiento para poder hacer el agregado al detectar el ENTER en el elemento comentarios.
 */
function actualizarMovimiento(){
  var timestamp = Math.round(Date.now() / 1000);
      
  if(timestamp - $("#timestampSesion").val() > $("#duracionSesion").val()) {
    alert('Su sesión expiró. Por favor vuelva loguearse.'); 
    window.location.href = "../controlstock/index.php";
  }
  else {
    verificarSesion();
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
        var log = "SI";
        //alert(query);
        $.getJSON(url, {query: ""+query+"", log: log}).done(function(request) {
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
  var query = 'select movimientos.fecha, movimientos.hora, movimientos.cantidad, movimientos.comentarios, movimientos.tipo, productos.entidad, productos.codigo_emsa, productos.nombre_plastico from movimientos inner join productos on movimientos.producto=productos.idprod where movimientos.idmov='+idMov;
  
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
      var codigo = resultado[0]['codigo_emsa'];
      var entidad = resultado[0]['entidad'];
      var comentarios = resultado[0]['comentarios'];
      var producto = resultado[0]['nombre_plastico'];
    }
    var mostrar = "";
    var titulo = '<h2 id="titulo" class="encabezado">EDICIÓN DE MOVIMIENTOS</h2>';
    var formu = '<form method="post" action="editarMovimiento.php">';
    var tabla = '<table class="tabla2" name="editarMovimiento">\n\
                  <caption>Formulario para editar el movimiento</caption>';
    var tr = '<th colspan="3" class="centrado tituloTabla">DATOS DEL MOVIMIENTO</th>';

    tr += '<tr>\n\
            <th align="left" width="15"><font class="negra">Fecha:</font></th>\n\
            <td align="center" colspan="2"><input type="text" name="fecha" id="fecha" title="Elegir fecha del movimiento\n(NO editable)" placeholder="Fecha" class="agrandar" style="width:100%; text-align: center" disabled></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Hora:</font></th>\n\
              <td align="center" colspan="2"><input type="text" name="hora" id="hora" title="Elegir la hora del movimiento\n(NO editable)" placeholder="Hora" class="agrandar" maxlength="35" style="width:100%; text-align: center" disabled></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Entidad:</font></th>\n\
              <td align="center" colspan="2"><input type="text" name="entidad" id="entidad" title="Ingresar la entidad del producto\n(NO editable)" placeholder="Entidad" class="agrandar" maxlength="75" style="width:100%; text-align: center" disabled></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Nombre:</font></th>\n\
              <td align="center" colspan="2"><input type="text" name="nombre" id="nombre" title="Ingresar el nombre del producto\n(NO editable)" placeholder="Producto" class="agrandar" maxlength="75" style="width:100%; text-align: center" disabled></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">C&oacute;digo:</font></th>\n\
              <td align="center" colspan="2"><input type="text" name="codigo" id="codigo" title="Ingresar el código del producto\n(NO editable)" placeholder="Código" class="agrandar" maxlength="75" style="width:100%; text-align: center" disabled></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Tipo:</font></th>\n\
              <td align="center" colspan="2"><input type="text" name="tipo" id="tipo" title="Ingresar el tipo de movimiento realizado\n(NO editable)" placeholder="Tipo de movimiento" class="agrandar" maxlength="35" style="width:100%; text-align: center" disabled></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Cantidad:</font></th>\n\
              <td align="center" colspan="2"><input type="text" id="cantidad" name="cantidad" title="Ingresar la cantidad" placeholder="Cantidad" class="agrandar" maxlength="35" size="9" disabled></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Comentarios:</font></th>\n\
              <td align="center" colspan="2"><input type="textarea" id="comentarios" name="comEditMov" title="Ingresar los comentarios del movimiento" placeholder="Comentarios" tabindex="1" class="agrandar" maxlength="35" size="9"></td>\n\
          </tr>';
    tr += '<tr>\n\
              <td class="pieTablaIzquierdo" style="width: 50%;border-right: 0px;"><input type="button" value="BLOQUEAR" id="editarMovimiento" name="editarMovimiento" title="Habilitar/Deshabilitar la edición del movimiento" class="btn btn-primary" align="center"/></td>\n\
              <td class="pieTablaDerecho" style="width: 50%;border-left: 0px;"><input type="button" value="ACTUALIZAR" id="actualizarMovimiento" name="actualizarMovimiento" title="Realizar la edición del movimiento" tabindex="2" class="btn btn-warning" align="center"/></td>\n\
              <td style="display:none"><input type="text" name="idMov" value="'+idMov+'"></td>\n\
          </tr>';
    tabla += tr;
    tabla += '</table>';
    
    formu += tabla;
    formu += '</form>';
    mostrar += titulo;
    mostrar += formu;
    var volver = '<br><a href="../controlstock/busquedas.php" name="volver" id="volverEdicionMovimiento" title="Volver a BÚSQUEDAS">Volver</a><br><br>';
    mostrar += volver;
    $(selector).html(mostrar);
  
    if (total >=1) {
      $("#fecha").val(fecha);
      $("#hora").val(hora);
      $("#entidad").val(entidad);
      $("#tipo").val(tipo);
      $("#nombre").val(producto);
      $("#codigo").val(codigo);
      $("#cantidad").val(cantidad.toLocaleString());
      $("#comentarios").val(comentarios);
    }
    
    $("#comentarios").attr("autofocus", true);
    
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
  var query = "select nombre, apellido, empresa, mail, telefono, observaciones from usuarios where idusuarios='"+user+"' limit 1";
  
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

/**
 * \brief Función que primero valida la info ingresada, y de ser válida, hace la actualización del pwd del usuario del sistema.
 */
function actualizarUser ()
  {
  var timestamp = Math.round(Date.now() / 1000);
      
  if(timestamp - $("#timestampSesion").val() > $("#duracionSesion").val()) {
    window.location.href = "../consultastock/index.php";
  }
  else {
    verificarSesion();
    
    var pw1 = $("#pw1").val();
    var pw2 = $("#pw2").val();

    if (pw1 === ''){
      alert('La contraseña 1 NO puede estar vacía.\nPor favor verifique.');
      $("#pw1").focus();
    }
    else {
      if (pw2 === ''){
        alert('La contraseña 2 NO puede estar vacía.\nPor favor verifique.');
        $("#pw2").focus();
      }
      else {
        if (pw1 !== pw2) {
          alert('Las contraseñas ingresadas NO son iguales.\nPor favor verifique.');
          $("#pw1").val('');
          $("#pw2").val('');
          $("#pw1").focus();
        }
        else {
          //alert('hay que actualizar a: '+$("#usuarioSesion").val()+'\nID: '+$("#userID").val());
          /******** COMENTO PARTE DEL USUARIO POR AHORA **********************/
          ///var user = $("#nombreUser").val();
          var iduser = $("#userID").val();
          var url = "data/updateQuery.php";
          var query = 'update appusers set password=sha1("'+pw1+'") ';
          /*
          if (user !== ''){
            query += ', user="'+user+'" ';
          }
          */
          query += 'where id_usuario='+iduser;
          var log = "NO";
          //alert(query);
          $.getJSON(url, {query: ""+query+"", log: log}).done(function(request) {
            var resultado = request["resultado"];
            if (resultado === "OK") {
              alert('Los datos se modificaron correctamente!.');
              $("#modalPwd").modal("hide");
              //cargarEditarMovimiento(idmov, "main-content");
              //inhabilitarMovimiento();
            }
            else {
              alert('Hubo un problema en la actualización. Por favor verifique.');
            }
            
          });
        }
      }
    }
  }
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
 * \brief Función que muestra el resultado de la consulta en pantalla. Arma la tabla con los datos pasados y luego la muestra en pantalla.
 * @param {String} radio String con el tipo de consulta realizada para saber que tipo de tabla hay que armar. 
 * @param {Array} datos Array de Strings con los datos a mostrar.
 * @param {String} j String con el número de pestaña donde se tiene que mostrar la tabla. 
 * @param {Boolean} todos Booleano que indica si la consutla realizada fue para todos los productos o entidades, o si fue sólo para algunos. Es para mostrar bien el caption de la tabla.
 * @param {String} offset String con el número de registro donde comienza la tabla de entre todos los registros del resultado.
 * @param {Boolean} parcial Booleano que indica si se deben mostrar subtotales o totales.
 * @param {Object} subtotales Objeto con los subtotales, a saber, subtotales{retiros: XX, renovaciones: xx, destrucciones: XX, ingresos: XX, consumos: XX, stock: XX}.
 * @param {Integer} max Entero con el total de datos a mostrar en la tabla.
 * @param {Integer} totalPlasticos Entero con el total acumulado del stock. SÓLO se usa en consultas de stock de entidades.
 * @returns {String} String con el HTML para mostrar la tabla.
 */
function mostrarTabla(radio, datos, j, todos, offset, parcial, subtotales, max, totalPlasticos){
  var tabla = '<table name="resultados" id="resultados_'+j+'" class="tabla2">';
  var rutaFoto = 'images/snapshots/';
  
  switch(radio) {
    case 'entidadStock':  tabla += '<tr><th class="tituloTabla" colspan="9">CONSULTA DE STOCK</th></tr>';
                          tabla += '<tr>\n\
                                      <th>Item</th>\n\
                                      <th>Entidad</th>\n\
                                      <th>Nombre</th>\n\
                                      <th>BIN</th>\n\
                                      <th>Código</th>\n\
                                      <th>Snapshot</th>\n\
                                      <th>&Uacute;ltimo Movimiento</th>\n\
                                      <th>Stock</th>\n\
                                      <th>Mensaje</th>\n\
                                   </tr>';
                          //var total = parseInt(subtotales["stock"], 10);
                          var total = 0;
                          for (var i=0; i<max; i++) { 
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
                            var mensaje = datos[i]['prodcom'];
                            var claseComentario = "";
                            if ((mensaje === "undefined")||(mensaje === null)||(mensaje === "")) 
                              {
                              mensaje = "";
                              claseComentario = "";
                            }
                            else {
                              var patron = "dif";
                              var buscar = mensaje.search(new RegExp(patron, "i"));
                              if (buscar !== -1){
                                claseComentario = "alarma1";
                              }
                            }
                            var claseResaltado = "alarma1";
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
                                        <td>'+offset+'</td>\n\
                                        <td style="text-align: left">'+entidad+'</td>\n\
                                        <td>'+nombre+'</td>\n\
                                        <td nowrap>'+bin+'</td>\n\
                                        <td>'+codigo_emsa+'</td>\n\
                                        <td><img id="snapshot" name="hint" src="'+rutaFoto+snapshot+'" alt="No se cargó aún." height="76" width="120"></img></td>\n\
                                        <td>'+ultimoMovimiento+'</td>\n\
                                        <td class="'+claseResaltado+'" style="text-align: right">'+stock.toLocaleString()+'</td>\n\
                                        <td class="'+claseComentario+'" >'+mensaje+'</td>\n\
                                      </tr>';
                            offset++;
                            total += stock;
                          }
                          if (!todos){
                            tabla += '<caption>Stock de la entidad: '+entidad+'</caption>';
                          }
                          else {
                            tabla += '<caption>Stock de todas las entidades</caption>';
                          }
                          
                          var subtitulo = '';
                          if (parcial){
                            subtitulo = 'SUB-TOTAL';
                          } 
                          else {
                            subtitulo = 'TOTAL';
                            tabla += '<tr><th colspan="7" class="centrado">'+subtitulo+':</th><td class="resaltado1 italica" style="text-align: right">'+parseInt(totalPlasticos, 10).toLocaleString()+'</td><th></th></tr>';
                            //totalMostrar = totalStock.toLocaleString();
                          }
                          var subtotalesJson = JSON.stringify(subtotales);
                          tabla += '<tr>\n\
                                      <td class="pieTabla" colspan="9">\n\
                                        <input type="button" id="1" indice="'+j+'" name="exportarBusqueda" value="EXPORTAR" class="btn btn-primary exportar">\n\
                                      </td>\n\
                                    </tr>';
                          tabla += "<tr>\n\
                                      <td style='display:none'><input type='text' id='subtotales_"+j+"' value="+subtotalesJson+"></td>\n\
                                    </tr>\n\
                                  </table>";
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
                          tabla += '<caption>Stock del producto: '+datos[0]['nombre_plastico']+'</caption>';
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
                          tabla += '<tr>\n\
                                      <td class="pieTabla" colspan="2">\n\
                                        <input type="button" id="2" indice="'+j+'" name="exportarBusqueda" value="EXPORTAR" class="btn btn-primary exportar">\n\
                                      </td>\n\
                                    </tr>\n\
                                  </table>';
                          break;
      case 'totalStock':  tabla += '<caption>Stock total en b&oacute;veda.</caption>';
                          tabla += '<tr>\n\
                                      <th colspan="3" class="tituloTabla">DETALLES</th>\n\
                                    </tr>';
                          tabla += '<tr>\n\
                                        <th>Item</th>\n\
                                        <th>Entidad</th>\n\
                                        <th>Stock</th>\n\
                                     </tr>';          
                          var total = 0;
                          for (var k in datos) { 
                            //var produ = datos[i]["idProd"];
                            var entidad = datos[k]["entidad"];
                            //var nombre = datos[i]['nombre_plastico'];
                            var bin = datos[k]['bin'];
                            var codigo_emsa = datos[k]['codigo_emsa'];
                            var stock = datos[k]['stock'];
                            var subtotal = parseInt(datos[k]['subtotal'], 10);
                            if ((bin === 'SIN BIN')||(bin === null)) 
                              {
                              bin = 'N/D o N/C';
                            }
                            tabla += '<tr>\n\
                                        <td>'+offset+'</td>\n\
                                        <td style="text-align: left">'+entidad+'</td>\n\
                                        <td class="resaltado italica" style="text-align: right">'+subtotal.toLocaleString()+'</td>\n\
                                      </tr>';
                            offset++;  
                            total += subtotal;
                          }
                          if (!parcial){
                            subtitulo = 'TOTAL';
                            tabla += '<tr><th colspan="2" class="centrado">TOTAL:</th><td class="resaltado1 italica" style="text-align: right">'+parseInt(totalPlasticos, 10).toLocaleString()+'</td></tr>';
                          }
                          var subtotalesJson = JSON.stringify(subtotales);
                          tabla += "<tr>\n\
                                      <td style='display:none'><input type='text' id='subtotales_"+j+"' value="+subtotalesJson+"></td>\n\
                                    </tr>";
                          tabla += '<tr>\n\
                                      <td class="pieTabla" colspan="3">\n\
                                        <input type="button" id="3" indice="'+j+'" name="exportarBusqueda" value="EXPORTAR" class="btn btn-primary exportar">\n\
                                      </td>\n\
                                    </tr>\n\
                                  </table>';              
                          break;
      case 'entidadMovimiento': tabla += '<tr><th class="tituloTabla" colspan="11">MOVIMIENTOS</th></tr>';
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

                                var productoViejo = parseInt(datos[0]['idprod'], 10);

                                for (var i=0; i<max; i++) { 
                                  var produ = parseInt(datos[i]["idprod"], 10);
                                  var idmov = datos[i]["idmov"];
                                  var entidad = datos[i]["entidad"];
                                  var nombre = datos[i]['nombre_plastico'].trim();
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
                                  
                                  if (productoViejo !== produ) {
                                    var totalConsumos = 0;
                                    if (subtotales["retiros"][productoViejo] !== undefined) {//alert('hay retiros\n');
                                      tabla += '<tr>\n\
                                                  <td colspan="9" class="negrita">Total Retiros:</td>\n\
                                                  <td class="subtotal" colspan="2">'+subtotales["retiros"][productoViejo].toLocaleString()+'</td>\n\
                                                </tr>';
                                      totalConsumos += parseInt(subtotales["retiros"][productoViejo], 10);
                                    }
                                    
                                    if (subtotales["renovaciones"][productoViejo] !== undefined) {//alert('hay renos\n');
                                      tabla += '<tr>\n\
                                                  <td colspan="9" class="negrita">Total Renovaciones:</td>\n\
                                                  <td class="subtotal" colspan="2">'+subtotales["renovaciones"][productoViejo].toLocaleString()+'</td>\n\
                                                </tr>';
                                      totalConsumos += parseInt(subtotales["renovaciones"][productoViejo], 10);
                                    }     alert('antes de destru');                              
//                                    if (subtotales["destrucciones"][productoViejo] !== undefined) {alert('hay renos\n');
//                                      tabla += '<tr>\n\
//                                                  <td colspan="9" class="negrita">Total Destrucciones:</td>\n\
//                                                  <td class="subtotal" colspan="2">'+subtotales["destrucciones"][productoViejo].toLocaleString()+'</td>\n\
//                                                </tr>';
//                                    }
                                    if (totalConsumos > 0) {
                                      tabla += '<tr>\n\
                                                  <td colspan="9" class="negrita">Total de Consumos:</td>\n\
                                                  <td class="totalConsumos" colspan="2">'+totalConsumos.toLocaleString()+'</td>\n\
                                                </tr>';
                                    }

                                    if (subtotales["ingresos"][productoViejo] !== undefined) {//alert('hay ingresos: '+subtotales["ingresos"][productoViejo]);
                                      tabla += '<tr>\n\
                                                  <td colspan="9" class="negrita">Total de Ingresos:</td>\n\
                                                  <td class="totalIngresos" colspan="2">'+subtotales["ingresos"][productoViejo].toLocaleString()+'</td>\n\
                                                </tr>';
                                    }
                                    productoViejo = produ;
                                    tabla += '<th colspan="11">&nbsp;\n\
                                              </th>';
                                  }
                                  
                                  tabla += '<tr>\n\
                                              <td>'+offset+'</td>\n\
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
              
                                  if ((!parcial) && (parseInt(i, 10) === parseInt(tamPagina-1, 10))){
                                    var totalConsumos = 0;alert('resumen con parcial en FALSE y ultimo registro');
                                    if (subtotales["retiros"][productoViejo] !== undefined) {//alert('hay retiros\n');
                                      tabla += '<tr>\n\
                                                  <td colspan="9" class="negrita">Total Retiros:</td>\n\
                                                  <td class="subtotal" colspan="2">'+subtotales["retiros"][productoViejo].toLocaleString()+'</td>\n\
                                                </tr>';
                                      totalConsumos += parseInt(subtotales["retiros"][productoViejo], 10);
                                    }
                                    
                                    if (subtotales["renovaciones"][productoViejo] !== undefined) {//alert('hay renos\n');
                                      tabla += '<tr>\n\
                                                  <td colspan="9" class="negrita">Total Renovaciones:</td>\n\
                                                  <td class="subtotal" colspan="2">'+subtotales["renovaciones"][productoViejo].toLocaleString()+'</td>\n\
                                                </tr>';
                                      totalConsumos += parseInt(subtotales["renovaciones"][productoViejo], 10);
                                    }
                                    
//                                    if (subtotales["destrucciones"][productoViejo] !== undefined) {//alert('hay renos\n');
//                                      tabla += '<tr>\n\
//                                                  <td colspan="9" class="negrita">Total Destrucciones:</td>\n\
//                                                  <td class="subtotal" colspan="2">'+subtotales["destrucciones"][productoViejo].toLocaleString()+'</td>\n\
//                                                </tr>';
//                                    }
//                                    alert('despues de destru');
                                    if (totalConsumos > 0) {
                                      tabla += '<tr>\n\
                                                  <td colspan="9" class="negrita">Total de Consumos:</td>\n\
                                                  <td class="totalConsumos" colspan="2">'+totalConsumos.toLocaleString()+'</td>\n\
                                                </tr>';
                                    }

                                    if (subtotales["ingresos"][productoViejo] !== undefined) {//alert('hay ingresos: '+subtotales["ingresos"][productoViejo]);
                                      tabla += '<tr>\n\
                                                  <td colspan="9" class="negrita">Total de Ingresos:</td>\n\
                                                  <td class="totalIngresos" colspan="2">'+subtotales["ingresos"][productoViejo].toLocaleString()+'</td>\n\
                                                </tr>';
                                    }
                                    //productoViejo = produ;
                                    tabla += '<th colspan="11">&nbsp;\n\
                                              </th>';
                                  }
                                  
                                  offset++;  
                                }/// FIN DEL FOR ********************************************************
                                
                                ///****************** RESUMEN último producto ********************************************************************
                                ///Detecto si es o no la primer página.
                                ///En caso de serlo seteo el total de Páginas a 0 para que NO muestre el resumen para el último producto
                                var pagActual = parseInt($(".nav-link.active").attr("activepage"), 10);
                                var totalPaginas = parseInt($("#totalPaginas_"+j).val(), 10);
                                if (offset === tamPagina+1){
                                  totalPaginas = 0;
                                }
                                
                                if (pagActual === totalPaginas){
                                  ///Resumen para el último producto: 
                                  var totalConsumos = 0;
                                  if (subtotales["retiros"][produ] !== undefined) {//alert('hay retiros\n');
                                    tabla += '<tr>\n\
                                                <td colspan="9" class="negrita">Total Retiros:</td>\n\
                                                <td class="subtotal" colspan="2">'+subtotales["retiros"][produ].toLocaleString()+'</td>\n\
                                              </tr>';
                                    totalConsumos += parseInt(subtotales["retiros"][produ], 10);
                                  }

                                  if (subtotales["renovaciones"][produ] !== undefined) {//alert('hay renos\n');
                                    tabla += '<tr>\n\
                                                <td colspan="9" class="negrita">Total Renovaciones:</td>\n\
                                                <td class="subtotal" colspan="2">'+subtotales["renovaciones"][produ].toLocaleString()+'</td>\n\
                                              </tr>';
                                    totalConsumos += parseInt(subtotales["renovaciones"][produ], 10);
                                  }

  //                                    if (subtotales["destrucciones"][productoViejo] !== undefined) {//alert('hay renos\n');
  //                                      tabla += '<tr>\n\
  //                                                  <td colspan="9" class="negrita">Total Destrucciones:</td>\n\
  //                                                  <td class="subtotal" colspan="2">'+subtotales["destrucciones"][productoViejo].toLocaleString()+'</td>\n\
  //                                                </tr>';
  //                                    }
                                  if (totalConsumos > 0) {
                                    tabla += '<tr>\n\
                                                <td colspan="9" class="negrita">Total de Consumos:</td>\n\
                                                <td class="totalConsumos" colspan="2">'+totalConsumos.toLocaleString()+'</td>\n\
                                              </tr>';
                                  }

                                  if (subtotales["ingresos"][produ] !== undefined) {//alert('hay ingresos: '+subtotales["ingresos"][productoViejo]);
                                    tabla += '<tr>\n\
                                                <td colspan="9" class="negrita">Total de Ingresos:</td>\n\
                                                <td class="totalIngresos" colspan="2">'+subtotales["ingresos"][produ].toLocaleString()+'</td>\n\
                                              </tr>';
                                  }
                                }
                                ///****************** RESUMEN último producto ********************************************************************
                                if (!todos){
                                  tabla += '<caption>Movimientos de la entidad: '+entidad+'</caption>';
                                }
                                else {
                                  tabla += '<caption>Movimientos de todas las entidades</caption>';
                                }

                                tabla += '<th colspan="11">&nbsp;\n\
                                          </th>';
                                var subtotalesJson = JSON.stringify(subtotales);
                                tabla += "<tr>\n\
                                            <td style='display:none'><input type='text' id='subtotales_"+j+"' value='"+subtotalesJson+"'></td>\n\
                                          </tr>";
                                tabla += '<tr>\n\
                                            <td class="pieTabla" colspan="11">\n\
                                              <input type="button" id="4" indice="'+j+'" name="exportarBusqueda" value="EXPORTAR" class="btn btn-primary exportar">\n\
                                            </td>\n\
                                          </tr>\n\
                                        </table>';  
                                break;
      case 'productoMovimiento':  var bin = datos[0]['bin'];
                                  //var produ = datos[0]['produ'];
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
                                  var tabla = '<table name="detallesProducto" id="detallesProducto_'+j+'" class="tabla2">';
                                  tabla += '<caption>Detalles del producto: '+datos[0]['nombre_plastico']+'</caption>';
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
                                  tabla += '<table name="movimientos" id="resultados_'+j+'" class="tabla2">';
                                  tabla += '<caption>Movimientos del producto: '+datos[0]['nombre_plastico']+'</caption>';
                                  tabla += '<tr><th class="tituloTabla" colspan="6">MOVIMIENTOS</th></tr>';
                                  tabla += '<tr>\n\
                                              <th>Item</th>\n\
                                              <th>Fecha</th>\n\
                                              <th>Hora</th>\n\
                                              <th>Tipo</th>\n\
                                              <th>Cantidad</th>\n\
                                              <th>Comentarios</th>\n\
                                           </tr>';
                                  var subtotalRetiro = 0;
                                  var subtotalIngreso = 0;
                                  var subtotalReno = 0;
                                  var subtotalDestruccion = 0;
                                  var totalConsumos = 0;

                                  for (var i=0; i<max; i++) { 
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
                                                <td>'+offset+'</td>\n\
                                                <td>'+fecha+'</td>\n\
                                                <td>'+hora+'</td>\n\
                                                <td>'+tipo2+'</td>\n\
                                                <td class="'+claseResaltado+'"><a href="editarMovimiento.php?id='+idmov+'">'+cantidad.toLocaleString()+'</a></td>\n\
                                                <td>'+comentarios+'</td>\n\
                                              </tr>';
                                    offset++;  
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

                                  tabla += '<tr>\n\
                                              <td class="pieTabla" colspan="6">\n\
                                                <input type="button" id="5" indice="'+j+'" name="exportarBusqueda" value="EXPORTAR" class="btn btn-primary exportar">\n\
                                              </td>\n\
                                            </tr>\n\
                                          </table>';
                                  break;
      default: break;
    }
  
  return tabla;
} 

/**
 * \brief Función que, en base a los parámetros pasados, ejecuta la o las consultas pertinentes
 * @param {String} radio String que indica el tipo de consulta a realizar (stock de entidades o de productos, total en bóveda, o movimientos).
 * @param {type} queries Array de Strings con la o las consultas a realizar.
 * @param {type} consultasCSV Array de Strings con las consultas para generar el o los CSV.
 * @param {type} idProds Array de Int con los ID del o de los productos.
 * @param {type} tipoConsultas Array de Strings con el mensaje que indica los tipos de consultas realizadas.
 * @param {type} entidadesStock Array de Strings con los nombres de la o las entidades seleccionadas para consultar su stock.
 * @param {type} entidadesMovimiento Array de Strings con los nombres de la o las entidades seleccionadas para consultas sus movimientos.
 * @param {type} nombresProductos Array de Strings con los nombres del o de los productos seleccionados.
 * @param {type} nombres Array de Strings con el nombre a mostrar en las pestañas generadas.
 * @param {type} ent Array de Strings con los nombres de las entidades seleccionadas.
 * @param {String} prodHint String con la cadena usada para la búsqueda de productos.
 * @param {String} mensajeTipo String con el tipo de consulta realizada (si fue stock o movimientos y de que entidad o producto). 
 * @param {String} mensajeUsuario String con el usuario seleccionado, sólo en el caso se haya filtrado por algún usuario involucrado.
 * @param {String} mensajeFecha String con el rango de fechas elegido para la consulta, o todo el rango en caso de no haberlo seleccionado.
 * @returns {String} String con el HTML que contiene los títulos y la tabla a mostrar. La tabla la generará mostrarTabla a la cual se llama desde acá.
 */
function mostrarResultados(radio, queries, consultasCSV, idProds, tipoConsultas, entidadesStock, entidadesMovimiento, nombresProductos, nombres, ent, prodHint, mensajeTipo, mensajeUsuario, mensajeFecha){
  var url = "data/selectQueryJSON.php";

  $("#main-content").empty();

  var mostrarGlobal = '<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">';
  var activo = '';
  var campos = '';
  var largos = '';
  var mostrarCamposQuery = '';
  var x = 55;
  var tipMov = '';
    
  for (var n in queries){//alert(queries[n]);
    if (n == 0) {
      activo = 'active';
    }
    else {
      activo = '';
    }
    mostrarGlobal += '<li class="nav-item  rounded-right rounded-left">\n\
                        <a class="nav-link '+activo+'" id="pills-'+idProds[n]+'-tab" activepage="1" data-toggle="pill" href="#'+idProds[n]+'" role="tab" aria-controls="'+nombres[n]+'" aria-selected="true">'+nombres[n]+'</a>\n\
                      </li>'; 
  }
  mostrarGlobal += '</ul>';
  mostrarGlobal += '<div class="tab-content rounded-right" id="pills-tabContent">';

  var jsonQuery = JSON.stringify(queries);
  
  $.getJSON(url, {query: ""+jsonQuery+"", tipo: ""+radio+""}).done(function(request){
    for (var j in request){
      var datos = request[j].resultado;
      var totalPlasticos = request[j].suma;
      var totalRetiros = request[j]["retiros"];
      var totalRenovaciones = request[j]["renovaciones"];
      var totalDestrucciones = request[j]["destrucciones"];alert(totalDestrucciones);
      var totalIngresos = request[j]["ingresos"];
      var totalDatos = parseInt(request[j].totalRows, 10);
      if (j == 0) {
        activo = 'fade show active';
      }
      else {
        activo = 'fade';
      }
      var divi = '<div class="tab-pane '+activo+' rounded-right" id="'+idProds[j]+'" indice="'+j+'" role="tabpanel" aria-labelledby="pills-home-tab">'; 
      var mostrar = '';
      mostrar += divi;
      var titulo = "<h2 id='titulo'>Resultado de la búsqueda</h2>";
      mostrar += titulo;
      var mensajeConsulta = tipoConsultas[j];
      if (mensajeTipo !== null) {
        mensajeConsulta += " "+mensajeTipo;
      }
      mensajeConsulta += " "+mensajeFecha;
      if (mensajeUsuario !== null) {
        mensajeConsulta += mensajeUsuario;
      } 
      mostrar += "<h3>"+mensajeConsulta+"</h3>";
      var mensajeTotalDatos = '';
      var todos = false;
      if (totalDatos >= 1) 
        {
        var formu = '<form name="resultadoBusqueda" id="resultadoBusqueda_'+j+'" target="_blank" action="exportar.php" method="POST" class="exportarForm">';
        switch (radio){
          case 'entidadStock':  if (entidadesStock[0] === 'todos'){
                                  todos = true;
                                }
                                mensajeTotalDatos = "<h3>Total de productos: <font class='naranja'>"+totalDatos+"</font></h3>";
                                break;
          case 'productoStock': break;
          case 'totalStock':  mensajeTotalDatos = "<h3>Total de entidades: <font class='naranja'>"+totalDatos+"</font></h3>";
                              break;
          case 'entidadMovimiento': if (entidadesMovimiento[0] === 'todos'){
                                      todos = true;
                                    }
                                    mensajeTotalDatos = "<h3>Total de movimientos: <font class='naranja'>"+totalDatos+"</font></h3>";
                                    break;
          case 'productoMovimiento':  mensajeTotalDatos = "<h3>Total de movimientos: <font class='naranja'>"+totalDatos+"</font></h3>";
                                      break;
          default: break;
        }
        
        var subtotales = {"retiros":totalRetiros, "renovaciones":totalRenovaciones, "destrucciones":totalDestrucciones, "ingresos":totalIngresos};
        var max = parseInt(tamPagina, 10);
        var parcial = true;
        if (totalDatos < tamPagina){
          max = totalDatos%tamPagina;
          parcial = false;
        }
        var tabla = mostrarTabla(radio, datos, j, todos, 1, parcial, subtotales, max, totalPlasticos); 
        
        formu += tabla;
        
        var datosOcultos = '<table id="datosOcultos_'+j+'" name="datosOcultos" class="tabla2" style="display:none">';
        switch (radio){
          case 'entidadStock':  campos = "Id-Entidad-Nombre-BIN-C&oacute;digo-Contacto-Snapshot-&Uacute;lt. Mov.-Stock-Alarma1-Alarma2-Mensaje";
                                largos = "0.8-1.2-2.5-0.8-2-1-1-1.2-1.2-1-2-1.7";
                                mostrarCamposQuery = "1-1-1-0-1-0-0-1-1-0-0-1";
                                x = 20;
                                tipMov = 'entStock';
                                datosOcultos += '<tr><td style="display:none"><input type="text" id="query_'+j+'" name="query_'+j+'" value="'+queries[j]+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="idTipo" name="idTipo" value="1"></td>\n\
                                                    <td style="display:none"><input type="text" id="indice" name="indice" value=""></td>\n\
                                                    <td style="display:none"><input type="text" id="consultaCSV_'+j+'" name="consultaCSV_'+j+'" value="'+consultasCSV[j]+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="campos" name="campos" value="'+campos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="largos" name="largos" value="'+largos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="param" name="param" value=""></td>\n\
                                                    <td style="display:none"><input type="text" id="entidad_'+j+'" name="entidad_'+j+'" value="'+entidadesStock[j]+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="mostrar" name="mostrar" value="'+mostrarCamposQuery+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="tipoConsulta_'+j+'" name="tipoConsulta_'+j+'" value="'+tipoConsultas[j]+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="x" name="x" value="'+x+'"></td>\n\
                                                  </tr>';
                                break;
          case 'productoStock': campos = "Id-Entidad-Nombre-BIN-C&oacute;digo-Contacto-Snapshot-Stock-Alarma1-Alarma2-Mensaje-&Uacute;ltimo Movimiento";
                                largos = "0.8-1.2-2.5-0.8-2-2.5-1-1-1-1-2-1";
                                mostrarCamposQuery = "1-1-1-1-1-1-1-1-0-0-1-1";;
                                x = 22;
                                tipMov = 'prodStock';
                                datosOcultos += '<tr><td style="display:none"><input type="text" id="query_'+j+'" name="query_'+j+'" value="'+queries[j]+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="idTipo" name="idTipo" value="2"></td>\n\
                                                    <td style="display:none"><input type="text" id="indice" name="indice" value=""></td>\n\
                                                    <td style="display:none"><input type="text" id="consultaCSV_'+j+'" name="consultaCSV_'+j+'" value="'+consultasCSV[j]+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="campos" name="campos" value="'+campos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="largos" name="largos" value="'+largos+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="nombreProducto" name="nombreProducto" value="'+nombresProductos[j]+'"></td>\n\
                                                    <td style="display:none"><input type="text" id="param" name="param" value=""></td>\n\
                                                      <td style="display:none"><input type="text" id="idProd" name="idProd" value="'+idProds[j]+'"></td>\n\
                                                      <td style="display:none"><input type="text" id="mostrar" name="mostrar" value="'+mostrarCamposQuery+'"></td>\n\
                                                      <td style="display:none"><input type="text" id="tipoConsulta_'+j+'" name="tipoConsulta_'+j+'" value="'+mensajeConsulta+'"></td>\n\
                                                      <td style="display:none"><input type="text" id="x" name="x" value="'+x+'"></td>\n\
                                                    </tr>';
                                break;
          case 'totalStock':  campos = 'Id-Entidad-Stock';
                              largos = '1-3.0-1.8';
                              mostrarCamposQuery = "1-1-1";
                              x = 60;
                              tipMov = 'totalStock';
                              datosOcultos += '<tr><td style="display:none"><input type="text" id="query_0" name="query_0" value="'+queries[j]+'"></td>\n\
                                                <td style="display:none"><input type="text" id="idTipo" name="idTipo" value="3"></td>\n\
                                                <td style="display:none"><input type="text" id="indice" name="indice" value=""></td>\n\
                                                <td style="display:none"><input type="text" id="consultaCSV_0" name="consultaCSV_0" value="'+consultasCSV[j]+'"></td>\n\
                                                <td style="display:none"><input type="text" id="campos" name="campos" value="'+campos+'"></td>\n\
                                                <td style="display:none"><input type="text" id="mostrar" name="mostrar" value="'+mostrarCamposQuery+'"></td>\n\
                                                <td style="display:none"><input type="text" id="largos" name="largos" value="'+largos+'"></td>\n\
                                                <td style="display:none"><input type="text" id="param" name="param" value=""></td>\n\
                                                <td style="display:none"><input type="text" id="tipoConsulta_0" name="tipoConsulta_0" value="'+mensajeConsulta+'"></td>\n\
                                                <td style="display:none"><input type="text" id="x" name="x" value="'+x+'"></td>\n\
                                              </tr>';
                             break;
          case 'entidadMovimiento': campos = 'Id-Entidad-Nombre-BIN-Código-Contacto-Snapshot-Stock-Alarma1-Alarma2-ComentariosProd-&Uacute;ltimo Movimiento-Fecha-Hora-Cantidad-Tipo-Comentarios';
                                    //Orden de la consulta: entidad - nombre - bin - codigo - contacto - snapshot - stock - alarma1 - alarma2 - prodcom - fecha - hora - cantidad - tipo - comentarios
                                    largos = '0.6-1.6-1.9-1-1-1-1-1-1-1-1.1-1.5-1.5-0.8-1.2-1.4-2';
                                    mostrarCamposQuery = '1-1-1-0-0-0-0-0-0-0-0-0-1-1-1-1-1';
                                    x = 40;
                                    tipMov = 'entMov';
                                    datosOcultos += '<tr><td style="display:none"><input type="text" id="query_'+j+'" name="query_'+j+'" value="'+queries[j]+'"></td>\n\
                                                        <td style="display:none"><input type="text" id="idTipo" name="idTipo" value="4"></td>\n\
                                                        <td style="display:none"><input type="text" id="indice" name="indice" value=""></td>\n\
                                                        <td style="display:none"><input type="text" id="consultaCSV_'+j+'" name="consultaCSV_'+j+'" value="'+consultasCSV[j]+'"></td>\n\
                                                        <td style="display:none"><input type="text" id="campos" name="campos" value="'+campos+'"></td>\n\
                                                        <td style="display:none"><input type="text" id="largos" name="largos" value="'+largos+'"></td>\n\
                                                        <td style="display:none"><input type="text" id="param" name="param" value=""></td>\n\
                                                        <td style="display:none"><input type="text" id="mostrar" name="mostrar" value="'+mostrarCamposQuery+'"></td>\n\
                                                        <td style="display:none"><input type="text" id="entidad_'+j+'" name="entidad_'+j+'" value="'+entidadesMovimiento[j]+'"></td>\n\
                                                        <td style="display:none"><input type="text" id="tipoConsulta_'+j+'" name="tipoConsulta_'+j+'" value="'+mensajeConsulta+'"></td>\n\
                                                        <td style="display:none"><input type="text" id="x" name="x" value="'+x+'"></td>\n\
                                                      </tr>';
                                    break;
          case 'productoMovimiento':  campos = 'Id-Entidad-Nombre-BIN-Código-Contacto-Snapshot-Stock-Alarma1-Alarma2-ComentariosProd-&Uacute;ltimo Movimiento-Fecha-Hora-Cantidad-Tipo-Comentarios';
                                      //Orden de la consulta: entidad - nombre - bin - codigo - snapshot - stock - alarma - prodcom - fecha - hora - cantidad - tipo - comentarios
                                      largos = '0.4-1.5-1.8-1-1-1-1-1-1-1-1.1-1.5-1.5-0.8-1.2-1.4-2';
                                      mostrarCamposQuery = '1-0-0-0-0-0-0-0-0-0-0-0-1-1-1-1-1';
                                      x = 40;
                                      tipMov = 'prodMov';
                                      datosOcultos += '<tr><td style="display:none"><input type="text" id="query_'+j+'" name="query_'+j+'" value="'+queries[j]+'"></td>\n\
                                                          <td style="display:none"><input type="text" id="idTipo" name="idTipo" value="5"></td>\n\
                                                          <td style="display:none"><input type="text" id="indice" name="indice" value=""></td>\n\
                                                          <td style="display:none"><input type="text" id="consultaCSV_'+j+'" name="consultaCSV_'+j+'" value="'+consultasCSV[j]+'"></td>\n\
                                                          <td style="display:none"><input type="text" id="campos" name="campos" value="'+campos+'"></td>\n\
                                                          <td style="display:none"><input type="text" id="largos" name="largos" value="'+largos+'"></td>\n\
                                                          <td style="display:none"><input type="text" id="param" name="param" value=""></td>\n\
                                                          <td style="display:none"><input type="text" id="nombreProducto" name="nombreProducto" value="'+nombresProductos[j]+'"></td>\n\
                                                          <td style="display:none"><input type="text" id="mostrar" name="mostrar" value="'+mostrarCamposQuery+'"></td>\n\
                                                          <td style="display:none"><input type="text" id="idProd" name="idProd" value="'+idProds[j]+'"></td>\n\
                                                          <td style="display:none"><input type="text" id="tipoConsulta_'+j+'" name="tipoConsulta_'+j+'" value="'+mensajeConsulta+'"></td>\n\
                                                          <td style="display:none"><input type="text" id="x" name="x" value="'+x+'"></td>\n\
                                                        </tr>';
                                      break;
          default: break;
        }
        datosOcultos += '</table>';
        
        formu += datosOcultos;
        formu += '</form>';
        
        if (mensajeTotalDatos !== ''){
          mostrar += mensajeTotalDatos;
        }
        

        ///************************************ Comienzo paginación **********************************************************
        
        var totalPaginas = Math.ceil(totalDatos/tamPagina);
        var page = 1;
        var ultimoRegistro = tamPagina;
        if (tamPagina > totalDatos){
          ultimoRegistro = totalDatos;
        }
        if (totalPaginas > 1){
          var rango = "<h5 id='rango_"+j+"' class='rango'>(P&aacute;gina "+page+": registros del 1 al "+ultimoRegistro+")</h5>";
          mostrar += rango;
        }
        
        mostrar += formu;
        
        if (totalPaginas > 1) {
          var paginas = '<div class="pagination" id="paginas" indice="'+j+'">\n\
                          <ul>';
          paginas += '<input style="display: none" type="text" id="totalPaginas_'+j+'" value="'+totalPaginas+'">';
          paginas += '<input style="display: none" type="text" id="totalRegistros_'+j+'" value="'+totalDatos+'">';
          paginas += '<input style="display: none" type="text" id="totalPlasticos_'+j+'" value="'+totalPlasticos+'">';
          for (var k=1;k<=totalPaginas;k++) {
            if (page === k) {
            //si muestro el índice de la página actual, no coloco enlace
              paginas += '<li ><a class="paginate pageActive" i='+j+' data="'+k+'">'+k+'</a></li>';
            }  
            else {
            //si el índice no corresponde con la página mostrada actualmente,
            //coloco el enlace para ir a esa página
              paginas += '<li><a class="paginate" i='+j+' data="'+k+'">'+k+'</a></li>';
            }
          }
          if (page !== totalPaginas) {
            paginas += '<li><a class="paginate siguiente" i='+j+' data="'+(page+1)+'">Siguiente</a></li>';
          } 
          paginas += '</ul>';
          paginas += '</div>';
          mostrar += paginas;
        }
        ///***************************************** FIN paginación **********************************************************
        
        if (idProds[j] === undefined) {
          idProds[j] = '';
        }
        var volver = '<br><a title="Volver a BÚSQUEDAS" href="../controlstock/busquedas.php?h='+prodHint+'&t='+tipMov+'&id='+idProds[j]+'&ent='+ent[j]+'" name="volver" id="volverBusqueda" >Volver</a><br><br>';
        mostrar += volver;
        mostrar += '</div>';
        $("#pills-tabContent").append(mostrar);
        delete(totalDatos);
        delete(datos);  
        }/// FIN del if de totalDatos>1  
      else {
        mostrar += "<br><hr><h3>No existen registros para la consulta realizada.</h3><hr>";
        var volver = '<br><a title="Volver a BÚSQUEDAS" href="../controlstock/busquedas.php?h='+prodHint+'&t='+tipMov+'&id='+idProds[j]+'&ent='+ent[j]+'" name="volver" id="volverBusqueda" >Volver</a><br><br>';
        mostrar += volver;
        mostrar += '</div>';
        $("#pills-tabContent").append(mostrar);
      }         
    }
  });

  mostrarGlobal += '</div>';
  $("#main-content").append(mostrarGlobal);
  }

/**
 * \brief Función que ejecuta la búsqueda y muestra el resultado.
 */
function realizarBusqueda(){  
  var timestamp = Math.round(Date.now() / 1000);
      
  if(timestamp - $("#timestampSesion").val() > $("#duracionSesion").val()) {
    alert('Su sesión expiró. Por favor vuelva loguearse.'); 
    window.location.href = "../controlstock/index.php";
  }
  else {
    verificarSesion();
    var radio = $('input:radio[name=criterio]:checked').val();
    var entidadesStock = new Array();
    $("#entidadStock option:selected").each(function() {
      entidadesStock.push($(this).val());
    });
    var entidadesMovimiento = new Array();
    $("#entidadMovimiento option:selected").each(function() {
      entidadesMovimiento.push($(this).val());
    });
    var todos = false;
    var idProds = new Array();
    var nombresProductos = new Array();
    var nombres = new Array();
    var tipoConsultas = new Array();
    $("#hint option:selected").each(function() {
      idProds.push($(this).val());
      var nombreProducto = $(this).text( );
      nombresProductos.push(nombreProducto);
      if ((nombreProducto !== "undefined") && (nombreProducto !== '') && (nombreProducto !== '--Seleccionar--')) {
        ///Separo en partes el nombreProducto que contiene [entidad: codigo] --- nombreProducto
        var tempo = nombreProducto.split("- ");
        nombres.push(tempo[1].trim());
        //var tempo2 = tempo1.split("{");
        //var nombreSolo = tempo2[0].trim();
      }
    });
    var queries = new Array();
    var consultasCSV = new Array();
    
    var tipo = $("#tipo").find('option:selected').val( );
    var idUser = $("#usuario").val();
    var nombreUsuario = $("#usuario").find('option:selected').text( );
    var radioFecha = $('input:radio[name=criterioFecha]:checked').val();
    var inicio = $("#inicio").val();
    var fin = $("#fin").val();
    var mes = $("#mes").val();
    var año = $("#año").val();
    var rangoFecha = null;
    var prodHint = '';
    var ent = new Array();
    
    var query = 'select productos.entidad, productos.nombre_plastico, productos.bin, productos.codigo_emsa, productos.contacto, productos.snapshot, productos.ultimoMovimiento, productos.stock, productos.alarma1, productos.alarma2, productos.comentarios as prodcom';
    var consultaCSV = 'select productos.entidad as entidad, productos.nombre_plastico as nombre, productos.bin as BIN, productos.stock as stock, productos.alarma1, productos.alarma2';
    var tipoConsulta = '';
    var mensajeFecha = '';
    
    var validado = true;
    var validarFecha = false;
    var validarTipo = false;
    var validarUser = false;
    var ordenFecha = false;
        
    switch (radio) {
      case 'entidadStock':  delete nombres;
                            var nombres = new Array();
                            for (var i in entidadesStock){
                              delete (query);
                              delete (consultaCSV);
                              delete (tipoConsulta);
                              var tipoConsulta = '';
                              var query = 'select productos.entidad, productos.nombre_plastico, productos.bin, productos.codigo_emsa, productos.contacto, productos.snapshot, productos.ultimoMovimiento, productos.stock, productos.alarma1, productos.alarma2, productos.comentarios as prodcom';
                              var consultaCSV = 'select productos.entidad as entidad, productos.nombre_plastico as nombre, productos.bin as BIN, productos.stock as stock, productos.alarma1, productos.alarma2';
                              if (entidadesStock[i] !== 'todos') {
                                ent.push(entidadesStock[i]);
                                query += " from productos where entidad='"+entidadesStock[i]+"' and estado='activo'";
                                consultaCSV += " from productos where entidad='"+entidadesStock[i]+"' and estado='activo'";
                                tipoConsulta = 'Stock de '+entidadesStock[i];
                              } 
                              else {
                                query += " from productos where estado='activo'";
                                consultaCSV += " from productos where estado='activo'";
                                tipoConsulta = 'Stock de todas las entidades';
                                todos = true;
                              }
                              queries.push(query);
                              consultasCSV.push(consultaCSV);
                              tipoConsultas.push(tipoConsulta);
                              idProds.push(entidadesStock[i]);
                              nombres.push(entidadesStock[i]);
                            }
                            if (todos && (entidadesStock.length > 1)){
                              alert('No se puede consultar "TODOS" junto con otras entidades. Por favor verifique.');
                              $("#entidadStock").focus();
                              return;
                            }
                            if (entidadesStock.length > limiteSeleccion) {
                              alert("Se superó el máximo de "+limiteSeleccion+" opciones elegidas. Por favor verifique.");
                              $("#entidadStock").focus();
                              return;
                            }
                           break;
      case 'productoStock': for (var i in idProds){
                              if ((idProds[i] === 'NADA') || (nombresProductos[i] === '')){
                                alert('Debe seleccionar al menos un producto ó seleccionar no debe de estar marcado. Por favor verifique.');
                                document.getElementById("productoStock").focus();
                                validado = false;
                                return false;
                              }
                              else {
                                query = 'select productos.entidad, productos.nombre_plastico, productos.bin, productos.codigo_emsa, productos.contacto, productos.snapshot, productos.ultimoMovimiento, productos.stock, productos.alarma1, productos.alarma2, productos.comentarios as prodcom';
                                query += " from productos where idProd="+idProds[i];
                                consultaCSV = 'select productos.entidad as entidad, productos.nombre_plastico as nombre, productos.bin as BIN, productos.stock as stock, productos.alarma1, productos.alarma2';
                                consultaCSV += " from productos where idProd="+idProds[i];
                                tipoConsulta = 'Stock del producto '+nombres[i];
                                queries.push(query);
                                consultasCSV.push(consultaCSV);
                                tipoConsultas.push(tipoConsulta);
                              }
                              prodHint = $("#productoStock").val();
                            }  
                            
                            break;
      case 'totalStock':  query = "select entidad, sum(stock) as subtotal from productos where estado='activo' group by entidad";
                          queries[0] = query;
                          consultaCSV = "select entidad as Entidad, sum(stock) as Subtotal from productos where estado='activo' group by entidad";
                          consultasCSV[0] = consultaCSV;
                          tipoConsulta = 'Total de plásticos en bóveda';
                          tipoConsultas[0] = tipoConsulta;
                          idProds[0] = 1;
                          delete nombres;
                          var nombres = new Array();
                          nombres[0] = "Stock en Bóveda";
                          break;                    
      case 'entidadMovimiento': delete nombres;
                                var nombres = new Array();
                                for (var i in entidadesMovimiento){
                                  query = 'select productos.idprod, productos.entidad, productos.nombre_plastico, productos.bin, productos.codigo_emsa, productos.contacto, productos.snapshot, productos.ultimoMovimiento, productos.stock, productos.alarma1, productos.alarma2, productos.comentarios as prodcom';
                                  query += ", DATE_FORMAT(movimientos.fecha, '%d/%m/%Y') as fecha, DATE_FORMAT(movimientos.hora, '%H:%i') as hora, movimientos.cantidad, movimientos.tipo, movimientos.comentarios, movimientos.idmov from productos inner join movimientos on productos.idprod=movimientos.producto where productos.estado='activo' ";
                                  consultaCSV = "select DATE_FORMAT(movimientos.fecha, '%d/%m/%Y'), DATE_FORMAT(movimientos.hora, '%H:%i') as hora, productos.entidad, productos.nombre_plastico, productos.bin, movimientos.tipo, movimientos.cantidad, movimientos.comentarios from productos inner join movimientos on productos.idprod=movimientos.producto where productos.estado='activo' ";
                                  if (entidadesMovimiento[i] !== 'todos') {
                                    ent = entidadesMovimiento[i];
                                      query += "and productos.entidad='"+entidadesMovimiento[i]+"'";
                                      consultaCSV += "and productos.entidad='"+entidadesMovimiento[i]+"'";
                                      tipoConsulta = 'Movimientos de '+entidadesMovimiento[i];
                                  } 
                                  else {
                                    tipoConsulta = 'Movimientos de todas las entidades';
                                    todos = true;
                                  }
                                  queries.push(query);
                                  consultasCSV.push(consultaCSV);
                                  tipoConsultas.push(tipoConsulta);
                                  idProds.push(entidadesMovimiento[i]);
                                  nombres.push(entidadesMovimiento[i]);
                                }
                                if (todos && (entidadesMovimiento.length > 1)){
                                  alert('No se puede consultar "TODOS" junto con otras entidades. Por favor verifique.');
                                  $("#entidadMovimiento").focus();
                                  return;
                                }
                                if (entidadesMovimiento.length > limiteSeleccion) {
                                  alert("Se superó el máximo de "+limiteSeleccion+" opciones elegidas. Por favor verifique.");
                                  $("#entidadMovimiento").focus();
                                  return;
                                }
                                validarFecha = true;
                                validarTipo = true;
                                validarUser = true;
                                ordenFecha = true;
                                break;                       
      case 'productoMovimiento':  for (var k in idProds){
                                    query = 'select productos.entidad, productos.nombre_plastico, productos.bin, productos.codigo_emsa, productos.contacto, productos.snapshot, productos.ultimoMovimiento, productos.stock, productos.alarma1, productos.alarma2, productos.comentarios as prodcom';
                                    query += ", DATE_FORMAT(movimientos.fecha, '%d/%m/%Y') as fecha, DATE_FORMAT(movimientos.hora, '%H:%i') as hora, movimientos.cantidad, movimientos.tipo, movimientos.comentarios, movimientos.idmov from productos inner join movimientos on productos.idprod=movimientos.producto where ";
                                    consultaCSV = 'select productos.entidad as entidad, productos.nombre_plastico as nombre, productos.bin as BIN, productos.stock as stock, productos.alarma1, productos.alarma2';
                                    consultaCSV = "select DATE_FORMAT(movimientos.fecha, '%d/%m/%Y'), DATE_FORMAT(movimientos.hora, '%H:%i') as hora, productos.entidad, productos.nombre_plastico, productos.bin, movimientos.tipo, movimientos.cantidad, movimientos.comentarios from productos inner join movimientos on productos.idprod=movimientos.producto where productos.estado='activo' ";                                 
                                    if ((idProds[k] === 'NADA') || (nombresProductos[k] === '')){
                                      alert('Debe seleccionar al menos un producto ó seleccionar no debe de estar marcado. Por favor verifique.');
                                      document.getElementById("productoMovimiento").focus();
                                      validado = false;
                                      return false;
                                    }
                                    else {
                                      query += "idProd="+idProds[k];
                                      consultaCSV += "and idProd="+idProds[k];
                                      queries.push(query);
                                      consultasCSV.push(consultaCSV);
                                      validarFecha = true;
                                      validarTipo = true;
                                      validarUser = true;
                                      ordenFecha = true;
                                    }
                                    tipoConsulta = 'Movimientos del producto '+nombres[k];
                                    tipoConsultas.push(tipoConsulta);
                                    prodHint = $("#productoMovimiento").val();
                                  }
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
                              rangoFecha = " and (fecha >='"+inicio+"') and (fecha<='"+fin+"')";
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
                      if (mesSiguiente < 10) 
                        {
                        mesSiguiente = '0'+mesSiguiente;
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
                    rangoFecha = " and (fecha >='"+inicio+"') and (fecha<'"+fin+"')";
                    break;
        case 'todos': break;
        default: break;
      }
    }
    
    if (validado) 
      {
      for (var n in queries){
        if (rangoFecha !== null) {
          queries[n] += rangoFecha;
          consultasCSV[n] += rangoFecha; 
        }
        var mensajeTipo = null;  
        if (validarTipo) {   
          if (tipo !== 'Todos') {
            queries[n] += " and tipo='"+tipo+"'";
            consultasCSV[n] += " and tipo='"+tipo+"'";
            mensajeTipo = "del tipo "+tipo;
          }
          else {
            mensajeTipo = "de todos los tipos";
          };
        }

        var mensajeUsuario = null;
        if (validarUser) {
          if (idUser !== 'todos') {
            queries[n] += " and (control1="+idUser+" or control2="+idUser+")";
            consultasCSV[n] += " and (control1="+idUser+" or control2="+idUser+")";
            mensajeUsuario = " en los que está involucrado el usuario "+nombreUsuario;
          }
        }

        if (ordenFecha) {
          queries[n] += " order by entidad asc, nombre_plastico asc, movimientos.fecha desc, hora desc,  idprod";
          consultasCSV[n] += " order by entidad asc, nombre_plastico asc, movimientos.fecha desc, hora desc, idprod";
        }
        else {
          queries[n] += " order by entidad asc, nombre_plastico asc, idprod asc";
          consultasCSV[n] += " order by entidad asc, nombre_plastico asc, idprod asc";
        }
      }  
      mostrarResultados(radio, queries, consultasCSV, idProds, tipoConsultas, entidadesStock, entidadesMovimiento, nombresProductos, nombres, ent, prodHint, mensajeTipo, mensajeUsuario, mensajeFecha);
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
 * @param {String} entidadSeleccionada String con de la entidad previamente seleccionada (si corresponde).
 */
function cargarFormBusqueda(selector, hint, tipo, idProd, entidadSeleccionada){
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
                 
        var tabla = '<table id="parametros" name="parametros" class="tabla2">\n\
                      <caption>Formulario para realizar las consultas</caption>';
        var tr = '<tr>\n\
                    <th colspan="5" class="tituloTabla">STOCK</th>\n\
                  </tr>';
        tr += '<tr>\n\
                <td class="fondoVerde">\n\
                  <input type="radio" name="criterio" title="Elegir el tipo de consulta a realizar\nSeleccionar si se quiere conocer el stock de una entidad" value="entidadStock" checked="checked">\n\
                </td>\n\
                <th>Entidad:</th>\n\
                  <td colspan="3">\n\
                    <select name="entidad" id="entidadStock" tabindex="1" style="width: 100%" multiple title="Seleccionar la entidad" size="6">\n\
                      <option value="todos" selected="yes">---TODOS---</option>';
        for (var j in entidades) {
          var entidad = entidades[j].trim();
          tr += '<option value="'+entidad+'">'+entidad+'</option>';
        }  
        tr += '   </select>\n\
                </td>\n\
              </tr>';
        tr += '<tr>\n\
                <td class="fondoVerde">\n\
                  <input type="radio" name="criterio" title="Elegir el tipo de consulta a realizar\nSeleccionar si se quiere conocer el stock de un producto" value="productoStock">\n\
                </td>\n\
                <th>Producto:</th>\n\
                <td align="center" colspan="3">\n\
                  <input type="text" id="productoStock" name="producto" placeholder="Producto" title="Ingresar el producto" class="agrandar size="9" tabindex="2" onkeyup=\'showHint(this.value, "#productoStock", "")\'>\n\
                </td>\n\
              </tr>';
        tr += '<tr>\n\
                <td class="fondoVerde">\n\
                  <input type="radio" name="criterio" title="Elegir el tipo de consulta a realizar\nSeleccionar si se quiere conocer el stock total de plásticos" value="totalStock">\n\
                </td>\n\
                <td colspan="4" class="negrita" style="text-align: left">Total de plásticos en bóveda</td>\n\
              </tr>';
        tr += '<tr>\n\
                <th colspan="5" class="centrado">MOVIMIENTOS</th>\n\
              </tr>';
        tr += '<tr>\n\
                <td class="fondoVerde">\n\
                  <input type="radio" name="criterio" title="Elegir el tipo de consulta a realizar\nSeleccionar si se quieren conocer los movimientos de una entidad" value="entidadMovimiento">\n\
                </td>\n\
                <th>Entidad:</th>\n\
                  <td colspan="3">\n\
                    <select name="entidad" id="entidadMovimiento" title="Seleccionar la entidad" tabindex="3" multiple style="width: 100%" size="6">\n\
                      <option value="todos" selected="yes">---TODOS---</option>';
        for (var j in entidades) {
          var entidad1 = entidades[j].trim();
          tr += '<option value="'+entidad1+'">'+entidad1+'</option>';
        }   
        tr += '   </select>\n\
                </td>\n\
              </tr>';
        tr += '<tr>\n\
                <td class="fondoVerde">\n\
                  <input type="radio" name="criterio" title="Elegir el tipo de consulta a realizar\nSeleccionar si se quieren conocer los movimientos de un producto" value="productoMovimiento">\n\
                </td>\n\
                <th>Producto:</th>\n\
                <td align="center" colspan="3">\n\
                  <input type="text" id="productoMovimiento" name="producto" placeholder="Producto" title="Ingresar el producto" class="agrandar" size="9" tabindex="4" onkeyup=\'showHint(this.value, "#productoMovimiento", "")\'>\n\
                </td>\n\
              </tr>';
        tr += '<tr>\n\
                  <td class="fondoNaranja">\n\
                    <input type="radio" name="criterioFecha" title="Elegir el período a buscar\nSeleccionar si se quiere buscar por fechas" value="intervalo">\n\
                  </td>\n\
                  <th>Entre:</th>\n\
                  <td>\n\
                    <input type="date" name="inicio" id="inicio" title="Elegir la fecha de inicio\n(Sólo si se optó por una consulta por fechas)" tabindex="6" style="width:100%; text-align: center" min="2017-10-01">\n\
                  </td>\n\
                  <td>y:</td>\n\
                  <td>\n\
                    <input type="date" name="fin" id="fin" title="Elegir la fecha de finalización\n(Sólo si se optó por una consulta por fechas)" tabindex="7" style="width:100%; text-align: center" min="2017-10-01">\n\
                  </td>\n\
                </tr>';
        tr += '<tr>\n\
                <td class="fondoNaranja">\n\
                  <input type="radio" name="criterioFecha" title="Elegir el período a buscar\nSeleccionar si se quiere buscar por meses" value="mes">\n\
                </td>\n\
                <th>Mes:</th>\n\
                <td>\n\
                  <select id="mes" name="mes" title="Elegir el mes a buscar\n(Sólo si se optó por una consulta por mes)" tabindex="8" style="width:100%">\n\
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
                  <select id="año" name="año" title="Elegir el año\n(Sólo si se optó por una consulta por mes)" tabindex="9" style="width:100%">\n\
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
                  <input type="radio" name="criterioFecha" title="Elegir el período a buscar\nSeleccionar si se quieren TODOS los movimientos" value="todos" checked="checked">\n\
                </td>\n\
                <th>TODOS</th>';
        tr += '<tr>\n\
                <th>Tipo:</th>\n\
                <td>\n\
                  <select id="tipo" name="tipo" title="Elegir el tipo de consulta a buscar" tabindex="10" style="width:100%">\n\
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
                  <select name="usuario" id="usuario" title="Elegir un usuario" tabindex="11" style="width: 100%">\n\
                    <option value="todos">---TODOS---</option>';
        for (var j in nombresUsuarios) {
            tr += '<option value="'+idusers[j]+'">'+nombresUsuarios[j]+' '+apellidosUsuarios[j]+'</option>';
          }      
        tr += '   </select>\n\
                </td>\n\
              </tr>';
        tr += '<tr>\n\
                <td colspan="5" class="pieTabla">\n\
                  <input type="button" class="btn btn-success" name="consultar" id="realizarBusqueda" title="Ejecutar la consulta" tabindex="12" value="Consultar" align="center">\n\
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
            $('#entidadStock option[value="'+entidadSeleccionada+'"]').attr("selected", true);
            sel = '#entidadStock';
          }
          else {
            $('#entidadMovimiento option[value="'+entidadSeleccionada+'"]').attr("selected", true);
            sel = '#entidadMovimiento';
          }
          $(sel).parent().prev().prev().children().prop("checked", true);
          $(sel).focus();
        }
        if (tipo === 'totalStock') {
          $("[name=criterio]").val(["totalStock"]);
          $("#realizarBusqueda").focus();
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
  var query = 'select idprod, nombre_plastico, entidad, codigo_emsa, bin, codigo_origen, contacto, snapshot, stock, alarma1, alarma2, ultimoMovimiento, comentarios from productos where idprod='+idProd+' limit 1';
  
  $.getJSON(url, {query: ""+query+""}).done(function(request) {
    var resultado = request["resultado"];
    var total = request["rows"];
    if (total >= 1) {
      var stock = parseInt(resultado[0]['stock'], 10);
      var alarma1 = parseInt(resultado[0]['alarma1'], 10);
      var alarma2 = parseInt(resultado[0]['alarma2'], 10);
      var ultimoMovimiento = resultado[0]['ultimoMovimiento'];
      if ((ultimoMovimiento === undefined) || (ultimoMovimiento === null)||(ultimoMovimiento === "null")){
        ultimoMovimiento = 'NO HAY';
      }
    }
    var mostrar = "";
    var formu = '<form method="post" id="productUpdate" name="productUpdate" action="producto.php">';
    var tabla = '<table class="tabla2" name="producto">\n\
                  <caption>Formulario para editar los datos del producto</caption>';
    var tr = '<th colspan="3" class="centrado tituloTabla">DATOS DEL PRODUCTO</th>';

    tr += '<tr>\n\
            <th align="left"><font class="negra">Entidad:</font></th><td align="center" colspan="2"><input type="text" name="entidad" id="entidad" title="Ingresar la entidad" placeholder="Entidad" tabindex="4" class="agrandar" style="width:100%; text-align: center"></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Nombre:</font></th>\n\
              <td align="center" colspan="2"><input type="text" name="nombre" id="nombre" title="Ingresar el nombre del producto" placeholder="Nombre" tabindex="5" class="agrandar" maxlength="35" style="width:100%; text-align: center"></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Código EMSA:</font></th>\n\
              <td align="center" colspan="2"><input type="text" name="codigo_emsa" id="codigo_emsa" title="Ingresar el código de EMSA" placeholder="C&oacute;digo EMSA" tabindex="6" class="agrandar" maxlength="35" style="width:100%; text-align: center"></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Código Origen:</font></th>\n\
              <td align="center" colspan="2"><input type="text" name="codigo_origen" id="codigo_origen" title="Ingresar el código de origen" placeholder="C&oacute;digo Origen" tabindex="7" class="agrandar" maxlength="35" style="width:100%; text-align: center"></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Contacto:</font></th>\n\
              <td align="center" colspan="2"><input type="text" id="contacto" name="contacto2" title="Ingresar el contacto" placeholder="Contacto" tabindex="8" class="agrandar" maxlength="35" size="9"></td>\n\
           </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Foto:</font></th>\n\
              <td align="center" colspan="2"><input type="text" id="nombreFoto" name="nombreFoto" title="Ingresar el nombre de la foto" placeholder="Nombre de la foto" tabindex="9" class="agrandar" maxlength="35" size="9"></td>\n\
           </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">BIN:</font></th>\n\
              <td align="center" colspan="2"><input type="text" name="bin" id="bin" title="Ingresar el BIN del producto" placeholder="BIN" tabindex="10" class="agrandar" maxlength="35" style="width:100%; text-align: center"></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Stock:</font></th>\n\
              <td align="center" colspan="2"><input type="text" id="stockProducto" name="stockProducto" title="Stock del producto.\nSólo editable en un producto nuevo" placeholder="Stock" tabindex="-1" class="agrandar" maxlength="35" size="9"></td>\n\
          </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Alarma 1:</font></th>\n\
              <td align="center" colspan="2"><input type="text" id="alarma1" name="alarma1" title="Cantidad de plásticos para disparar el primer nivel de alarma" placeholder="Nivel de advertencia" tabindex="11" class="agrandar" maxlength="35" size="9"></td>\n\
           </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Alarma 2:</font></th>\n\
              <td align="center" colspan="2"><input type="text" id="alarma2" name="alarma2" title="Cantidad de plásticos para disparar el nivel crítico de alarma" placeholder="Nivel Crítico" tabindex="12" class="agrandar" maxlength="35" size="9"></td>\n\
           </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Último Movimiento:</font></th>\n\
              <td align="center" colspan="2"><input type="text" id="ultimoMovimiento" name="ultimoMovimiento" title="Último movimiento realizado.\nNO editable; se actualiza de forma automática" placeholder="&Uacute;ltimo Movimiento" class="agrandar" maxlength="35" size="9"></td>\n\
           </tr>';
    tr += '<tr>\n\
              <th align="left"><font class="negra">Comentarios:</font></th>\n\
              <td align="center" colspan="2"><input type="textarea" id="comentarios" name="comProd"  title="Ingresar un comentario" placeholder="Comentarios" tabindex="13" class="agrandar" maxlength="35" size="9"></td>\n\
          </tr>';
    tr += '<tr>\n\
              <td style="width: 33%;border-right: 0px;"><input type="button" value="EDITAR" id="editarProducto" name="editarProducto" title="Habilitar la edición del producto" tabindex="3" class="btn btn-primary" align="center"/></td>\n\
              <td style="width: 33%;border-left: 0px;border-right: 0px;"><input type="button" value="ACTUALIZAR" id="actualizarProducto" name="actualizarProducto" title="Realizar la actualización" tabindex="14" class="btn btn-warning" align="center"/></td>\n\
              <td style="width: 33%;border-left: 0px;"><input type="button" value="ELIMINAR" id="eliminarProducto" name="eliminarProducto" title="Dar de baja el producto" class="btn btn-danger" align="center"/></td>\n\
          </tr>';
    tr += '<tr>\n\
              <td colspan="3" class="pieTabla"><input type="button" value="NUEVO" id="agregarProducto" name="agregarProducto" title="Agregar un nuevo producto" class="btn btn-success" align="center"/></td>\n\
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
                <caption>Formulario para buscar el producto</caption>\n\
                <tr>\n\
                  <th align="left" class="tituloTabla" colspan="5"><font class="negra">BUSCAR:</font></th>\n\
                </tr>\n\
                <tr>\n\
                  <td align="center" colspan="5" class="pieTabla"><input type="text" id="productoBusqueda" name="productoBusqueda" placeholder="Producto" title="Ingresar el producto" tabindex="1" class="agrandar" size="9" onkeyup=\'showHintProd(this.value, "#productoBusqueda", ""), ""\'></td>\n\
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
    var tabla = '<table id="estadisticas" name="estadisticas" class="tabla2">\n\
                  <caption>Formulario para ver las estadísticas</caption>';
    var tr = '<tr>\n\
                <th colspan="5" class="centrado tituloTabla">CRITERIOS</th>\n\
              </tr>';
    tr += '<tr>\n\
            <td class="fondoVerde"><input type="radio" name="criterio" title="Elegir el criterio a consultar\nSeleccionar si se quiere la estadística de una entidad" value="entidadMovimiento" checked="true"></td>\n\
            <th>Entidad:</th>\n\
            <td colspan="3">\n\
              <select name="entidad" id="entidadGrafica" title="Seleccionar la entidad" style="width: 100%">\n\
                <option value="todos" selected="yes">---TODOS---</option>';
    for (var i in entidades) {
      tr += '<option value="'+entidades[i]['entidad']+'">'+entidades[i]['entidad']+'</option>';
    }
    tr += '   </select>\n\
            </td>\n\
          </tr>';
    tr += '<tr>\n\
            <td class="fondoVerde"><input type="radio" name="criterio" title="Elegir el criterio a consultar\nSeleccionar si se quiere la estadística de un producto" value="productoMovimiento"></td>\n\
            <th>Producto:</th>\n\
            <td align="center" colspan="3"><input type="text" id="productoGrafica" name="producto" title="Ingresar el producto" placeholder="Producto" class="agrandar" size="9" onkeyup="showHint(this.value, \'#productoGrafica\', \'\')"></td>\n\
          </tr>';
    tr += '<th colspan="5" class="centrado">FECHAS</th>';
    tr += '<tr>\n\
                  <td class="fondoNaranja">\n\
                    <input type="radio" name="criterioFecha" title="Elegir el período a buscar\nSeleccionar si se quiere consultar por fechas" value="intervalo">\n\
                  </td>\n\
                  <th>Entre:</th>\n\
                  <td>\n\
                    <input type="date" name="diaInicio" id="diaInicio" title="Elegir la fecha de inicio\n(Sólo si se busca por fechas)" tabindex="6" style="width:100%; text-align: center" min="2017-10-01">\n\
                  </td>\n\
                  <td>y:</td>\n\
                  <td>\n\
                    <input type="date" name="diaFin" id="diaFin" title="Elegir la fecha de finalización\n(Sólo si se busca por fechas)" tabindex="7" style="width:100%; text-align: center" min="2017-10-01">\n\
                  </td>\n\
                </tr>';
        tr += '<tr>\n\
                <td class="fondoNaranja">\n\
                  <input type="radio" name="criterioFecha" title="Elegir el período a buscar\nSeleccionar si se quiere consultar por meses" value="mes">\n\
                </td>\n\
                <th>Mes Inicial:</th>\n\
                <td>\n\
                  <select id="mesInicio" name="mesInicio" title="Elegir el mes de inicio\n(Sólo si se busca por meses)" tabindex="8" style="width:100%">\n\
                    <option value="todos" selected="yes">--Seleccionar--</option>\n\
                    <option value="1">Enero</option>\n\
                    <option value="2">Febrero</option>\n\
                    <option value="3">Marzo</option>\n\
                    <option value="4">Abril</option>\n\
                    <option value="5">Mayo</option>\n\
                    <option value="6">Junio</option>\n\
                    <option value="7">Julio</option>\n\
                    <option value="8">Agosto</option>\n\
                    <option value="9">Setiembre</option>\n\
                    <option value="10">Octubre</option>\n\
                    <option value="11">Noviembre</option>\n\
                    <option value="12">Diciembre</option>\n\
                  </select>\n\
                </td>\n\
                <th>Año:</th>\n\
                <td>\n\
                  <select id="añoInicio" title="Elegir el año de inicio\n(Sólo si se busca por meses)" name="añoInicio" tabindex="9" style="width:100%">\n\
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
                </td>\n\
                <th>Mes Final:</th>\n\
                <td>\n\
                  <select id="mesFin" name="mesFin" title="Elegir el mes de finalización\n(Sólo si se busca por meses)" tabindex="8" style="width:100%">\n\
                    <option value="todos" selected="yes">--Seleccionar--</option>\n\
                    <option value="1">Enero</option>\n\
                    <option value="2">Febrero</option>\n\
                    <option value="3">Marzo</option>\n\
                    <option value="4">Abril</option>\n\
                    <option value="5">Mayo</option>\n\
                    <option value="6">Junio</option>\n\
                    <option value="7">Julio</option>\n\
                    <option value="8">Agosto</option>\n\
                    <option value="9">Setiembre</option>\n\
                    <option value="10">Octubre</option>\n\
                    <option value="11">Noviembre</option>\n\
                    <option value="12">Diciembre</option>\n\
                  </select>\n\
                </td>\n\
                <th>Año:</th>\n\
                <td>\n\
                  <select id="añoFin" name="añoInicio" title="Elegir el año de finalización\n(Sólo si se busca por meses)" tabindex="9" style="width:100%">\n\
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
                  <input type="radio" name="criterioFecha" title="Elegir el período a buscar\nSeleccionar si se quieren TODOS los movimientos" value="todos" checked="checked">\n\
                </td>\n\
                <th>TODOS</th>';
    tr += '<tr>\n\
            <th colspan="2">Tipo:</th>\n\
            <td colspan="3">\n\
              <select id="tipo" name="tipo" title="Elegir el tipo de movimiento a buscar" style="width:100%">\n\
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
            <td colspan="5" class="pieTabla"><input type="button" class="btn btn-success" title="Realizar la gráfica" name="realizarGrafica" id="realizarGrafica" value="Consultar" align="center"></td>\n\
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
    $("#nombreGrafica").val("");
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
  var formuInicio = '<form name="exportarGraph" id="exportarGraph" target="_blank" action="generarGrafica.php" method="POST">';
  var formuFin = "</form>";
  var grafica = '<figure>\n\
                  <img src="graficar.php" id="grafiquita" width="750px" height="350px">\n\
                  <figcaption>Gr&aacute;fica con las estad&iacute;sticas</figcaption>\n\
                </figure>';
  var volver = '<a title="Volver a ESTADÍSTICAS" href="estadisticas.php">Volver</a>';
  mostrar += titulo;
  mostrar += formuInicio;
  mostrar += grafica;
  mostrar += formuFin;
  mostrar += '<br><br>';
  mostrar += volver;
  mostrar += '<br><br>';
  $(selector).html(mostrar);
}

/**
  \brief Función que se encarga de realizar la gráfica.
*/
function realizarGrafica(){
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
    var criterioFecha = $('input:radio[name=criterioFecha]:checked').val();
    
    var mensajeFecha = '';
    var inicio = '';
    var fin = '';
    var rangoFecha = '';
    var hoy = new Date();
    var tempMonth = parseInt(hoy.getMonth(), 10)+1;
    var tempDia = hoy.getDate();
    var tempAño = hoy.getUTCFullYear();
    if (tempMonth < 10){
      tempMonth = "0"+tempMonth;
    }
    if (tempDia < 10){
      tempDia = "0"+tempDia;
    }
    var meses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Setiembre", "Octubre", "Noviembre", "Diciembre"];
 
    var validado = true;
    
    switch (criterioFecha) {
      case "intervalo": var diaInicio = $("#diaInicio").val();
                        var diaFin = $("#diaFin").val();
                        if (diaInicio === ''){
                          diaInicio = "2017-09-01";
                        }
                        else {
                          diaInicio = $("#diaInicio").val();
                        }
                        if (diaFin === ''){
                          diaFin = hoy.getFullYear()+"-"+hoy.getUTCMonth()+1+"-"+hoy.getDate();
                        }
                        else {
                          diaFin = $("#diaFin").val();
                        }
                        var inicioDate = new Date(diaInicio+" 00:00:00");
                        var finDate = new Date(diaFin+" 23:59:59");
                        if (finDate < inicioDate) {
                          alert('ERROR. La fecha final NO puede ser anterior a la fecha inicial.\nPor favor verifique.');
                          $("#diaInicio").focus();
                          validado = false;
                        }
                        else {
                          var mesTemp = inicioDate.getUTCMonth()+1;
                          if (mesTemp < 10) {
                            mesTemp = "0"+mesTemp;
                          }
                          var diaTemp = inicioDate.getDate();
                          if (diaTemp < 10) {
                            diaTemp = "0"+diaTemp;
                          }
                          var dia1 = diaTemp+"/"+mesTemp.toString()+"/"+inicioDate.getUTCFullYear();
                          inicio = inicioDate.getUTCFullYear()+"-"+mesTemp.toString()+"-"+diaTemp;
                          
                          if (finDate > hoy){
                            var mesTemp1 = hoy.getUTCMonth()+1;
                            if (mesTemp1 < 10) {
                              mesTemp1 = "0"+mesTemp1;
                            }
                            var diaTemp1 = hoy.getDate();
                            if (diaTemp1 < 10) {
                              diaTemp1 = "0"+diaTemp1;
                            }
                            fin = hoy.getUTCFullYear()+"-"+mesTemp1.toString()+"-"+diaTemp1;
                            dia2 = diaTemp1+"/"+mesTemp1.toString()+"/"+hoy.getUTCFullYear();
                          }
                          else {
                            var mesTemp2 = finDate.getUTCMonth()+1;
                            if (mesTemp2 < 10) {
                              mesTemp2 = "0"+mesTemp2;
                            }
                            var diaTemp2 = finDate.getDate();
                            if (diaTemp2 < 10) {
                              diaTemp2 = "0"+diaTemp2;
                            }
                            dia2 = diaTemp2+"/"+mesTemp2.toString()+"/"+finDate.getUTCFullYear();
                            fin = finDate.getUTCFullYear()+"-"+mesTemp2.toString()+"-"+diaTemp2;
                          }
                          
                          if (inicio == fin) {
                            mensajeFecha = "del día "+dia1;
                          }
                          else {
                            mensajeFecha = "entre el "+dia1+" y el "+dia2;
                          }                      
                        }
                        rangoFecha = "(fecha >= '"+inicio + "') and (fecha <= '"+fin+"')";
                        break;
      case "mes": ///Recupero valores pasados:
                  var mesInicio = $("#mesInicio").val();
                  var añoInicio = parseInt($("#añoInicio").val(), 10);
                  var mesFin = $("#mesFin").val();
                  var añoFin = parseInt($("#añoFin").val(), 10);   
                  if (mesInicio === 'todos'){
                    mesInicio = 1;
                  }
                  else {
                    mesInicio = parseInt(mesInicio, 10);
                  }
                  if (mesFin === 'todos'){
                    mesFin = 12;
                  }
                  else {
                    mesFin = parseInt(mesFin, 10);
                  }
                  ///Instancio dos objetos tipo Date con las fechas inicial y final:
                  var finDate1 = new Date(añoFin,mesFin,0, 23,59,59);
                  var inicioDate1 = new Date(añoInicio+"-"+mesInicio+"-01 00:00:00");

                  var mes1 = '';
                  var mes2 = '';
                  var dia1 = '';
                  var dia2 = '';

                  var inicialMonth1 = parseInt(inicioDate1.getUTCMonth(), 10)+1;  
                  if (inicialMonth1 < 10){
                    mes1 = '0'+inicialMonth1.toString();
                  }
                  else {
                    mes1 = inicialMonth1.toString();
                  }
                  if (inicioDate1.getDate() < 10){
                    dia1 = '0'+inicioDate1.getDate();
                  }
                  else {
                    dia1 = inicioDate1.getDate();
                  }
                  inicio = inicioDate1.getUTCFullYear()+"-"+mes1+"-"+dia1;
                  
                  ///Chequeo que la fecha final pasada no sea posterior a hoy:
                  if (finDate1 > hoy){
                    fin = tempAño+"-"+tempMonth.toString()+"-"+tempDia;
                    añoFin = hoy.getUTCFullYear();
                    mesFin = hoy.getMonth()+1;
                    //dia2 = hoy.getDate()+"/"+hoy.getMonth()+1+"/"+hoy.getUTCFullYear();
                  }
                  else {
                    var finalMonth1 = parseInt(finDate1.getMonth(), 10)+1;
                    if (finalMonth1 < 10){
                      mes2 = '0'+finalMonth1.toString();
                    }
                    else {
                      mes2 = finalMonth1.toString();
                    }
                    if (finDate1.getDate() < 10){
                      dia2 = '0'+finDate1.getDate();
                    }
                    else {
                      dia2 = finDate1.getDate();
                    }
                    fin = finDate1.getFullYear()+"-"+mes2+"-"+dia2;
                  }
                       
                  ///Comienzo validación del rango elegido:
                  if (añoFin < añoInicio) {
                    alert('ERROR. El año final NO puede ser anterior al año inicial. \nPor favor verifique.');
                    $("#añoInicio").focus();
                    validado = false;
                  }
                  else {
                    ///Mismo año:
                    if (añoInicio === añoFin){
                      ///Mismo año y mes final anterior al inicial:
                      if (mesFin < mesInicio) {
                        alert('ERROR. El mes final NO puede ser anterior que el mes inicial.\nPor favor verifique.');
                        $("#mesInicio").focus();
                        validado = false;
                      }
                      else {
                        ///Mismo año y mismo mes, poner solo del mes y año tal:
                        if (mesFin === mesInicio) {
                          mensajeFecha = "de "+meses[mesInicio]+" de "+añoInicio;
                        }
                        ///Mismo año, pero meses distintos, entonces poner entre tal mes y tal mes del año tal:
                        else {
                          mensajeFecha = "entre "+meses[mesInicio]+" y "+meses[mesFin]+" de "+añoInicio;
                        }
                      }
                    }
                    ///Año final es necesariamente mayor al inicial por lo cual NO IMPORTAN los meses. Pongo rango completo:
                    else {
                      mensajeFecha = "entre "+meses[mesInicio]+"/"+añoInicio+" y "+meses[mesFin]+"/"+añoFin;
                    }
                    ///Sólo agrego un IF para el caso en que sea de enero a diciembre cosa que aparezca solo del año tal:
                    if ((añoInicio === añoFin)&&(mesInicio === 1)&&(mesFin === 12)){
                      mensajeFecha = "de "+añoInicio;
                    }
                    
                    rangoFecha = "(fecha >= '"+inicio + "') and (fecha <= '"+fin+"')";
                  }    
                  break;
      case "todos": var fin1 = tempAño+"-"+tempMonth+"-"+tempDia;
                    inicio = '2017-09-01';
                    rangoFecha = "(fecha >= '"+inicio + "') and (fecha <= '"+fin1+"')";
                    mensajeFecha = "entre "+meses[09]+"/"+"2017"+" y "+meses[hoy.getUTCMonth()+1]+"/"+hoy.getFullYear();
                    break;
      default: break;
    }
    ////*************************************************************************************************************************************************////////
    var tipoConsulta = '';

    var query = "select productos.nombre_plastico, movimientos.cantidad, movimientos.tipo, fecha from productos inner join movimientos on productos.idprod=movimientos.producto where productos.estado='activo' ";
    //alert("rango: "+rangoFecha+"\nquery:"+query);
    switch (radio) {
      case 'entidadMovimiento': if (entidadGrafica !== 'todos') {
                                  query += "and entidad='"+entidadGrafica+"' ";
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
                                    query += "and idProd="+idProd+' ';
                                  }
                                  tipoConsulta = 'del producto '+nombreSolo;
                                  break;
      default: break;
    }
    
    if (validado) {
      if (criterioFecha !== 'todos'){
        query += "and "+rangoFecha;
      }
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
      //alert(query);
      $.getJSON(url, {query: ""+query+""}).done(function(request){
        var totalDatos = request.rows;     
        if (totalDatos >= 1) {
          $("#consulta").val(query);
          $("#mensaje").val(mensajeConsulta);
          $("#fechaInicio").val(inicio);
          $("#fechaFin").val(fin);
          $("#hacerGrafica").val("yes");
          $('#graficar').attr('action', 'estadisticas.php?g=1');//alert("inicial: "+$("#fechaInicio").val()+"\nfin: "+$("#fechaFin").val());
          $("#graficar").submit();
        }
        else {
          alert("No existen registros que coincidan con esos parámetros.");
        }
      }); 
    }  
  }
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
    //parametros = unescape(parametros);
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
                                          var temp5 = temp1[3].split('=');
                                          var h = temp2[1]; 
                                          var tipo = decodeURI(temp4[1]);
                                          var fecha = decodeURI(temp5[1]);
                                          var idprod = parseInt(temp3[1], 10);
                                          setTimeout(function(){cargarMovimiento("#main-content", h, idprod, tipo, fecha)}, 100);                                          
                                        }
                                        else {
                                          setTimeout(function(){cargarMovimiento("#main-content", "", "-1", "", "")}, 100);
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
                                                  setTimeout(function(){cargarEditarMovimiento(idmov, "#main-content")}, 30);
                                                }
                                                else {
                                                  setTimeout(function(){cargarEditarMovimiento(-1, "#main-content")}, 1000);
                                                }  
                                              break;                                       
    default: break;
  }  

/*****************************************************************************************************************************
/// Comienzan las funciones que manejan los eventos relacionados al RESALTADO de los input.
******************************************************************************************************************************
*/
      
///Disparar funcion cuando algún elemento de la clase agrandar reciba el foco.
///Se usa para resaltar el elemento seleccionado.
$(document).on("focus", ".agrandar", function (){
  $(this).css("font-size", 24);
  $(this).css("background-color", "#e7f128");
  $(this).css("font-weight", "bolder");
  $(this).css("color", "red");
  $(this).css("height", "100%");
  //$(this).css("max-width", "100%");
  //$(this).parent().prev().prev().children().prop("checked", true);
});

///Disparar funcion cuando algún elemento de la clase agrandar pierda el foco.
///Se usa para volver al estado "normal" el elemento que dejó de estar seleccionado.
$(document).on("blur", ".agrandar", function (){
  $(this).css("font-size", "inherit");
  $(this).css("background-color", "#ffffff");
  $(this).css("font-weight", "inherit");
  $(this).css("color", "inherit");
});
  
/*****************************************************************************************************************************
/// ***************************************************** FIN RESALTADO ******************************************************
******************************************************************************************************************************
*/



/*****************************************************************************************************************************
/// Comienzan las funciones que manejan los eventos relacionados a los MOVIMIENTOS como ser creación, edición y eliminación.
******************************************************************************************************************************
*/

///Disparar funcion al cambiar el elemento elegido en el select con las sugerencias para los productos.
///Cambia el color de fondo para resaltarlo, carga un snapshot del plástico si está disponible, y muestra
///el stock actual.
$(document).on("change focusin", "#hint", function (e){
  var rutaFoto = 'images/snapshots/';
  var nombreFoto = $(this).find('option:selected').attr("name");
  var prod = $(this).find('option:selected').val();
  $(this).css('background-color', '#ffffff');
  //$(this).find('option:selected').css('background-color', '#ffffff');
  
  $("#snapshot").remove();
  $("#stock").remove();
  $("#ultimoMov").remove();
  $("#comentHint").remove();
  $(".popover").remove();
  var stock = $("#hint").find('option:selected').attr("stock");
  var comentarios = $("#hint").find('option:selected').attr("comentarios");
  var alarma1 = $("#hint").find('option:selected').attr("alarma1");
  alarma1 = parseInt(alarma1, 10);
  var alarma2 = $("#hint").find('option:selected').attr("alarma2");
  alarma2 = parseInt(alarma2, 10);
  var ultimoMovimiento = $("#hint").find('option:selected').attr("ultimomov");
  if ((ultimoMovimiento === undefined) || (ultimoMovimiento === null)||(ultimoMovimiento === "null")){
    ultimoMovimiento = 'NO HAY';
  }
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
  mostrar += '<p id="ultimoMov" name="ulitmoMov">Último Movimiento: <font class="'+resaltado+'" style="font-size:1.2em">'+ultimoMovimiento+'</font></p>';
  if ((comentarios !== '')&&(comentarios !== "null")&&(comentarios !== ' ')&&(comentarios !== undefined)){
    mostrar += '<p id="comentHint" name="comentHint">Comentarios: <font class="alarma1" style="font-size:1.4em">'+comentarios+'</font></p>';
  }
  //$(this).css('background-color', '#efe473');
  $(this).css('background-color', '#9db7ef');
  //$(this).find('option:selected').css('background-color', '#79ea52');
  $("#hint").after(mostrar);
  $(this).parent().prev().prev().children().prop("checked", true);
  //setTimeout(function(){mostrarHistorial(prod)}, 100);
  mostrarHistorial(prod);
  });
  
/// ****** COMENTO EVENTO CLICK POR SER REDUNDANTE CON EL CHANGE ***************************  
/////Disparar función al hacer CLICK en alguna de las option del select #hint.
//$(document).on("click", "#hint", function (){
//  $("#historial").remove();
//  var prod = $(this).val();
//  if ($("#historial").length > 0){
//    $("#historial").remove();
//  }
//  setTimeout(function(){mostrarHistorial(prod)}, 200);       
//});
/// **************************** FIN EVENTO CLICK ******************************************* 
  
///Disparar función al hacer ENTER sobre alguna de las OPTION del select HINT.
///Básicamente, la idea es que al presionar ENTER, se ejecute la opción por defecto cosa de ahorrar tiempo.  
///En el caso de ser llamado desde MOVIMIENTOS, pasa al foco al campo CANTIDAD. Mientras que en el caso de BUSQUEDAS hace el SUBMIT.
///Además, si la tecla presionada es el TAB, se pasa el foco al siguiente elemento.
$(document).on("keydown", "#hint", function (e){
  var keyCode = e.keyCode || e.which;
  var temp = "#"+$(this).prev().attr("id");
  
  switch (temp) {
    case "#producto": var sel = $(this).find('option:selected').val();
                      var productos = new Array();
                      $('option:selected',this).each(function() {
                        productos.push($(this).val());
                      });
                      var totalProductos = productos.length;
                      if (totalProductos > 1){
                        alert("Los movimientos hay que agregarlos por cada producto. Por favor verifique.");
                      }
                      else {
                        if ((sel !== 'NADA') && ((keyCode === 1) || (keyCode === 13) || (keyCode === 9))) {
                          e.preventDefault();
                          //$("#comentarios").focus();
                          $("#cantidad").focus();
                        }
                      }
                      break;
    case "#productoStock":  var productosStock = new Array();
                            $('option:selected',this).each(function() {
                              productosStock.push($(this).val());
                            });
                            var totalProductosStock = productosStock.length;
                            var seguir = true;  
                            for (var i in productosStock) {
                              if (productosStock[i] === 'NADA'){
                                seguir = false;
                              }
                            }
                            if (e.which === 13){
                              if (seguir){
                                if (totalProductosStock > limiteSeleccion){
                                  alert("Se superó el máximo de "+limiteSeleccion+" opciones elegidas. Por favor verifique.");
                                }
                                else {
                                  realizarBusqueda();
                                }
                              }
                              else {
                                alert('--Seleccionar-- NO debe de estar marcado. Por favor verifique.');
                              }
                            }
                            if (keyCode == 9) {
                              e.preventDefault();    
                              $("#entidadMovimiento").focus();
                            }
                            break;
    case "#productoMovimiento": var productosMovimiento = new Array();
                                $('option:selected',this).each(function() {
                                  productosMovimiento.push($(this).val());
                                });
                                var totalProductosMovimiento = productosMovimiento.length;
                                var seguir = true;
                                //alert(entidadesStock);  
                                for (var i in productosMovimiento) {
                                  if (productosMovimiento[i] === 'NADA'){
                                    seguir = false;
                                  }
                                }
                                if (e.which === 13){
                                  if (seguir){
                                    if (totalProductosMovimiento > limiteSeleccion){
                                      alert("Se superó el máximo de "+limiteSeleccion+" opciones elegidas. Por favor verifique.");
                                    }
                                    else {
                                      realizarBusqueda();
                                    }
                                  }
                                  else {
                                    alert('--Seleccionar-- NO debe de estar marcado. Por favor verifique.');
                                  }
                                }   
                                if (keyCode == 9) {
                                  e.preventDefault();    
                                  $("#inicio").focus();
                                }
                                break;
    case "#productoGrafica":  var sel = $("#hint").find('option:selected').val(); 
                              if (((keyCode === 1) || (keyCode === 13))){
                                if (sel !== 'NADA') {
                                  realizarGrafica();
                                }
                                else {
                                  alert('Se debe elegir un producto del cual consultar las estadísticas. Por favor verifique.');
                                  $("#hint").focus();
                                }
                              }
                              if (keyCode == 9) {
                                e.preventDefault();    
                                $("#diaInicio").focus();
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
///En el caso en que me encuentre en el form de movimientos, debe pasar al campo CANTIDAD en lugar del hacer el submit.
$(document).on("keypress", "#comentarios", function(e) {
  var commentName = $(this).attr("name");

  switch (commentName) {
    case "comMov":  if(e.which === 13) {
                      $("#cantidad").focus();
                    }                   
                    break;
    case "comEditMov":  if(e.which === 13) {
                          actualizarMovimiento();
                        }
                        break;
    case "comProd": if(e.which === 13) {
                      
                    }  
                    break;
    default: break;
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

/*****************************************************************************************************************************
/// ***************************************************** FIN MOVIMIENTOS ****************************************************
******************************************************************************************************************************
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
  $("#ultimoMov").remove();
  $("#historial").remove();
  $("#stock").removeClass('alarma1');
  $("#stock").removeClass('alarma2');
  $("#stock").removeClass('resaltado');
  
  var idProd = $(this).find('option:selected').val();
  var stock = $("#hintProd").find('option:selected').attr("stock");
  var alarma1 = $("#hintProd").find('option:selected').attr("alarma1");
  alarma1 = parseInt(alarma1, 10);
  var alarma2 = $("#hintProd").find('option:selected').attr("alarma2");
  alarma2 = parseInt(alarma2, 10);
  var ultimoMovimiento = $("#hintProd").find('option:selected').attr("ultimomov");
  if ((ultimoMovimiento === undefined) || (ultimoMovimiento === null)||(ultimoMovimiento === "null")){
    ultimoMovimiento = 'NO HAY';
  }
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
  mostrar += '<p id="ultimoMov" name="ulitmoMov">Último Movimiento: <font class="'+resaltado+'" style="font-size:1.2em">'+ultimoMovimiento+'</font></p>';
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
  var timestamp = Math.round(Date.now() / 1000);
      
  if(timestamp - $("#timestampSesion").val() > $("#duracionSesion").val()) {
    alert('Su sesión expiró. Por favor vuelva loguearse.'); 
    window.location.href = "../controlstock/index.php";
  }
  else {
    verificarSesion();
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
        ///Por ahora se anula el pedido de confirmación y se hace el update igual a pedido de Diego:
        //var confirmar = confirm('¿Confirma la modificación del producto con los siguientes datos?\n\nEntidad: '+entidad+'\nNombre: '+nombre+'\nCódigo Emsa: '+codigo_emsa+'\nCódigo Origen: '+codigo_origen+'\nContacto: '+contacto+'\nSnapshot: '+nombreFoto+'\nBin: '+bin+'\nAlarma1: '+alarma1+'\nAlarma2: '+alarma2+'\nComentarios: '+comentarios+"\n?");
        var confirmar = true;
        if (confirmar) {
          var url = "data/updateQuery.php";
          var query = "update productos set nombre_plastico= '"+nombre+"', entidad = '"+entidad+"', codigo_emsa = '"+codigo_emsa+"', codigo_origen = '"+codigo_origen+"', contacto = '"+contacto+"', snapshot = '"+nombreFoto+"', bin = '"+bin+"', alarma1 = "+alarma1+", alarma2 = "+alarma2+", comentarios = '"+comentarios+"' where idprod = "+idProducto;
          var log = "SI";
          //alert(query);
          $.getJSON(url, {query: ""+query+"", log: log}).done(function(request) {
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
  }
});

///Disparar función al hacer enter estando en alguno de los input de la edición del Producto
///Esto hace que se pase el foco al siguiente input del form para ahorrar tiempo.
$(document).on("keypress", "#productUpdate input", function(e) {
  //alert($(this).attr("name"));
  if (e.which === 13) { 
    var tabindex = $(this).attr('tabindex');
    tabindex++;
    $('[tabindex=' + tabindex + ']').focus();
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
      var log = "SI";
      
      $.getJSON(url, {query: ""+query+"", log: log}).done(function(request) {
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
  var timestamp = Math.round(Date.now() / 1000);
      
  if(timestamp - $("#timestampSesion").val() > $("#duracionSesion").val()) {
    alert('Su sesión expiró. Por favor vuelva loguearse.'); 
    window.location.href = "../controlstock/index.php";
  }
  else {
    verificarSesion();
    var nombre = $(this).val();
    if (nombre === 'EDITAR') {
      habilitarProducto();
    }
    else {
      inhabilitarProducto();
    }
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
      var log = "SI";
      
      var confirmar = confirm("¿Confirma que desea agregar el producto con los siguientes datos: \n\nEntidad: "+entidad+"\nNombre: "+nombre+"\nCódigo Emsa: "+codigo_emsa+"\nCódigo Origen: "+codigo_origen+"\nContacto: "+contacto+"\nSnapshot: "+nombreFoto+"\nBin: "+bin+"\nStock Inicial: "+stock+"\nAlarma1: "+alarma1+"\nAlarma2: "+alarma2+"\nComentarios: "+comentarios+"\n?");
      if (confirmar) {
        $.getJSON(url, {query: ""+query+"", log: log}).done(function(request){
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

/*****************************************************************************************************************************
/// ***************************************************** FIN PRODUCTOS ******************************************************
******************************************************************************************************************************
*/



/*****************************************************************************************************************************
/// Comienzan las funciones que manejan los eventos relacionados a los USUARIOS como ser creación, edición y eliminación.
******************************************************************************************************************************
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
            var query = 'select max(idusuarios) as ultimoUser from usuarios limit 1';
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

///Disparar función al hacer click en el link con el nombre del usuario que está logueado.
///Esto hace que se abra el modal para cambiar la contraseña.
$(document).on("click", "#user", function(){
  $("#modalPwd").modal("show");
});

///Disparar función al abrirse el modal para cambiar la contraseña.
///Lo único que hace es limpiar el form para poder ingresar los nuevos datos.
$(document).on("shown.bs.modal", "#modalPwd", function() {
  $("#pw1").val('');
  $("#pw2").val('');
  $("#pw1").attr("autofocus", true);
  $("#pw1").focus();
});

///Disparar función al hacer click en el botón de ACTUALIZAR que está en el MODAL.
///Primero valida que la info ingresada sea válida (pwd no nulos e iguales entre sí), y luego 
///ejecuta la consulta para cambiar la contraseña.
$(document).on("click", "#btnModal", function(){
  actualizarUser();
});

///Disparar función al hacer ENTER estando en el elemento pw1 del MODAL.
///Esto hace que se pase el foco al siguiente input del MODAL (pw2) cosa de ahorrar tiempo.
$(document).on("keypress", "#pw1", function(e) {
  if(e.which === 13) {
    $("#pw2").focus();
  }  
});

///Disparar función al hacer ENTER estando en el elemento pw1 del MODAL.
///Esto hace que se pase el foco al siguiente input del MODAL (pw2) cosa de ahorrar tiempo.
$(document).on("keypress", "#pw2", function(e) {
  if(e.which === 13) {
    actualizarUser();
  }  
});

///Disparar función al hacer enter estando en el elemento nombreUsuario.
///Básicamente, la idea es pasar el foco al elemento password cosa de ahorrar tiempo en el ingreso.
$(document).on("keypress", "#nombreUsuario", function(e) {
  if(e.which === 13) {
    e.preventDefault();
    $("#password").focus();
  }  
});
    
/*****************************************************************************************************************************
/// **************************************************** FIN USUARIOS ********************************************************
******************************************************************************************************************************
*/



/*****************************************************************************************************************************
/// Comienzan las funciones que manejan los eventos relacionados a las BÚSQUEDAS como ser creación, edición y eliminación.
******************************************************************************************************************************
*/

///Disparar función al hacer enter estando en el elemento Producto.
///Básicamente, la idea es pasar el foco al select hint cosa de ahorrar tiempo en el ingreso.
$(document).on("keypress", "#productoStock, #productoMovimiento, #productoGrafica", function(e) { 
  if(e.which === 13) {
    //alert('enter');
    $("#hint").focus();
  }
});      
   
///Disparar función al presionar el TAB estando en alguno de los input del form para las búsquedas.
///Es un complemento del anterior que detecta primero si se presionó el TAB. En ese caso, chequea primero
///que haya alguna sugerencia en HINT, y si la hay pasa el foco al select cosa de ahorrar tiempo.
$(document).on("keydown", "#parametros input, #movimiento input", function(e) { 
  var keyCode = e.keyCode ? e.keyCode : e.which ? e.which : e.charCode;
  var sel = $(this).attr("id");
  
  if ((sel === "productoStock")||(sel === "productoMovimiento")||(sel === "producto")){
    if (keyCode == 9) { 
      e.preventDefault(); 
      var length = $('#hint> option').length;
      if (length > 0) {
        $("#hint").focus();
      }
      else {
        switch (sel) {
          case "producto":  //$("#comentarios").focus();
                            $("#cantidad").focus();
                            break;
          case "productoStock":  $("#entidadMovimiento").focus();
                            break;
          case "productoMovimiento":  $("#inicio").focus();
                            break;
                          default: break;                
        }
      }
    } 
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
  //var sel = $(temp).find('option:selected').val();
  var entidades = new Array();
  $('option:selected',this).each(function() {
    entidades.push($(this).val());
  });
  var totalEntidades = entidades.length;
  var todos = false;
  for (var i in entidades) {
    if (entidades[i] === 'todos'){
      todos = true;
    }
  }

  if ((e.which === 1) || (e.which === 13)) {
    if (todos && (totalEntidades > 1)){
      alert('No se puede consultar "TODOS" junto con otras entidades. Por favor verifique.');
    }
    else {
      if (totalEntidades > limiteSeleccion){
        alert("Se superó el máximo de "+limiteSeleccion+" opciones elegidas. Por favor verifique.");
      }
      else {
        realizarBusqueda();
      }
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
  /// 4- movimientos por entidad.
  /// 5- movimientos por producto.
  var id = $(this).attr("id");
  var indice = $(this).attr("indice");
  $("input[name='indice']" ).val(indice);
  var x = $("#x").val();
  var query = $("#query_"+indice+"").val();
  var consultaCSV = $("#consultaCSV_"+indice+"").val();
  var largos = $("#largos").val();
  var campos = $("#campos").val();
  var mostrar = $("#mostrar").val();
  var tipoConsulta = $("#tipoConsulta_"+indice+"").val();
  //alert(query);
  switch (id) {
    case "1": var entidad = $("#entidad_"+indice+"").val();
              break;
    case "2": var idProd = $("#idProd").val();
              var nombreProducto = $("#nombreProducto").val();
              break;
    case "3": break;
    case "4": var criterioFecha = $("#criterioFecha").val();
              var entidad = $("#entidad").val();
              var tipo = $("#tipo").val();
              var usuario = $("#usuario").val();
              break;
    case "5": var criterioFecha = $("#criterioFecha").val();
              var idProd = $("#idProd").val();
              var nombreProducto = $("#nombreProducto").val();
              var tipo = $("#tipo").val();
              var usuario = $("#usuario").val();
              
    default: break;
  }
  
  switch (criterioFecha) {
    case "intervalo": var inicio = $("#inicio").val();
                      var fin = $("#fin").val();
                      break;
    case "mes": var mes = $("#mes").val();
                var año = $("#año").val();                 
                break;
    case "todos": break;
    default: break;
  }
  
  var param = "id:"+id+"&x:"+x+"&largos:"+largos+"&campos:"+campos+"&query:"+query+"&consultaCSV:"+consultaCSV+"&mostrar:"+mostrar+"&tipoConsulta:"+tipoConsulta;
  
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
    case "1": param += '&entidad:'+entidad;//+'&nombreProducto:'+nombreProducto;
              break;
    case "2": param += '&idProd:'+idProd+'&nombreProducto:'+nombreProducto;
              break;
    case "3": break;
    case "4": param += '&entidad:'+entidad+'&tipo:'+tipo+'&usuario:'+usuario;
              switch (criterioFecha){
                case "intervalo": param += '&inicio:'+inicio+'&fin:'+fin;
                                  break;
                case "mes": param += '&mes:'+mes+'&año:'+año;
                            break;
                case "todos": break;
                default: break;
              }
              break;
    case "5": param += '&idProd:'+idProd+'&tipo:'+tipo+'&usuario:'+usuario+'&nombreProducto:'+nombreProducto;
              switch (criterioFecha){
                case "intervalo": param += '&inicio:'+inicio+'&fin:'+fin;
                                  break;
                case "mes": param += '&mes:'+mes+'&año:'+año;
                            break;
                case "todos": break;
                default: break;
              }            
              break;
    default: break;
  }
  //alert(query);
  //$("#param").val(param);
  $("#resultadoBusqueda_"+indice).submit();
});//*** fin del click .exportar ***

///Disparar función al hacer click en alguno de los links con las PÁGINAS de los resultados.
///Básicamente arma la consulta para mostrar la pagina solicitada y llama a la función para ejecutarla.
$(document).on("click", ".paginate", function (){
  var page = parseInt($(this).attr('data'), 10);
  $(".nav-link.active").attr("activepage", ""+page+"");
  var indice = $(this).attr('i');
  var totalPaginas = parseInt($("#totalPaginas_"+indice).val(), 10);
  var totalRegistros = parseInt($("#totalRegistros_"+indice).val(), 10);
  var totalPlasticos = parseInt($("#totalPlasticos_"+indice).val(), 10);
  var offset = (page-1)*tamPagina;
  var primerRegistro = offset+1;
  var ultimoRegistro = offset + tamPagina;
  if (ultimoRegistro > totalRegistros){
    ultimoRegistro = totalRegistros;
  }
  var rango = "<h5 id='rango_"+indice+"' class='rango'>(P&aacute;gina "+page+": registros del "+primerRegistro+" al "+ultimoRegistro+")</h4>";
  
  var limite = tamPagina;
  if (page < (totalPaginas-1)){
    limite = tamPagina + 1;
  }
  
  var max = tamPagina;
  //alert("pagina: "+page+"\ntotalPaginas: "+totalPaginas+"\noffset: "+offset+"\nlimite: "+limite);
  var query = $("#query_"+indice).val();
  
  if (page === totalPaginas){
    max = totalRegistros%tamPagina;
    if (max === 0) {
      limite = tamPagina;
      max = tamPagina;
    }
    else {
      limite = max;
    }
  }
  
  query += " limit "+limite+" offset "+offset;

 //alert(query);
  var idTipo = $("#idTipo").val();
  var radio = '';
  var entidad = '';
  var todos = false;
  switch (idTipo){
    case "1": radio = 'entidadStock';
              entidad = $("#entidad_"+indice+"").val();
              var subtotales = JSON.parse($("#subtotales_"+indice).val());
              break;
    case "2": radio = 'productoStock';
              break;
    case "3": radio = 'totalStock';
              var subtotales = JSON.parse($("#subtotales_"+indice).val());
              break;
    case "4": radio = 'entidadMovimiento';
              entidad = $("#entidad_"+indice+"").val();
              var subtotales = JSON.parse($("#subtotales_"+indice).val());
              break;
    case "5": radio = 'productoMovimiento';
              break;
    default: break;
  }
  if (entidad === 'todos'){
    todos = true;
  }
  
  var url = "data/selectQuery.php";
  $.getJSON(url, {query: ""+query+""}).done(function(request){
    var datos = request.resultado;

    var parcial = true;
    switch (radio){
      case "entidadStock":  if (page < totalPaginas){
                              parcial = true;
                            }
                            break;
      case "productoStock": radio = 'productoStock';
                            break;
      case "totalStock":  radio = 'totalStock';
                          if (page < totalPaginas){
                            parcial = true;
                          }
                          break;
      case "entidadMovimiento": radio = 'entidadMovimiento';
                                entidad = $("#entidad_"+indice+"").val();
                                //alert(datos[limite-1]['idprod']+"\n"+datos[limite-2]['idprod']);
                                if (((datos[limite-1]['idprod']) !== (datos[limite-2]['idprod']))&&(page !== totalPaginas)){
                                  parcial = false;
                                }
                                break;
      case "procutoMovimiento": radio = 'productoMovimiento';
                                break;
      default: break;
    }
   
    //alert('antes de llamar a mostrar tabla: \nstock: '+subtotales["stock"]+"\nretiros: "+subtotales["retiros"]+"\nrenos: "+subtotales["renovaciones"]+"\ndestrucciones: "+subtotales["destrucciones"]+"\ningresos: "+subtotales["ingresos"]+"\nconsumos: "+subtotales["consumos"]);
    var tabla = mostrarTabla(radio, datos, indice, todos, primerRegistro, parcial, subtotales, max, totalPlasticos);
    
    $("#resultados_"+indice+"").remove();
    if ($("#detallesProducto_"+indice+"").length > 0){
      $("#detallesProducto_"+indice+"").remove();
    }
    $("#rango_"+indice+"").remove();
    $("#resultadoBusqueda_"+indice+"").prepend(rango);
    $("#resultadoBusqueda_"+indice+"").append(tabla);
    
    if (page !== 1) {
      var anterior = '<li><a class="paginate anterior" i='+indice+' data="'+(page-1)+'">Anterior</a></li>';
      $(".pagination[indice='"+indice+"'] li .anterior").remove();
      $(".pagination[indice='"+indice+"'] ul").prepend(anterior); 
    }
    else {
      $(".pagination[indice='"+indice+"'] li .anterior").remove();
      //$(".pagination li a[data='1']").addClass('pageActive');
    }
    
    $(".pagination[indice='"+indice+"'] li a").each(function (){
      var indLi = parseInt($(this).attr('data'), 10);
      if (page === indLi){
        $(this).addClass('pageActive');   
      }
      else {
        $(this).removeClass('pageActive');
      }
      if (page !== totalPaginas){
        var siguiente = '<li><a class="paginate siguiente" i='+indice+' data="'+(page+1)+'">Siguiente</a></li>';
        $(".pagination[indice='"+indice+"'] li .siguiente").remove();
        $(".pagination[indice='"+indice+"'] ul").append(siguiente);
      }
      else {
        $(".pagination[indice='"+indice+"'] li .siguiente").remove();
      }
    });  
    $('html, body').animate({scrollTop:136}, '10');
  });  
});


$(document).on("shown.bs.tab", "a[data-toggle='pill']",  function () {
  /*var page = $(".nav-link.active").attr("activepage");
  var indice = $(".tab-pane.active").attr("indice");
  var totalPaginas = parseInt($("#totalPaginas_"+indice).val(), 10);
  alert("indice: "+indice+"\nPagina: "+page+"\nTotalPaginas: "+totalPaginas);
  if (page !== 1) {
    var anterior = '<li><a class="paginate anterior" i='+indice+' data="'+(page-1)+'">Anterior</a></li>';
    $(".pagination[indice='"+indice+"'] li .anterior").remove();
    $(".pagination[indice='"+indice+"'] ul").prepend(anterior); 
  }
  else {
    $(".pagination[indice='"+indice+"'] li .anterior").remove();
    //$(".pagination li a[data='1']").addClass('pageActive');
  }

  $(".pagination[indice='"+indice+"'] li a").each(function (){
    var indLi = parseInt($(this).attr('data'), 10);
    if (page === indLi){
      $(this).addClass('pageActive');   
    }
    else {
      $(this).removeClass('pageActive');
    }
    if (page !== totalPaginas){
      var siguiente = '<li><a class="paginate siguiente" i='+indice+' data="'+(page+1)+'">Siguiente</a></li>';
      $(".pagination[indice='"+indice+"'] li .siguiente").remove();
      $(".pagination[indice='"+indice+"'] ul").append(siguiente);
    }
    else {
      $(".pagination[indice='"+indice+"'] li .siguiente").remove();
    }
  }); */
});

/*****************************************************************************************************************************
/// *************************************************** FIN BÚSQUEDAS ********************************************************
******************************************************************************************************************************
*/



/*****************************************************************************************************************************
/// Comienzan las funciones que manejan los eventos relacionados a las GRAFICAS
******************************************************************************************************************************
*/

///Disparar función al hacer click en el botón CONSULTAR del form graficar
///Básicamente, se llama a la función realizarGrafica()
$(document).on("click", "#realizarGrafica", function (){
  realizarGrafica();
});

/*****************************************************************************************************************************
/// *************************************************** FIN GRAFICAS *********************************************************
******************************************************************************************************************************
*/



/*****************************************************************************************************************************
/// Comienzan las funciones que manejan el DESPLAZAMIENTO dentro de la página
******************************************************************************************************************************
*/

///Función que muestra/oculta las flechas para subir y bajar la página según el scroll:
$(window).scroll(function() {
//alert('en el scroll');
  if ($(this).scrollTop() > 80) {
    $('.arrow').fadeIn(50);
  } else {
    $('.arrow').fadeOut(400);
  }
});

///Función que desplaza el foco hacia el final de la página:
$(document).on("click", ".arrow-bottom", function() {
  //event.preventDefault();
  $('html, body').animate({scrollTop:$(document).height()}, '1000');
        return false;
});

///Función que desplaza el foco hacia el comienzo de la página:
$(document).on("click", ".arrow-top", function() {
  //event.preventDefault();
  $('html, body').animate({scrollTop:136}, '1000');
  return false;
});

/*****************************************************************************************************************************
/// *************************************************** FIN DESPLAZAMIENTO ***************************************************
******************************************************************************************************************************
*/

}

/**
 * \brief Función que envuelve todos los eventos JQUERY con sus respectivos handlers.
 */
$(document).on("ready", todo());//*** fin del ready ***