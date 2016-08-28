/* Funciones de producto */
$(document).ready(function(){

	$("#finalizar").on("click", function(){
		var tbody = $("#tabla_salidas").find("tbody");
		var id_almacen = $("#almacen").val();
		var tipo_m = $("#tipo_movimiento").val();
		var selection = verificar_selection(tbody);

		if(id_almacen != 0){
			if(tipo_m != 0){
				if(selection > 0){
					bootbox.confirm("Estás seguro de finalizar la salida?", function(result) {
						if(result){
							get_values_outlet(tbody);
						}
					});
				}else{
					bootbox.alert("Debes seleccionar al menos un producto para realizar un ajuste de inventario.");
				}
			}else{
				bootbox.alert("Debes seleccionar el tipo de movimiento para realizar un ajuste de inventario.");
				$("#tipo_movimiento").focus();
			}
		}else{
			bootbox.alert("Debes seleccionar el almacén donde se hará el ajuste de inventario.");
			$("#almacen").focus();
		}
	});

	/*$("#pausar").on("click", function(){
		tbody = $("#tabla_salidas").find("tbody");
		id_almacen = $("#almacen").val();
		count_tr = $(tbody).find("tr").length;

		if(id_almacen != 0){
			if(count_tr > 1){
				bootbox.confirm("Estás seguro de pausar la salida?", function(result) {
					if(result){
						get_values_outlet(tbody);
					}
				});
			}else{
				bootbox.alert("Necesitas agregar productos para pausar una salida.");
			}
		}else{
			bootbox.alert("Debes seleccionar el almacén donde se hará la salida.");
			$("#almacen").focus();
		}
	});*/

	$("#cancelar").on("click", function(){
		tbody = $("#tabla_salidas").find("tbody");
		count_tr = $(tbody).find("tr").length;

		if(count_tr > 1){
			bootbox.confirm("Estás seguro de cancelar el ajuste de inventario?", function(result) {
				if(result){
					tbody_clean();
					update_total_sistema();
				}
			});
		}else{
			bootbox.alert("Necesitas agregar productos para cancelar un ajuste de inventario.");
		}
	});

	$("#almacen").on("change", function(){
		tbody_clean();
	});

	$("#tipo_movimiento").on("change", function(){
		var id_almacen = $("#almacen").val();
		var tipo_m = $("#tipo_movimiento").val();
		tbody_clean();

		if(tipo_m != "0"){
			$.ajax({
			    // la URL para la petición
			    url : "obtener_datos_ajuste",
			 
			    // especifica si será una petición POST o GET
			    type : "POST",

			    //datos enviados mediante post
			    data: { almacen : id_almacen,
			    		tipo : tipo_m },

			    //especifica el tipo de dato que espera recibir
			    dataType: 'json',

			    // código a ejecutar si la petición es satisfactoria;
			    // la respuesta es pasada como argumento a la función
			    success : function(respuesta_ajuste) {
			    	if (respuesta_ajuste == null) {
			    		bootbox.alert('No existen movimientos para ajustar.');
			    	}else{
			    		crea_tr(respuesta_ajuste);
			    		update_total_sistema();
			    	}
			    },
			 
			    // código a ejecutar si la petición falla;
			    // son pasados como argumentos a la función
			    // el objeto de la petición en crudo y código de estatus de la petición
			    error : function(xhr, status) {
			        bootbox.alert('Disculpe, existió un problema');
			    }
			});
		}else{
			bootbox.alert("Debes seleccionar el tipo de movimiento para realizar un ajuste de inventario.");
			$("#tipo_movimiento").focus();
		}
	});
});

function crea_tr(producto){
	var html_tr = "";
	var color_back = "yellowgreen";

	for(var i = 0 ; i < producto.length ; i++){
		if(parseInt(producto[i].diferencia) < 0){
			color_back = "#db8a8a";
		}

		html_tr += "<tr class='text-center producto_" + producto[i].id_producto + "'>";
		html_tr += 	"<td class='marca'>" + producto[i].marca + "</td>";
		html_tr += 	"<td class='modelo'>" + producto[i].modelo + "</td>";
		html_tr += 	"<td class='descripcion'>" + producto[i].descripcion + "</td>";
		html_tr += 	"<td class='talla_" + producto[i].id_talla + "'>" + producto[i].talla + "</td>";
		html_tr += 	"<td class='cantidad_sistema'>" + producto[i].cantidad_sistema + "</td>";
		html_tr += 	"<td class='cantidad'>" + producto[i].cantidad_fisica + "</td>";
		html_tr += 	"<td class='diferencia' style='background-color:" + color_back + "'>" + producto[i].diferencia + "</td>";
		html_tr += 	"<td><input class='selection' type='checkbox' onchange='update_total_diff(this);'></td>";
		html_tr += "</tr>";
	}

	$("#tabla_salidas").prepend(html_tr);
}

function update_total_sistema(){
	var tr_class = $(".cantidad_sistema");
	var tr_class_cantidad = $(".cantidad");
	var total_fisico = 0;
	var total_sistema = 0;

	for (var i = 0; i < tr_class.length; i++) {
		cantidad = parseInt(tr_class_cantidad.eq(i).text());
		cantidad_sistema = parseInt(tr_class.eq(i).text());
		total_sistema += cantidad_sistema;
		total_fisico += cantidad;
	}

	$("#total_sistema").text(total_sistema);
	$("#total_s").text(total_fisico);
}

function verificar_selection(tbody){
	var tr_selection = tbody.find(".selection");
	var selected = 0;

	for (var i = 0; i < tr_selection.length; i++) {
		if(tr_selection.eq(i).prop("checked") == true){
			selected += 1;
		}
	}

	return selected;
}

function update_total_diff(obj_check){
	var tr_current = $(obj_check).parent().parent();
	var cantidad_diff = parseInt(tr_current.find(".diferencia").text());
	var total_diff_current = parseInt($("#total_diferencia").text());
	var total_diff = 0;
	var color_back = "";

	if(tr_current.find(".selection").prop("checked") == true){
		total_diff = total_diff_current + cantidad_diff;
	}else{
		total_diff = total_diff_current - cantidad_diff;
	}

	if(total_diff == 0){
		color_back = "#FFF";
	}else if(total_diff > 0){
		color_back = "yellowgreen";
	}else{
		color_back = "#db8a8a";
	}

	$("#total_diferencia").text(total_diff).css("background-color", color_back);
}

function get_values_outlet(tbody_adjustment){
	var adjustment = new Object;
	var adjustment_detail = new Array();
	var count_tr = $(tbody_adjustment).find("tr").length;
	var id_almacen = $("#almacen").val();
	var tipo_movimiento = $("#tipo_movimiento").val();

	adjustment.cantidad = parseInt($("#total_diferencia").text());
	adjustment.id_almacen = id_almacen;
	adjustment.id_tipo_movimiento = tipo_movimiento;

	for(i = 0 ; i < (count_tr - 1) ; i++){
		tr_id = $(tbody_adjustment).find("tr").eq(i).attr("class");
		tr_id_producto = tr_id.split(" ");
		id_producto_tr = tr_id_producto[1].split("_");

		tr_talla = $(tbody_adjustment).find("tr").eq(i).find("td").eq(3).attr("class");
		tr_id_talla = tr_talla.split("_");
		id_talla_tr = tr_id_talla[1];

		cantidad = $(tbody_adjustment).find("tr").eq(i).find(".diferencia").text();

		check = $(tbody_adjustment).find("tr").eq(i).find(".selection").prop("checked");

		adjustment_detail[i] = new Object;

		adjustment_detail[i].id_producto = id_producto_tr[1];
		adjustment_detail[i].id_talla = id_talla_tr;
		adjustment_detail[i].cantidad = cantidad;
		adjustment_detail[i].check = check;
	}

	send_values_outlet(adjustment, adjustment_detail);
}

function send_values_outlet(adjustment, adjustment_detail){
	$.ajax({
	    // la URL para la petición
	    url : "registrar_ajuste",
	 
	    // especifica si será una petición POST o GET
	    type : "POST",

	    //datos enviados mediante post
	    data: { obj_adjustment : adjustment,
	    		obj_adjustment_detail: adjustment_detail },

	    //especifica el tipo de dato que espera recibir
	    dataType: 'json',

	    // código a ejecutar si la petición es satisfactoria;
	    // la respuesta es pasada como argumento a la función
	    success : function(respuesta_ajuste) {
	    	bootbox.alert(respuesta_ajuste.mensaje);

	    	if(respuesta_ajuste.resp == 't'){
	    		tbody_clean();
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

function tbody_clean(){
	tbody_s = $("#tabla_salidas").find("tbody");
	tr_current = $(tbody_s).find("tr");
	tr_count = $(tr_current).length;

	for (i = 0 ; i < tr_count - 1 ; i++) {
		$(tr_current).eq(i).remove();
	}

	$("#total_s").text("0");
	$("#total_sistema").text("0");
	$("#total_diferencia").text("0").css("background-color", "#FFF");

	tbody_p = $("#tabla_productos").find("tbody");
	tr_current_p = $(tbody_p).find("tr");
	tr_count_p = $(tr_current_p).length;

	for (i = 0 ; i < tr_count_p - 1 ; i++) {
		$(tr_current_p).eq(i).remove();
	}

	$("#tr_tallas").find("td.tallas_c").text("0");	
}