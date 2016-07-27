/* Funciones de producto */
$(document).ready(function(){

	$('[data-toggle="tooltip"]').tooltip();

	$("#codigo_barras").on("keypress", function(event){
		
		codigo = $(this).val();
		almacen_s = $("#almacen_s").val();
		if(event.keyCode == 13){
			if(almacen_s > 0){
				$.ajax({
				    // la URL para la petición
				    url : "obtener_producto",
				 
				    // especifica si será una petición POST o GET
				    type : "POST",

				    //datos enviados mediante post
				    data: { codigo_barras : codigo,
				    		id_almacen_s : almacen_s },

				    //especifica el tipo de dato que espera recibir
				    dataType: 'json',

				    // código a ejecutar si la petición es satisfactoria;
				    // la respuesta es pasada como argumento a la función
				    success : function(respuesta_producto) {
				    	if (respuesta_producto == null) {
				    		bootbox.alert('El código no existe favor de ingresarlo correctamente o su cantidad en el almacen de salida es 0.');
				    	}else{
				    		tr_current = $(".producto_" + respuesta_producto[0].id_producto);
				    		tr_count = $(tr_current).find(".talla_" + respuesta_producto[0].id_talla).length;

				    		if(tr_count > 0){
				    			cantidad_traspaso = parseInt($(tr_current).find(".talla_" + respuesta_producto[0].id_talla).parent().find('.cantidad_v').text()) + 1;
				    			cantidad_max = 0;

				    			for (i = 0; i < respuesta_producto.length ; i++) {
				    				if(respuesta_producto[i].id_tipo_movimiento == 1 || respuesta_producto[i].id_tipo_movimiento == 7 || respuesta_producto[i].id_tipo_movimiento == 8 || respuesta_producto[i].id_tipo_movimiento == 9){
				    					cantidad_max += parseInt(respuesta_producto[i].cantidad);
				    				}else{
				    					cantidad_max -= parseInt(respuesta_producto[i].cantidad);
				    				}
				    			}

				    			if(cantidad_max >= cantidad_traspaso){
					    			td_current = $(tr_current).find(".talla_" + respuesta_producto[0].id_talla);
					    			
					    			if(td_current.length > 0){
					    				add_quantity($(td_current).parent(), 1);
					    			}else{
					    				tr_new = crea_tr(respuesta_producto);
					    				$("#tabla_traspasos tbody").prepend(tr_new);
					    			}

					    			update_quantity("s", 1);

					    		}else{
					    			bootbox.alert("La cantidad de salida no puede ser mayor a la cantidad en el inventario fisico del almacén.");
					    		}
				    		}else{
				    			tr_new = crea_tr(respuesta_producto);
				    			$("#tabla_traspasos tbody").prepend(tr_new);
				    			update_quantity("s", 1);
				    		}
				    	}
				    	$("#codigo_barras").val("");
				    },
				 
				    // código a ejecutar si la petición falla;
				    // son pasados como argumentos a la función
				    // el objeto de la petición en crudo y código de estatus de la petición
				    error : function(xhr, status) {
				        bootbox.alert('Disculpe, existió un problema');
				    }
				});
			}else{
				$(this).val("");
				bootbox.alert("Debes seleccionar un almacén para realizar la búsqueda del producto.");
			}
		}
	});

	$("#buscar_modelo").on("click", function(){

		almacen_s = $("#almacen_s").val();

		if(almacen_s > 0){
			$.ajax({
			    // la URL para la petición
			    url : "obtener_marcas",
			 
			    // especifica si será una petición POST o GET
			    type : "POST",

			    //especifica el tipo de dato que espera recibir
			    dataType: 'json',

			    // código a ejecutar si la petición es satisfactoria;
			    // la respuesta es pasada como argumento a la función
			    success : function(respuesta_marcas) {
			    	if (respuesta_marcas != null) {
			    		html_select =  '<label for="marca">Marca: </label>';
			    		html_select += '<select name="marca" id="marcas_modal" class="form-control">';
						html_select +=		'<option value="0">Seleccionar...</option>';
						
						for (i = 0; i < respuesta_marcas.length; i++) {
							html_select += '<option value="' + respuesta_marcas[i].id_marca + '">' + respuesta_marcas[i].marca + '</option>';
						}
						
						html_select += '</select>';
						$("#marca_modal").html(html_select);
			    	} else{
			    		bootbox.alert('Hubo un error al cargar las marcas, intente cerrando y abriendo la traspasona de búsqueda por modelo.');
			    	}
			    },
			 
			    // código a ejecutar si la petición falla;
			    // son pasados como argumentos a la función
			    // el objeto de la petición en crudo y código de estatus de la petición
			    error : function(xhr, status) {
			        bootbox.alert('Disculpe, existió un problema');
			    }
			});

			$('#modelos_p').modal();
		}else{
			$("#codigo_barras").val("");
			bootbox.alert("Debes seleccionar un almacén para realizar la búsqueda del producto.");
		}
	});

	$("#find_model").on("click", function(){
		marca = $("#marca_modal").find('select').val();
		modelo = $("#modelo").val();
		if(modelo.trim() == ""){
			modelo = 0;
		}
		almacen_s = $("#almacen_s").val();

		if(marca != 0 || modelo.trim() != ""){
			$.ajax({
			    // la URL para la petición
			    url : "obtener_producto_modelo/" + marca + "/" + modelo + "/" + almacen_s,
			 
			    // especifica si será una petición POST o GET
			    //type : "POST",

			    //especifica el tipo de dato que espera recibir
			    dataType: 'json',

			    beforeSend : function(xhr){
			    	$("#modelos_p").find(".cargando").html('<img src="/inventarios/assets/img/cargando.gif" />');
			    },

			    // código a ejecutar si la petición es satisfactoria;
			    // la respuesta es pasada como argumento a la función
			    success : function(respuesta_modelos) {
			    	$("#modelos_p").find(".cargando").html('');
			    	if (respuesta_modelos != null) {
						$("#tabla_modelos").find("tbody").html("");
						html_tr = "";
						
						for (i = 0; i < respuesta_modelos.length; i++) {
							html_tr = '<tr class="text-center producto_' + respuesta_modelos[i].id_producto + '">';
							html_tr += 		'<td><input type="checkbox" name="producto_modelo" class="sel_check" disabled /></td>';
							html_tr += 		'<td class="marca_s">' + respuesta_modelos[i].marca + '</td>';
							html_tr += 		'<td class="modelo_s">' + respuesta_modelos[i].modelo + '</td>';
							html_tr += 		'<td class="descripcion_s">' + respuesta_modelos[i].descripcion + '</td>';
							html_tr +=		'<td class="tallas_s"></td>';
							html_tr +=		'<td class="cantidad_s"><input type="number" min="1" value="1" /></td>';
							html_tr += '</tr>';
							$("#tabla_modelos").find("tbody").prepend(html_tr);
							$("#tabla_modelos").find(".tallas_s").load("obtener_tallas_select", function( response, status, xhr ) {
								if ( status == "error" ) {
									var msg = "Disculpe, existió un problema: ";
									bootbox.alert( msg + xhr.status + " " + xhr.statusText );
								}
							});
						}

			    	} else{
			    		bootbox.alert('No existen productos con esas opciones de búsqueda.');
			    		$("#modelos_p").find(".cargando").html('');
			    	}
			    },
			 
			    // código a ejecutar si la petición falla;
			    // son pasados como argumentos a la función
			    // el objeto de la petición en crudo y código de estatus de la petición
			    error : function(xhr, status) {
			        bootbox.alert('Hubo un error al cargar los productos, intentelo de nuevo.');
			    }
			});
		}else{
			bootbox.alert("Favor de seleccionar una marca o teclear un modelo.");
			$("#marca_modal").find('select').focus();
		}
	});

	$("#send_sel").on("click", function(){
		tbody = $("#tabla_modelos").find("tbody");
		tr_count = $(tbody).find("tr").length;
		id_almacen_s = $("#almacen_s").val();
		productos = new Array();
		productos[0] = new Object();

		for (i = 0 ; i < tr_count ; i++) {
			tr_current = $(tbody).find("tr").eq(i);
			if($(tr_current).find(".sel_check").prop("checked")){
				producto = $(tr_current).attr("class").split(" ");
				id_producto = producto[1].split("_");
				productos[0].id_producto = id_producto[1];
				productos[0].marca = $(tr_current).find(".marca_s").text();
				productos[0].modelo = $(tr_current).find(".modelo_s").text();
				productos[0].descripcion = $(tr_current).find(".descripcion_s").text();
				productos[0].id_talla = $(tr_current).find(".talla_select").val();
				productos[0].talla = $(tr_current).find(".talla_select option:selected").text();
				cantidad_sel = $(tr_current).find(".cantidad_s").find("input").val();

				tr_class = $("#tabla_traspasos").find("tbody").find(".producto_" + productos[0].id_producto);
				td_current = $(tr_class).find(".talla_" + productos[0].id_talla);

				if(td_current.length > 0) {
					cantidad_sal = parseInt($(td_current).parent().find(".cantidad_v").text()) + parseInt(cantidad_sel);
					cantidad_max_sal = parseInt($(td_current).parent().find(".cant_max").val());

					if(cantidad_sal <= cantidad_max_sal){
						//add_quantity_prod(productos[0].id_producto, productos[0].id_talla, cantidad_sel);
						update_quantity("s", cantidad_sel);
						add_quantity($(td_current).parent(), cantidad_sel);
					}else{
						bootbox.alert("La cantidad de salida no puede ser mayor a la cantidad en el inventario fisico del almacén de salida.");
					}
				}else{
					obtener_cantidad_modelo(productos, tr_class, id_almacen_s, cantidad_sel, productos[0].id_talla);
				}
			}
		}

		$(tbody).html("");
		$('#modelos_p').modal("hide");
	});

	$("#traspaso_s").on("click", function(){
		var tbody = $("#tabla_traspasos").find("tbody");
		var id_almacen_s = $("#almacen_s").val();
		var id_almacen_e = $("#almacen_e").val();
		count_tr = $(tbody).find("tr").length;

		if(id_almacen_e != 0){
			if(id_almacen_s.localeCompare(id_almacen_e) != 0){
				if(count_tr > 1){
					bootbox.confirm("Estás seguro de finalizar la traspaso?", function(result) {
						if(result){
							get_values_transfer(tbody, id_almacen_s, id_almacen_e);
						}
					});
				}else{
					bootbox.alert("Necesitas agregar productos para registrar una traspaso.");
				}
			}else{
				bootbox.alert("El almacén de salida y entrada deben ser distintos.");
			}
		}else{
			bootbox.alert("Debes seleccionar el almacén de entrada donde se hará la traspaso.");
			$("#almacen_e").focus();
		}
	});

	$("#traspaso_e").on("click", function(){
		if($(this).text().length == 36){
			var movs_ent = $("#t_entrada").find("input").val();
			var movs_sal = $("#t_salida").find("input").val();

			get_transfers(movs_ent, movs_sal);
		}
	});

	$("#cancelar").on("click", function(){
		var tbody = $("#tabla_traspasos").find("tbody");
		var count_tr = $(tbody).find("tr").length;

		bootbox.confirm("Estás seguro de cancelar la traspaso?", function(result) {
			if(result){
				location.href = "index";
			}
		});
	});

	$("#almacen_s").on("change", function(){
		tbody_clean();
	});
});

function get_transfers(movs_ent, movs_sal){
	$.ajax({
	    // la URL para la petición
	    url : "obtener_traspasos",
	 
	    // especifica si será una petición POST o GET
	    type : "POST",

	    //datos pasados por el metodo post
	    data: { movs_e : movs_ent,
	    		movs_s : movs_sal },

	    //especifica el tipo de dato que espera recibir
	    dataType: 'json',

	    // código a ejecutar si la petición es satisfactoria;
	    // la respuesta es pasada como argumento a la función
	    success : function(transfers) {
	    	if(transfers != null){
	    		$("#tabla_traspasos").hide();
	    		$("#traspaso_s").prop("disabled", true);
	    		$("#almacen_s").prop("disabled", true);
	    		$("#almacen_e").prop("disabled", true);
	    		$("#codigo_barras").prop("disabled", true);
	    		$("#buscar_modelo").prop("disabled", true);
	    		$("#traspaso_e").slideUp("fast").text("Confirmar traspasos");
	    		$("#traspaso_e").append("&nbsp;<span class='glyphicon glyphicon-ok' aria-hidden='true'></span>");

	    		crea_table_transfers(transfers);
	    		
	    		$("#traspaso_e").slideDown("slow").attr("onclick", "confirm_transfers()");
	    		$("#traspaso_e").removeClass('btn-warning').addClass('btn-success');
	    	}else{
	    		bootbox.alert("No existen traspasos pendientes.");
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

function crea_table_transfers(transfers){
	var html_table = "";

	for (var k = 0 ; k < transfers[0].outlet.length ; k++){
	
		var cantidad = 0;
		var almacen_origen = '';
		almacen_origen = elegir_almacen(transfers[0].outlet[k][0].id_almacen_s);
		var almacen_destino = '';
		almacen_destino = elegir_almacen(transfers[0].outlet[k][0].id_almacen_e);

		html_table += '<table class="table table-bordered table-condensed table-outlet" id="output_' + transfers[0].outlet[k][0].id_movimiento + '">';
		html_table += 		'<thead>';
		html_table += 			'<tr class="th-transfer-o">';
		html_table +=				'<th style="border: hidden; background-color: white;"></th>'
		html_table += 				'<th style="border: hidden;" class="text-center" colspan="5">Traspaso para que confirmes; Origen: ' + almacen_origen + ' -> Destino: ' + almacen_destino + '</th>';
		html_table += 			'</tr>';
		html_table += 			'<tr class="th-transfer-o">';
		html_table +=				'<th style="border: hidden; background-color: white;"></th>';
		html_table +=				'<th class="cantidad text-center">Cantidad</th>';
		html_table +=				'<th class="marca text-center">Marca</th>';
		html_table +=				'<th class="modelo text-center">Modelo</th>';
		html_table +=				'<th class="descripcion text-center">Descripcion</th>';
		html_table +=				'<th class="talla text-center">Talla</th>';
		html_table += 			'</tr>';
		html_table += 		'</thead>';
		html_table += 		'<tbody>';

		for (var l = 0; l < transfers[0].outlet[k].length; l++) {
			html_table += "<tr class='text-center'>";
			html_table += 		"<td style='border: hidden; background-color: white;'></td>";
			html_table += 		"<td class='cantidad_v'>" + transfers[0].outlet[k][l].cantidad + "</td>";
			html_table += 		"<td class='marca_v'>" + transfers[0].outlet[k][l].marca + "</td>";
			html_table += 		"<td class='modelo_v'>" + transfers[0].outlet[k][l].modelo + "</td>";
			html_table += 		"<td class='descripcion_v'>" + transfers[0].outlet[k][l].descripcion + "</td>";
			html_table += 		"<td>" + transfers[0].outlet[k][l].talla + "</td>";
			html_table += "</tr>";

			cantidad += parseInt(transfers[0].outlet[k][l].cantidad);
		}

		html_table += 			'<tr class="text-center">';
		html_table += 				'<td class="total_traspasos th-transfer-o"><b>Total cantidad: </b></td>';
		html_table += 				'<td>' + cantidad + '</td>';
		html_table += 				'<td style="border: hidden;"></td>';
		html_table += 				'<td style="border: hidden;"></td>';
		html_table += 				'<td style="border: hidden;"></td>';
		html_table += 				'<td style="border: hidden;"></td>';
		html_table += 			'</tr>';
		html_table += 			'<tr class="text-center">';
		html_table += 				'<td style="border: hidden;"></td>';
		html_table += 				'<td style="border: hidden;"><button onclick="cancel_transfer(' + transfers[0].outlet[k][0].id_movimiento + ')" style="display: inline-block;" type="button" class="btn btn-sm btn-danger" class="cancel_transfer">Cancelar traspaso <span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></td>';
		html_table += 				'<td style="border: hidden;"></td>';
		html_table += 				'<td style="border: hidden;"></td>';
		html_table += 				'<td style="border: hidden;"><button onclick="final_transfer(' + transfers[0].outlet[k][0].id_movimiento + ', this)" style="display: inline-block;" type="button" class="btn btn-sm btn-success" class="final_transfer">Confirmar traspaso <span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button></td>';
		html_table += 				'<td style="border: hidden;"></td>';
		html_table += 			'</tr>';
		html_table += 		'</tbody>';

	}

	for (var i = 0 ; i < transfers[0].entry.length ; i++){
	
		var cantidad_t = 0;
		var almacen_origen = '';
		almacen_origen = elegir_almacen(transfers[0].entry[i][0].id_almacen_s);
		var almacen_destino = '';
		almacen_destino = elegir_almacen(transfers[0].entry[i][0].id_almacen_e);

		html_table += '<table class="table table-bordered table-condensed table-entries" id="entry_' + transfers[0].entry[i][0].id_movimiento + '">';
		html_table += 		'<thead>';
		html_table += 			'<tr class="th-transfer-e">';
		html_table +=				'<th style="border: hidden; background-color: white;"></th>'
		html_table += 				'<th style="border: hidden;" class="text-center" colspan="5">Traspaso para que te confirmen; Origen: ' + almacen_origen + ' -> Destino: ' + almacen_destino + '</th>';
		html_table += 			'</tr>';
		html_table += 			'<tr class="th-transfer-e">';
		html_table +=				'<th style="border: hidden; background-color: white;"></th>';
		html_table +=				'<th class="cantidad text-center">Cantidad</th>';
		html_table +=				'<th class="marca text-center">Marca</th>';
		html_table +=				'<th class="modelo text-center">Modelo</th>';
		html_table +=				'<th class="descripcion text-center">Descripcion</th>';
		html_table +=				'<th class="talla text-center">Talla</th>';
		html_table += 			'</tr>';
		html_table += 		'</thead>';
		html_table += 		'<tbody>';

		for (var j = 0; j < transfers[0].entry[i].length; j++) {
			html_table += "<tr class='text-center'>";
			html_table += 		"<td style='border: hidden; background-color: white;'></td>";
			html_table += 		"<td class='cantidad_v'>" + transfers[0].entry[i][j].cantidad + "</td>";
			html_table += 		"<td class='marca_v'>" + transfers[0].entry[i][j].marca + "</td>";
			html_table += 		"<td class='modelo_v'>" + transfers[0].entry[i][j].modelo + "</td>";
			html_table += 		"<td class='descripcion_v'>" + transfers[0].entry[i][j].descripcion + "</td>";
			html_table += 		"<td>" + transfers[0].entry[i][j].talla + "</td>";
			html_table += "</tr>";

			cantidad_t += parseInt(transfers[0].entry[i][j].cantidad);
		}

		html_table += 			'<tr class="text-center">';
		html_table += 				'<td class="total_traspasos th-transfer-e"><b>Total cantidad: </b></td>';
		html_table += 				'<td>' + cantidad_t + '</td>';
		html_table += 				'<td style="border: hidden;"></td>';
		html_table += 				'<td style="border: hidden;"></td>';
		html_table += 				'<td style="border: hidden;"></td>';
		html_table += 				'<td style="border: hidden;"></td>';
		html_table += 			'</tr>';
		html_table += 		'</tbody>';

	}

	$(".tables-transfer").prepend(html_table);
}

function elegir_almacen(id_almacen){
	switch(id_almacen) {
	    case '1':
	        return 'A';
	        break;
	    case '2':
	        return 'B';
	        break;
	    case '3':
	        return 'C';
	        break;
	    case '4':
	        return 'D';
	        break;
	    default:
	        return 'No existe ese almacén';
	}
}

function confirm_transfers(){
	var table_outlet = $(".table-outlet");
	var movimientos = new Array;

	for (var i = 0; i < table_outlet.length; i++) {
		var id_mov = (table_outlet[i].id).replace("output_", "");
		movimientos.push(id_mov);
	}

	var id_movimientos = movimientos.toString();
	
	$.ajax({
	    // la URL para la petición
	    url : "confirmar_movimientos",
	 
	    // especifica si será una petición POST o GET
	    type : "POST",

	    // envia los valores del form
	    data : { movs : id_movimientos },

	    //especifica el tipo de dato que espera recibir
	    dataType: 'html',

	    // código a ejecutar si la petición es satisfactoria;
	    // la respuesta es pasada como argumento a la función
	    success : function(confirmacion_traspasos) {
	    	json_p = $.parseJSON(confirmacion_traspasos);
	    	bootbox.alert(json_p.mensaje);

	    	if(json_p.resp == 't'){
	    		table_id_movimiento.slideUp('fast');
	    		var valor_entrada_t = $("#t_entrada").text().trim();
	    		var valor_current = parseInt(valor_entrada_t) - 1;

	    		if (valor_current == 0){
	    			$("#t_entrada").remove();
	    		}else{
	    			$("#t_entrada").text(valor_current);
	    			$("#t_entrada").append('<span class="glyphicon glyphicon-save" aria-hidden="true"></span>');
	    		}
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

function final_transfer(id_movimiento, obj_button){
	var table_id_movimiento = $(obj_button).parent().parent().parent().parent();
	var id_movimiento = table_id_movimiento.attr('id').replace('output_', '');
	$.ajax({
	    // la URL para la petición
	    url : "confirmar_movimientos",
	 
	    // especifica si será una petición POST o GET
	    type : "POST",

	    // envia los valores del form
	    data : { movs : id_movimiento },

	    //especifica el tipo de dato que espera recibir
	    dataType: 'html',

	    // código a ejecutar si la petición es satisfactoria;
	    // la respuesta es pasada como argumento a la función
	    success : function(confirmacion_traspasos) {
	    	json_p = $.parseJSON(confirmacion_traspasos);
	    	bootbox.alert(json_p.mensaje);

	    	if(json_p.resp == 't'){
	    		table_id_movimiento.slideUp('fast');
	    		var valor_salida_t = $("#t_salida").text().trim();
	    		var valor_current = parseInt(valor_salida_t) - 1;

	    		if (valor_current == 0){
	    			$("#t_salida").remove();
	    		}else{
	    			$("#t_salida").text(valor_current);
	    			$("#t_salida").append('<span class="glyphicon glyphicon-open" aria-hidden="true"></span>');
	    		}

	    		if($("#entry_" + id_movimiento).length > 0){
	    			$("#entry_" + id_movimiento).slideUp('fast');
	    			var valor_entrada_t = $("#t_entrada").text().trim();
		    		var valor_current_e = parseInt(valor_entrada_t) - 1;

		    		if (valor_current_e == 0){
		    			$("#t_entrada").remove();
		    		}else{
		    			$("#t_entrada").text(valor_current_e);
		    			$("#t_entrada").append('<span class="glyphicon glyphicon-save" aria-hidden="true"></span>');
		    		}
	    		}
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

function cancel_transfer(id_movimiento){
	var table_id_movimiento = $(obj_button).parent().parent().parent().parent();
	$.ajax({
	    // la URL para la petición
	    url : "cancelar_movimientos",
	 
	    // especifica si será una petición POST o GET
	    type : "POST",

	    // envia los valores del form
	    data : { movs : id_movimiento },

	    //especifica el tipo de dato que espera recibir
	    dataType: 'html',

	    // código a ejecutar si la petición es satisfactoria;
	    // la respuesta es pasada como argumento a la función
	    success : function(cancelacion_traspasos) {
	    	json_p = $.parseJSON(cancelacion_traspasos);
	    	bootbox.alert(json_p.mensaje);

	    	if(json_p.resp == 't'){
	    		table_id_movimiento.slideUp('fast');
	    		var valor_salida_t = $("#t_salida").text().trim();
	    		var valor_current = parseInt(valor_salida_t) - 1;

	    		if (valor_current == 0){
	    			$("#t_salida").remove();
	    		}else{
	    			$("#t_salida").text(valor_current);
	    			$("#t_salida").append('<span class="glyphicon glyphicon-open" aria-hidden="true"></span>');
	    		}

	    		if($("#entry_" + id_movimiento).length > 0){
	    			$("#entry_" + id_movimiento).slideUp('fast');
	    			var valor_entrada_t = $("#t_entrada").text().trim();
		    		var valor_current_e = parseInt(valor_entrada_t) - 1;

		    		if (valor_current_e == 0){
		    			$("#t_entrada").remove();
		    		}else{
		    			$("#t_entrada").text(valor_current_e);
		    			$("#t_entrada").append('<span class="glyphicon glyphicon-save" aria-hidden="true"></span>');
		    		}
	    		}
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

function add_quantity(tr_current, quantity){
	td_cantidad = $(tr_current).find(".cantidad_v");
	quantity_current = parseInt($(td_cantidad).text());
	quantity_new = quantity_current + parseInt(quantity);
	$(tr_current).find("td.cantidad_v").text(quantity_new);
}

function crea_tr(producto){
	var cant_max = obtener_cantidad_max(producto);
	html_tr = "<tr class='text-center producto_" + producto[0].id_producto + "'>";
	html_tr += 	"<td style='border: hidden; background-color: white;'><input type='hidden' class='cant_max' value='" + cant_max + "' /></td>";
	html_tr += 	"<td class='cantidad_v'>1</td>";
	html_tr += 	"<td class='marca_v'>" + producto[0].marca + "</td>";
	html_tr += 	"<td class='modelo_v'>" + producto[0].modelo + "</td>";
	html_tr += 	"<td class='descripcion_v'>" + producto[0].descripcion + "</td>";
	html_tr += 	"<td class='talla_" + producto[0].id_talla + "'>" + producto[0].talla + "</td>";
	html_tr += 	"<td>";
	html_tr +=		"<button type='button' class='btn btn-danger btn-sm' onclick='remove_tr(this);'>";
	html_tr +=			"<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>";
	html_tr +=		"</button>";
	html_tr +=	"</td>";
	html_tr += "</tr>";

	return html_tr;
}

/*function update_total_precio(){
	tbody = $("#tabla_traspasos").find("tbody");
	tr_current = $(tbody).find("tr");
	tr_count = tr_current.length;

	var quantity_new = 0;
	var quantity_current;

	for (var i = 0; i < tr_count - 1 ; i++){
		quantity_current = parseInt($(tr_current).eq(i).find(".cantidad_v").text()) * parseInt($(tr_current).eq(i).find(".precio_v").find("input").val());
		quantity_new += quantity_current;
	}

	$("#total_p").text(quantity_new);
}*/

function remove_tr(obj_button){
	tr_current = $(obj_button).parent().parent();
	quantity_current = parseInt($(tr_current).find(".cantidad_v").text());

	if(quantity_current > 1){
		ask_quantity(quantity_current, tr_current);
	}else{
		bootbox.confirm("Estás seguro de borrar el producto de la traspaso?", function(result) {
			if(result){
				$(tr_current).remove();
				update_quantity("r", 1);
			}
		});
	}	
}

function ask_quantity(quantity_current, tr_current){
	bootbox.prompt({
		title: "Cuántas piezas quieres borrar?",
		//value: "makeusabrew",
		callback: function(result) {
			if (result === null) {
			  return null;
			} else {
				quantity_remove = parseInt(result);
			  	if(isNaN(quantity_remove)){
					bootbox.alert('Necesita ingresar un número en cantidad para eliminar piezas.');
				}else{
					if(quantity_remove > 0){
						if(quantity_remove > quantity_current){
							bootbox.alert('La cantidad a borrar debe ser menor o igual a la cantidad en la traspaso.');
						}else{
							quantity_new_ask = quantity_current - quantity_remove;
							if(quantity_new_ask == 0){
								$(tr_current).remove();
							}else{
								$(tr_current).find(".cantidad_v").text(quantity_new_ask);
							}

							update_quantity("r", quantity_remove);
						}
					}else{
						bootbox.alert('La cantidad a borrar debe ser mayor que cero.');
					}
				}
			}
		}
	});
}

function valida_talla(obj_input){
	if($(obj_input).val() == '0'){
		$(obj_input).parent().parent().find(".sel_check").prop("checked", false);
	}else{
		$(obj_input).parent().parent().find(".sel_check").prop("checked", true);
	}
}

function update_quantity(operation, quantity_remove){
	quantity_current = parseInt($("#total_t").text());

	if (operation == "s"){
		quantity_new = quantity_current + parseInt(quantity_remove);
	}else{
		quantity_new = quantity_current - parseInt(quantity_remove);
	}
	
	$("#total_t").text(quantity_new);
}

function get_values_transfer(tbody_transfer, id_almacen_s, id_almacen_e){
	transfer = new Object;
	transfer_detail = new Array();

	transfer.cantidad = parseInt($("#total_t").text());
	transfer.id_almacen_s = id_almacen_s;
	transfer.id_almacen_e = id_almacen_e;

	for(i = 0 ; i < (count_tr - 1) ; i++){
		tr_id = $(tbody_transfer).find("tr").eq(i).attr("class");
		tr_id_producto = tr_id.split(" ");
		id_producto_tr = tr_id_producto[1].split("_");

		tr_talla = $(tbody_transfer).find("tr").eq(i).find("td").eq(5).attr("class");
		tr_id_talla = tr_talla.split("_");
		id_talla_tr = tr_id_talla[1];

		cantidad = $(tbody_transfer).find("tr").eq(i).find(".cantidad_v").text();

		transfer_detail[i] = new Object;

		transfer_detail[i].id_producto = id_producto_tr[1];
		transfer_detail[i].id_talla = id_talla_tr;
		transfer_detail[i].cantidad = cantidad;
	}

	send_values_transfer(transfer, transfer_detail);
}

function send_values_transfer(transfer, transfer_detail){console.log(transfer);
	$.ajax({
	    // la URL para la petición
	    url : "registrar_traspaso",
	 
	    // especifica si será una petición POST o GET
	    type : "POST",

	    //datos enviados mediante post
	    data: { obj_transfer : transfer,
	    		obj_transfer_detail: transfer_detail },

	    //especifica el tipo de dato que espera recibir
	    dataType: 'html',

	    // código a ejecutar si la petición es satisfactoria;
	    // la respuesta es pasada como argumento a la función
	    success : function(respuesta_traspaso) {
	    	respuesta_t = respuesta_traspaso.split("|");
	    	bootbox.alert(respuesta_t[0]);

	    	if(respuesta_t[1] == 't'){
	    		location.href = "index";
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

function obtener_cantidad_modelo(producto, tr_class, almacen_s, cantidad_sel, talla){
	$.ajax({
		url: "obtener_cantidad_modelo",
		type: "POST",
		data: { id_producto : producto[0].id_producto,
	    		id_almacen_s : almacen_s,
	    		id_talla : talla },
	    dataType: 'json',
	    // código a ejecutar si la petición es satisfactoria;
	    // la respuesta es pasada como argumento a la función
	    success : function(respuesta_modelo) {
	    	if(respuesta_modelo != null){
	    		var cantidad_max = obtener_cantidad_max(respuesta_modelo);
		    	if(cantidad_sel <= cantidad_max){
		    		html_tr = '<tr class="text-center producto_' + productos[0].id_producto + '">';
		    		html_tr +=		'<td style="border: hidden; background-color: white;"><input type="hidden" class="cant_max" value="' + cantidad_max + '" /></td>';
					html_tr +=		'<td class="cantidad_v">' + cantidad_sel + '</td>';
					html_tr += 		'<td class="marca_v">' + productos[0].marca + '</td>';
					html_tr += 		'<td class="modelo_v">' + productos[0].modelo + '</td>';
					html_tr += 		'<td class="descripcion_v">' + productos[0].descripcion + '</td>';
					html_tr +=		'<td class="talla_' + productos[0].id_talla + '">' + productos[0].talla + '</td>';
					html_tr += 	"<td>";
					html_tr +=		"<button type='button' class='btn btn-danger btn-sm' onclick='remove_tr(this);'>";
					html_tr +=			"<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>";
					html_tr +=		"</button>";
					html_tr +=	"</td>";
					html_tr += '</tr>';

					$("#tabla_traspasos tbody").prepend(html_tr);

					update_quantity("s", cantidad_sel);
					add_quantity($(td_current).parent(), cantidad_sel);
		    	}else{
		    		bootbox.alert("La cantidad de salida no puede ser mayor a la cantidad en el intraspasorio fisico del almacén.");
		    	}
		    }else{
		    	bootbox.alert("La cantidad del producto en el intraspasorio fisico del almacén es igual a 0.");
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

function obtener_cantidad_max(respuesta_modelo){
	var cant_max = 0;
	
	for(var i = 0 ; i < respuesta_modelo.length ; i++){
		if(respuesta_modelo[i].id_tipo_movimiento == '1'){
			cant_max += parseInt(respuesta_modelo[i].cantidad);
		}else{
			cant_max -= parseInt(respuesta_modelo[i].cantidad);
		}
	}

	return cant_max;
}

function tbody_clean(){
	tbody_v = $("#tabla_traspasos").find("tbody");
	tr_current = $(tbody_v).find("tr");
	tr_count = $(tr_current).length;

	for (i = 0 ; i < tr_count - 1 ; i++) {
		$(tr_current).eq(i).remove();
	}

	$("#total_t").text("0");	
}