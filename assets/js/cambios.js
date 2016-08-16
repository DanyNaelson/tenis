/* Funciones de producto */
$(document).ready(function(){

	$("#folio_venta").on("keypress", function(event){
		
		folio_v = $(this).val().trim();
		almacen = $("#almacen").val();
		if(event.keyCode == 13){
			if(almacen > 0){
				$.ajax({
				    // la URL para la petición
				    url : "obtener_producto_folio",
				 
				    // especifica si será una petición POST o GET
				    type : "POST",

				    //datos enviados mediante post
				    data: { folio : folio_v,
				    		id_almacen : almacen },

				    //especifica el tipo de dato que espera recibir
				    dataType: 'json',

				    // código a ejecutar si la petición es satisfactoria;
				    // la respuesta es pasada como argumento a la función
				    success : function(respuesta_producto) {
				    	if (respuesta_producto == null) {
				    		bootbox.alert('La venta no se realizó en el almacén seleccionado o el folio no existe en ventas, favor de ingresarlo correctamente.');
				    	}else{
				    		tr_current = $("." + folio_v);
				    		tr_count = $(tr_current).length;

				    		if(tr_count > 0){
				    			bootbox.alert('La venta ya fue cargada anteriormente.');
				    		}else{
				    			tr_new_folio = crea_tr_venta(respuesta_producto, folio_v);
				    			$("#tabla_folios tbody").prepend(tr_new_folio);
				    		}
				    	}
				    	update_difference();
				    	$("#folio_venta").val("");
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

	$("#codigo_barras").on("keypress", function(event){
		
		codigo = $(this).val();
		almacen = $("#almacen").val();
		if(event.keyCode == 13){
			if(almacen > 0){
				$.ajax({
				    // la URL para la petición
				    url : "obtener_producto",
				 
				    // especifica si será una petición POST o GET
				    type : "POST",

				    //datos enviados mediante post
				    data: { codigo_barras : codigo,
				    		id_almacen : almacen },

				    //especifica el tipo de dato que espera recibir
				    dataType: 'json',

				    // código a ejecutar si la petición es satisfactoria;
				    // la respuesta es pasada como argumento a la función
				    success : function(respuesta_producto) {
				    	if (respuesta_producto == null) {
				    		bootbox.alert('El código no existe favor de ingresarlo correctamente o su cantidad en inventario es 0.');
				    	}else{
				    		tr_current = $(".producto_" + respuesta_producto[0].id_producto);
				    		tr_count = $(tr_current).find(".talla_" + respuesta_producto[0].id_talla).length;

				    		if(tr_count > 0){
				    			cantidad_venta = parseInt($(tr_current).find(".talla_" + respuesta_producto[0].id_talla).parent().find('.cantidad_v').text()) + 1;
				    			cantidad_max = 0;

				    			for (i = 0; i < respuesta_producto.length ; i++) {
				    				if(respuesta_producto[i].id_tipo_movimiento == 1 || respuesta_producto[i].id_tipo_movimiento == 7 || respuesta_producto[i].id_tipo_movimiento == 8 || respuesta_producto[i].id_tipo_movimiento == 9){
				    					cantidad_max += parseInt(respuesta_producto[i].cantidad);
				    				}else{
				    					if(respuesta_producto[i].id_tipo_movimiento == 3 && respuesta_producto[i].confirmacion == -1){
				    						cantidad_max -= 0;
				    					}else{
				    						cantidad_max -= parseInt(respuesta_producto[i].cantidad);
				    					}
				    				}
				    			}

				    			if(cantidad_max >= cantidad_venta){
					    			td_current = $(tr_current).find(".talla_" + respuesta_producto[0].id_talla);
					    			
					    			if(td_current.length > 0){
					    				add_quantity($(td_current).parent(), 1);
					    			}else{
					    				tr_new = crea_tr(respuesta_producto);
					    				$("#tabla_cambios tbody").prepend(tr_new);
					    			}

					    			update_quantity("s", 1, "v");

					    		}else{
					    			bootbox.alert("La cantidad de salida no puede ser mayor a la cantidad en el inventario fisico del almacén.");
					    		}
				    		}else{
				    			c_max = obtener_cantidad_max(respuesta_producto);
				    			if(c_max >= 1){
					    			tr_new = crea_tr(respuesta_producto);
					    			$("#tabla_cambios tbody").prepend(tr_new);
					    			update_quantity("s", 1, "v");
					    		}else{
					    			bootbox.alert("La cantidad de salida no puede ser mayor a la cantidad en el inventario fisico del almacén.");
					    		}
				    		}
				    	}
				    	update_difference();
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

		almacen = $("#almacen").val();

		if(almacen > 0){
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
			    		bootbox.alert('Hubo un error al cargar las marcas, intente cerrando y abriendo la ventana de búsqueda por modelo.');
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
		almacen = $("#almacen").val();

		if(marca != 0 || modelo.trim() != ""){
			$.ajax({
			    // la URL para la petición
			    url : "obtener_producto_modelo/" + marca + "/" + modelo + "/" + almacen,
			 
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
		id_almacen = $("#almacen").val();
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

				tr_class = $("#tabla_cambios").find("tbody").find(".producto_" + productos[0].id_producto);
				td_current = $(tr_class).find(".talla_" + productos[0].id_talla);

				if(td_current.length > 0) {
					cantidad_sal = parseInt($(td_current).parent().find(".cantidad_v").text()) + parseInt(cantidad_sel);
					cantidad_max_sal = parseInt($(td_current).parent().find(".cant_max").val());

					if(cantidad_sal <= cantidad_max_sal){
						//add_quantity_prod(productos[0].id_producto, productos[0].id_talla, cantidad_sel);
						update_quantity("s", cantidad_sel, "v");
						add_quantity($(td_current).parent(), cantidad_sel);
					}else{
						bootbox.alert("La cantidad de salida no puede ser mayor a la cantidad en el inventario fisico del almacén.");
					}
				}else{
					obtener_cantidad_modelo(productos, tr_class, id_almacen, cantidad_sel, productos[0].id_talla);
				}
			}
		}
		update_difference();
		$(tbody).html("");
		$('#modelos_p').modal("hide");
	});

	$("#realizar").on("click", function(){
		var id_almacen = $("#almacen").val();
		var tbody_v = $("#tabla_folios").find("tbody");
		var count_tr_v = $(tbody_v).find("tr").length;
		var tbody_c = $("#tabla_cambios").find("tbody");
		var count_tr_c = $(tbody_c).find("tr").length;
		var diferencia = parseInt($("#diferencia").text());

		if(id_almacen != 0){
			if(count_tr_v > 1 && count_tr_c > 1){
				if(diferencia >= 0 && !isNaN(diferencia)){
					bootbox.confirm("Estás seguro de finalizar el cambio?", function(result) {
						if(result){
							get_values_change(tbody_v, tbody_c, id_almacen);
						}
					});
				}else{
					bootbox.alert("La diferencia debe ser mayor a 0.");
				}
			}else{
				bootbox.alert("Necesitas agregar productos para registrar un cambio.");
			}
		}else{
			bootbox.alert("Debes seleccionar el almacén donde se hará el cambio.");
			$("#almacen").focus();
		}
	});

	$("#cancelar").on("click", function(){
		var tbody_cambios = $("#tabla_cambios").find("tbody");
		var tbody_folios = $("#tabla_folios").find("tbody");
		var count_cambios = $(tbody_cambios).find("tr").length;
		var count_folios = $(tbody_folios).find("tr").length;

		if(count_cambios > 1 || count_folios > 1){
			bootbox.confirm("Estás seguro de cancelar el cambio?", function(result) {
				if(result){
					tbody_clean();
				}
			});
		}else{
			bootbox.alert("Necesitas agregar productos para cancelar un cambio.");
		}
	});

	$("#almacen").on("change", function(){
		tbody_clean();
	});
});

function get_values_change(tbody_v, tbody_c, id_almacen){
	var count_tr_v = $(tbody_v).find("tr").length;

	var change_v = new Object;
	var change_v_detail = new Array();

	change_v.cantidad = parseInt($("#total_vc").text());
	change_v.precio = parseInt($("#total_pc").text());

	for(i = 0 ; i < (count_tr_v - 1) ; i++){
		tr_id = $(tbody_v).find("tr").eq(i).attr("class");
		tr_id_producto = tr_id.split(" ");
		id_producto_v = tr_id_producto[1].split("_");

		tr_talla = $(tbody_v).find("tr").eq(i).find("td").eq(5).attr("class");
		tr_id_talla = tr_talla.split("_");
		id_talla_v = tr_id_talla[1];

		cantidad_v = $(tbody_v).find("tr").eq(i).find(".cantidad_c").text();

		precio_v = $(tbody_v).find("tr").eq(i).find(".precio_c").text();

		change_v_detail[i] = new Object;

		change_v_detail[i].id_producto = id_producto_v[1];
		change_v_detail[i].id_talla = id_talla_v;
		change_v_detail[i].cantidad = cantidad_v;
		change_v_detail[i].precio = precio_v;
	}

	var count_tr_c = $(tbody_c).find("tr").length;

	var change_c = new Object;
	var change_c_detail = new Array();

	change_c.cantidad = parseInt($("#total_vv").text());
	change_c.precio = parseInt($("#total_pv").text());

	for(i = 0 ; i < (count_tr_c - 1) ; i++){
		tr_id = $(tbody_c).find("tr").eq(i).attr("class");
		tr_id_producto = tr_id.split(" ");
		id_producto_c = tr_id_producto[1].split("_");

		tr_talla = $(tbody_c).find("tr").eq(i).find("td").eq(5).attr("class");
		tr_id_talla = tr_talla.split("_");
		id_talla_c = tr_id_talla[1];

		cantidad_c = $(tbody_c).find("tr").eq(i).find(".cantidad_v").text();

		precio_c = $(tbody_c).find("tr").eq(i).find(".precio_v").find("input").val();

		change_c_detail[i] = new Object;

		change_c_detail[i].id_producto = id_producto_c[1];
		change_c_detail[i].id_talla = id_talla_c;
		change_c_detail[i].cantidad = cantidad_c;
		change_c_detail[i].precio = precio_c;
	}

	send_values_change(change_v, change_v_detail, change_c, change_c_detail, id_almacen);
}

function send_values_change(change_v, change_v_detail, change_c, change_c_detail, id_almacen){
	$.ajax({
	    // la URL para la petición
	    url : "registrar_cambio",
	 
	    // especifica si será una petición POST o GET
	    type : "POST",

	    //datos pasados por el metodo post
	    data: { changev : change_v,
	    		changevdetail : change_v_detail,
	    		changec : change_c,
	    		changecdetail : change_c_detail,
	    		almacen : id_almacen },

	    //especifica el tipo de dato que espera recibir
	    dataType: 'json',

	    // código a ejecutar si la petición es satisfactoria;
	    // la respuesta es pasada como argumento a la función
	    success : function(change) {
	    	bootbox.alert(change.mensaje);
	    	if(change.resp == 't'){
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

function crea_tr_sales(sales){
	var precio_t = 0;
	var cantidad_t = 0;
	var html_tr = "";

	for (var i = 0; i < sales.length; i++) {
		html_tr += "<tr class='text-center producto_" + sales[i].id_producto + "'>";
		html_tr += 	"<td style='border: hidden; background-color: white;'><input class='movs' type='hidden' value='" + sales[i].id_movimiento + "' /></td>";
		html_tr += 	"<td class='cantidad_v'>" + sales[i].cantidad + "</td>";
		html_tr += 	"<td class='marca_v'>" + sales[i].marca + "</td>";
		html_tr += 	"<td class='modelo_v'>" + sales[i].modelo + "</td>";
		html_tr += 	"<td class='descripcion_v'>" + sales[i].descripcion + "</td>";
		html_tr += 	"<td class='talla_" + sales[i].id_talla + "'>" + sales[i].talla + "</td>";
		html_tr += 	"<td class='precio_v'>" + sales[i].precio + "</td>";
		html_tr += 	"<td>";
		html_tr +=		"<button type='button' class='btn btn-danger btn-sm' disabled='disabled'>";
		html_tr +=			"<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>";
		html_tr +=		"</button>";
		html_tr +=	"</td>";
		html_tr += "</tr>";

		cantidad_t += parseInt(sales[i].cantidad);
		precio_t += parseInt(sales[i].precio) * parseInt(sales[i].cantidad);
	}

	$("#total_vv").html(cantidad_t);
	$("#total_pv").html(precio_t);

	return html_tr;
}

function add_quantity(tr_current, quantity){
	td_cantidad = $(tr_current).find(".cantidad_v");
	quantity_current = parseInt($(td_cantidad).text());
	quantity_new = quantity_current + parseInt(quantity);
	$(tr_current).find("td.cantidad_v").text(quantity_new);
}

function crea_tr_venta(producto, folio){
	var cant_max = 0;
	var total_precio = 0;
	html_tr = "";

	for(var i = 0 ; i < producto.length ; i++){
		html_tr += "<tr class='text-center product_" + producto[i].id_producto + " " + folio + "'>";
		html_tr += 	"<td style='border: hidden; background-color: white;'></td>";
		html_tr += 	"<td class='cantidad_c'>" + producto[i].cantidad + "</td>";
		html_tr += 	"<td class='marca_c'>" + producto[i].marca + "</td>";
		html_tr += 	"<td class='modelo_c'>" + producto[i].modelo + "</td>";
		html_tr += 	"<td class='descripcion_c'>" + producto[i].descripcion + "</td>";
		html_tr += 	"<td class='talla_" + producto[i].id_talla + "'>" + producto[i].talla + "</td>";
		html_tr += 	"<td class='precio_c'>" + producto[i].precio + "</td>";
		html_tr += 	"<td>";
		html_tr +=		"<button type='button' class='btn btn-danger btn-sm' onclick='remove_tr(this, \"c\");'>";
		html_tr +=			"<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>";
		html_tr +=		"</button>";
		html_tr +=	"</td>";
		html_tr += "</tr>";

		cant_max += parseInt(producto[i].cantidad);
		total_precio += (parseInt(producto[i].cantidad) * parseInt(producto[i].precio));
	}

	$("#total_vc").html(cant_max);
	$("#total_pc").html(total_precio);

	return html_tr;
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
	html_tr += 	"<td class='precio_v'><input style='width:100%;' type='number' value='0' onkeyup='update_total_precio()'/></td>";
	html_tr += 	"<td>";
	html_tr +=		"<button type='button' class='btn btn-danger btn-sm' onclick='remove_tr(this, \"v\");'>";
	html_tr +=			"<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>";
	html_tr +=		"</button>";
	html_tr +=	"</td>";
	html_tr += "</tr>";

	return html_tr;
}

function update_total_precio(){
	tbody = $("#tabla_cambios").find("tbody");
	tr_current = $(tbody).find("tr");
	tr_count = tr_current.length;

	var quantity_new = 0;
	var quantity_current;

	for (var i = 0; i < tr_count - 1 ; i++){
		quantity_current = parseInt($(tr_current).eq(i).find(".cantidad_v").text()) * parseInt($(tr_current).eq(i).find(".precio_v").find("input").val());
		quantity_new += quantity_current;
	}

	$("#total_pv").text(quantity_new);

	update_difference();
}

function remove_tr(obj_button, tipo){
	tr_current = $(obj_button).parent().parent();
	quantity_current = parseInt($(tr_current).find(".cantidad_" + tipo).text());

	if(quantity_current > 1){
		ask_quantity(quantity_current, tr_current, tipo);
	}else{
		bootbox.confirm("Estás seguro de borrar el producto de la venta?", function(result) {
			if(result){
				$(tr_current).remove();
				update_quantity("r", 1, tipo);
				update_price(tr_current, tipo, 1);
			}
		});
	}	
}

function ask_quantity(quantity_current, tr_current, tipo){
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
							bootbox.alert('La cantidad a borrar debe ser menor o igual a la cantidad en la venta.');
						}else{
							quantity_new_ask = quantity_current - quantity_remove;
							if(quantity_new_ask == 0){
								$(tr_current).remove();
							}else{
								$(tr_current).find(".cantidad_" + tipo).text(quantity_new_ask);
							}

							update_quantity("r", quantity_remove, tipo);
							update_price(tr_current, tipo, quantity_remove);
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

function update_quantity(operation, quantity_remove, tipo){
	quantity_current = parseInt($("#total_v" + tipo).text());

	if (operation == "s"){
		quantity_new = quantity_current + parseInt(quantity_remove);
	}else{
		quantity_new = quantity_current - parseInt(quantity_remove);
	}
	
	$("#total_v" + tipo).text(quantity_new);
}

function update_price(tr_current, tipo, cant_delete){
	var precio_unit = 0;
	var precio = 0;
	var total_current = 0;
	var total_new = 0;

	if(tipo == "c"){
		precio_unit = parseInt($(tr_current).find(".precio_" + tipo).text());
	}else{
		precio_unit = parseInt($(tr_current).find(".precio_" + tipo).find("input").val());

		if(isNaN(precio_unit)){
			bootbox.alert("El precio debe ser un número entero.");
			return false;
		}
	}

	precio = precio_unit * cant_delete;
	total_current = parseInt($("#total_p" + tipo).text());
	total_new = total_current - precio;

	$("#total_p" + tipo).text(total_new);

	update_difference();
}

function update_difference(){
	var difference = 0;
	var total_v = parseInt($("#total_pv").text());
	var total_c = parseInt($("#total_pc").text());
	
	difference = total_v - total_c;
	$("#diferencia").text(difference);
}

function get_values_sale(tbody_sale, id_almacen){
	sale = new Object;
	sale_detail = new Array();

	sale.cantidad = parseInt($("#total_v").text());
	sale.id_almacen = id_almacen;
	sale.precio = parseInt($("#total_p").text());

	for(i = 0 ; i < (count_tr - 1) ; i++){
		tr_id = $(tbody_sale).find("tr").eq(i).attr("class");
		tr_id_producto = tr_id.split(" ");
		id_producto_tr = tr_id_producto[1].split("_");

		tr_talla = $(tbody_sale).find("tr").eq(i).find("td").eq(5).attr("class");
		tr_id_talla = tr_talla.split("_");
		id_talla_tr = tr_id_talla[1];

		cantidad = $(tbody_sale).find("tr").eq(i).find(".cantidad_v").text();

		precio = $(tbody_sale).find("tr").eq(i).find(".precio_v").find('input').val();

		if(precio.trim() == '0'){
			bootbox.alert('Todos los precios deben ser mayor a 0.');
			$(tbody_sale).find("tr").eq(i).find(".precio_v").find('input').focus();
			return false;
		}

		sale_detail[i] = new Object;

		sale_detail[i].id_producto = id_producto_tr[1];
		sale_detail[i].id_talla = id_talla_tr;
		sale_detail[i].cantidad = cantidad;
		sale_detail[i].precio = precio;
	}

	send_values_sale(sale, sale_detail);
}

function send_values_sale(sale, sale_detail){
	$.ajax({
	    // la URL para la petición
	    url : "registrar_venta",
	 
	    // especifica si será una petición POST o GET
	    type : "POST",

	    //datos enviados mediante post
	    data: { obj_sale : sale,
	    		obj_sale_detail: sale_detail },

	    //especifica el tipo de dato que espera recibir
	    dataType: 'html',

	    // código a ejecutar si la petición es satisfactoria;
	    // la respuesta es pasada como argumento a la función
	    success : function(respuesta_salida) {
	    	respuesta_s = respuesta_salida.split("|");
	    	bootbox.alert(respuesta_s[0]);

	    	if(respuesta_s[1] == 't'){
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

function obtener_cantidad_modelo(producto, tr_class, almacen, cantidad_sel, talla){
	$.ajax({
		url: "obtener_cantidad_modelo",
		type: "POST",
		data: { id_producto : producto[0].id_producto,
	    		id_almacen : almacen,
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
					html_tr +=		'<td class="precio_v"><input style="width:100%;" type="number" value="0" onkeyup="update_total_precio()"/></td>';
					html_tr += 	"<td>";
					html_tr +=		"<button type='button' class='btn btn-danger btn-sm' onclick='remove_tr(this);'>";
					html_tr +=			"<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>";
					html_tr +=		"</button>";
					html_tr +=	"</td>";
					html_tr += '</tr>';

					$("#tabla_cambios tbody").prepend(html_tr);

					update_quantity("s", cantidad_sel);
					add_quantity($(td_current).parent(), cantidad_sel);
		    	}else{
		    		bootbox.alert("La cantidad de salida no puede ser mayor a la cantidad en el inventario fisico del almacén.");
		    	}
		    }else{
		    	bootbox.alert("La cantidad del producto en el inventario fisico del almacén es igual a 0.");
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
		if(respuesta_modelo[i].id_tipo_movimiento == '1' || respuesta_modelo[i].id_tipo_movimiento == '7' || respuesta_modelo[i].id_tipo_movimiento == '8' || respuesta_modelo[i].id_tipo_movimiento == '9'){
			cant_max += parseInt(respuesta_modelo[i].cantidad);
		}else{
			if(respuesta_modelo[i].id_tipo_movimiento == '3' && respuesta_modelo[i].confirmacion == '-1'){
				cant_max -= 0;
			}else{
				cant_max -= parseInt(respuesta_modelo[i].cantidad);
			}
		}
	}

	return cant_max;
}

function tbody_clean(){
	var tbody_c = $("#tabla_cambios").find("tbody");
	tr_current_c = $(tbody_c).find("tr");
	tr_count_c = $(tr_current_c).length;

	for (i = 0 ; i < tr_count_c - 1 ; i++) {
		$(tr_current_c).eq(i).remove();
	}

	$("#total_vc").text("0");
	$("#total_pc").text("0");

	var tbody_v = $("#tabla_folios").find("tbody");
	tr_current_v = $(tbody_v).find("tr");
	tr_count_v = $(tr_current_v).length;

	for (i = 0 ; i < tr_count_v - 1 ; i++) {
		$(tr_current_v).eq(i).remove();
	}

	$("#total_vv").text("0");
	$("#total_pv").text("0");
}