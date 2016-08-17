/* Funciones de producto */
$(document).ready(function(){

	$("#fecha_inicio").datepicker({
		dateFormat: "yy-mm-dd"
	});

	$("#fecha_fin").datepicker({
		dateFormat: "yy-mm-dd"
	});

	$("#fecha_inicio,#fecha_fin").on("click", function(){
		$(this).val("");
	});

	$("#buscar").on("click", function(e){
		var id_almacen = $("#almacen").val();
		var limit_m = $("#registros").val();
		var offset_m = 0;
		var tipo_m = $("#tipo_m").val();
		var folio_m = $("#folio").val().trim();
		var fecha_i = $("#fecha_inicio").val().trim();
		var fecha_f = $("#fecha_fin").val().trim();

		if(id_almacen > 0){
			$.ajax({
			    // la URL para la petición
			    url : "obtener_movimientos",
			 
			    // especifica si será una petición POST o GET
			    type : "POST",

			    // envia los valores del form
			    data : {
			    	almacen : id_almacen,
			    	limit : limit_m,
			    	offset : offset_m,
			    	tipo_movimiento : tipo_m,
			    	folio : folio_m,
			    	fecha_inicio : fecha_i,
			    	fecha_fin : fecha_f
			    },

			    //especifica el tipo de dato que espera recibir
			    dataType: 'json',

			    beforeSend : function(xhr){
			    	$("#tabla_movimientos").find("tbody").html('<tr><td colspan="9" align="center"><img src="/inventarios/assets/img/cargando.gif" /></td></tr>');
			    },

			    // código a ejecutar si la petición es satisfactoria;
			    // la respuesta es pasada como argumento a la función
			    success : function(movimientos) {
			    	$("#tabla_movimientos").find("tbody").html("");
			    	crear_tr_mov(movimientos, 0);
			    },
			 
			    // código a ejecutar si la petición falla;
			    // son pasados como argumentos a la función
			    // el objeto de la petición en crudo y código de estatus de la petición
			    error : function(xhr, status) {
			        bootbox.alert('Disculpe, existió un problema');
			    }
			});
		}else{
			bootbox.alert("Debes seleccionar un almacén para realizar la búsqueda de movimientos.");
		}
	});
});

function crear_tr_mov(movimientos, inicio){
	var tr_html = "";
	var num = inicio;

	for(var i = 0 ; i < movimientos.length ; i++){
		var confirmacion = "";
		var color_status = "#000";
		var dis_confirmacion = "false";
		switch(movimientos[i].confirmacion){
			case "0":
		        confirmacion = "Sin confirmar";
		        break;
		    case "1":
		        confirmacion = "Confirmado";
		        color_status = "#419641"
		        break;
		    case "-1":
		        confirmacion = "Cancelado";
		        color_status = "#c12e2a";
		        break;
		    default:
		        confirmacion = "Sin status";
		}

		var disabled = "";
		if(movimientos[i].id_tipo_movimiento == "1" || movimientos[i].id_tipo_movimiento == "3" || movimientos[i].id_tipo_movimiento == "7" || movimientos[i].id_tipo_movimiento == "8" || movimientos[i].id_tipo_movimiento == "9"){
			disabled = "disabled";
		}

		num++;
		tr_html += "<tr class='text-center' id='movimiento_" + movimientos[i].id_movimiento + "'>";
		tr_html +=		"<td class='number th-blue'>" + num + "</td>";
		tr_html +=		"<td class='tipo_movimiento'>" + movimientos[i].tipo_movimiento + "</td>";
		tr_html +=		"<td class='folio'>" + movimientos[i].folio + "</td>";
		tr_html +=		"<td class='almacen'>" + movimientos[i].almacen + "</td>";
		tr_html +=		"<td class='fecha_hora'>" + movimientos[i].fecha + "</td>";
		tr_html +=		"<td class='cantidad'>" + movimientos[i].cantidad + "</td>";
		tr_html +=		"<td class='precio'>" + movimientos[i].precio + "</td>";
		tr_html +=		"<td class='estatus' style='color:" + color_status + "'>" + confirmacion + "</td>";
		tr_html +=		"<td class='detalles'>";
		tr_html += 			"<button type='button' class='btn btn-info btn-sm' onclick='ver_detalles(this, " + movimientos[i].id_movimiento + ");' >";
		tr_html += 				"<span class='glyphicon glyphicon-list' aria-hidden='true'></span> Ver";
		tr_html += 			"</button>";
		tr_html += 		"</td>";
		tr_html +=		"<td class='cancelar'>";
		tr_html += 			"<button type='button' class='btn btn-danger btn-sm' onclick='cancelar(this, " + movimientos[i].id_movimiento + ");' " + disabled + ">";
		tr_html += 				"<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>";
		tr_html += 			"</button>"
		tr_html += 		"</td>";
		tr_html += "</tr>";
	}

	$("#tabla_movimientos").find("tbody").prepend(tr_html);
}

function ver_detalles(obj_button, id_movimiento){
	var titulo_modal = $(obj_button).parent().parent().find(".tipo_movimiento").text() + ' - ' + $(obj_button).parent().parent().find(".folio").text();

	$(".modal-title").text(titulo_modal);

	$.ajax({
	    // la URL para la petición
	    url : "obtener_detalles_movimiento",
	 
	    // especifica si será una petición POST o GET
	    type : "POST",

	    // envia los valores del form
	    data : {
	    	movimiento : id_movimiento
	    },

	    //especifica el tipo de dato que espera recibir
	    dataType: 'json',

	    beforeSend : function(xhr){
	    	$("#tabla_detalles").find("tbody").html('<tr><td colspan="9" align="center"><img src="/inventarios/assets/img/cargando.gif" /></td></tr>');
	    },

	    // código a ejecutar si la petición es satisfactoria;
	    // la respuesta es pasada como argumento a la función
	    success : function(detalles_movimiento) {
	    	$("#tabla_detalles").find("tbody").html("");
	    	crear_tr_det_mov(detalles_movimiento);
	    },
	 
	    // código a ejecutar si la petición falla;
	    // son pasados como argumentos a la función
	    // el objeto de la petición en crudo y código de estatus de la petición
	    error : function(xhr, status) {
	        bootbox.alert('Disculpe, existió un problema');
	    }
	});

	$('#info').modal();
}

function crear_tr_det_mov(detalles_movimiento){
	var tr_html = "";
	var total_c = 0;
	var total_p = 0;

	for(var i = 0 ; i < detalles_movimiento.length ; i++){
		tr_html += "<tr class='text-center' id='movimiento_" + detalles_movimiento[i].id_producto + "'>";
		tr_html +=		"<td style='border: hidden; background-color: white;'></td>";
		tr_html +=		"<td class='cantidad_d'>" + detalles_movimiento[i].cantidad + "</td>";
		tr_html +=		"<td class='marca_d'>" + detalles_movimiento[i].marca + "</td>";
		tr_html +=		"<td class='modelo_d'>" + detalles_movimiento[i].modelo + "</td>";
		tr_html +=		"<td class='descripcion_d'>" + detalles_movimiento[i].descripcion + "</td>";
		tr_html +=		"<td class='talla_d'>" + detalles_movimiento[i].talla + "</td>";
		tr_html +=		"<td class='precio_d'>" + detalles_movimiento[i].precio + "</td>";
		tr_html += "</tr>";

		total_c += parseInt(detalles_movimiento[i].cantidad);
		total_p += parseInt(detalles_movimiento[i].precio);
	}

	$("#tabla_detalles").find("tbody").prepend(tr_html);
	$("#total_cantidad").html(total_c);
	$("#total_precio").html(total_p);
}

function cancelar(obj_button, id_movimiento){
	var tr_current = $(obj_button).parent().parent();

	bootbox.confirm("Seguro que desea borrar el producto?", function(result) {
		if(result){
			//$(obj_button).parent().parent().remove();
			$.ajax({
			    // la URL para la petición
			    url : "cancelar_movimiento",
			 
			    // especifica si será una petición POST o GET
			    type : "POST",

			    // envia los valores del form
			    data : {
			    	movimiento : id_movimiento
			    },

			    //especifica el tipo de dato que espera recibir
			    dataType: 'json',

			    /*beforeSend : function(xhr){
			    	$("#tabla_detalles").find("tbody").html('<tr><td colspan="9" align="center"><img src="/inventarios/assets/img/cargando.gif" /></td></tr>');
			    },*/

			    // código a ejecutar si la petición es satisfactoria;
			    // la respuesta es pasada como argumento a la función
			    success : function(cancelacion_movimiento) {
			    	bootbox.alert(cancelacion_movimiento.mensaje);
			    	if(cancelacion_movimiento.resp == "t"){
			    		$(obj_button).prop("disabled", true);
			    		tr_current.find(".estatus").css("color", "#c12e2a").text("Cancelado");
			    	}
			    },
			 
			    // código a ejecutar si la petición falla;
			    // son pasados como argumentos a la función
			    // el objeto de la petición en crudo y código de estatus de la petición
			    error : function(xhr, status) {
			        bootbox.alert('Disculpe, existió un problema');
			    }
			});
		}
	});
}