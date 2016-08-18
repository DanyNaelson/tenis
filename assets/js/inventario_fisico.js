/* Funciones de producto */
$(document).ready(function(){

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
				    			cantidad_sal = parseInt($(tr_current).find(".talla_" + respuesta_producto[0].id_talla).next().text()) + 1;
				    			cantidad_max = 0;

				    			for (i = 0; i < respuesta_producto.length ; i++) {
				    				if(respuesta_producto[i].id_tipo_movimiento == 1 || respuesta_producto[i].id_tipo_movimiento == 7 || respuesta_producto[i].id_tipo_movimiento == 8 || respuesta_producto[i].id_tipo_movimiento == 9){
				    					if(respuesta_producto[i].confirmacion == 1){
				    						cantidad_max += parseInt(respuesta_producto[i].cantidad);
				    					}
				    				}else{
				    					if(respuesta_producto[i].id_tipo_movimiento == 3 && respuesta_producto[i].confirmacion == -1){
				    						cantidad_max -= 0;
				    					}else{
				    						if(respuesta_producto[i].confirmacion == 1){
				    							cantidad_max -= parseInt(respuesta_producto[i].cantidad);
				    						}
				    					}
				    				}
				    			}

				    			//if(cantidad_max >= cantidad_sal){
					    			td_current = $(tr_current).find(".talla_" + respuesta_producto[0].id_talla);
					    			
					    			if(td_current.length > 0){
					    				add_quantity($(td_current).parent(), 1);
					    			}else{
					    				tr_new = crea_tr(respuesta_producto);
					    				$("#tabla_salidas tbody").prepend(tr_new);
					    			}

					    			update_quantity("s", 1);
					    			tr_current_p = $(".prod_" + respuesta_producto[0].id_producto).length;

						    		if(tr_current_p > 0){
						    			add_quantity_prod(respuesta_producto[0].id_producto, respuesta_producto[0].id_talla, 1);
						    		}else{
										obtener_talla_cantidad(respuesta_producto, 1);
						    		}

					    		/*}else{
					    			bootbox.alert("La cantidad de salida no puede ser mayor a la cantidad en el inventario fisico del almacén.");
					    		}*/
				    		}else{
				    			c_max = obtener_cantidad_max(respuesta_producto);
				    			//if(c_max >= 1){
					    			tr_new = crea_tr(respuesta_producto);
					    			$("#tabla_salidas tbody").prepend(tr_new);
					    			update_quantity("s", 1);

					    			tr_current_p = $(".prod_" + respuesta_producto[0].id_producto).length;

						    		if(tr_current_p > 0){
						    			add_quantity_prod(respuesta_producto[0].id_producto, respuesta_producto[0].id_talla, 1);
						    		}else{
										obtener_talla_cantidad(respuesta_producto, 1);
						    		}
					    		/*}else{
					    			bootbox.alert("La cantidad de salida no puede ser mayor a la cantidad en el inventario fisico del almacén.");
					    		}*/
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
							$("#tabla_modelos").find(".tallas_s").load( "obtener_tallas_select", function( response, status, xhr ) {
								if ( status == "error" ) {
									var msg = "Sorry but there was an error: ";
									$( "#error" ).html( msg + xhr.status + " " + xhr.statusText );
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

				tr_class = $("#tabla_salidas").find("tbody").find(".producto_" + productos[0].id_producto);
				td_current = $(tr_class).find(".talla_" + productos[0].id_talla);

				if(td_current.length > 0) {
					cantidad_sal = parseInt($(".producto_" + productos[0].id_producto).find(".cantidad").text()) + parseInt(cantidad_sel);
					cantidad_max_sal = parseInt($(".prod_" + productos[0].id_producto).find(".tallaid_" + productos[0].id_talla).text()) + parseInt($(".producto_" + productos[0].id_producto).find(".cantidad").text());

					//if(cantidad_sal <= cantidad_max_sal){
						add_quantity_prod(productos[0].id_producto, productos[0].id_talla, cantidad_sel);
						update_quantity("s", cantidad_sel);
						add_quantity($(td_current).parent(), cantidad_sel);
					/*}else{
						bootbox.alert("La cantidad de salida no puede ser mayor a la cantidad en el inventario fisico del almacén.");
					}*/
				}else{
					obtener_cantidad_modelo(productos, tr_class, id_almacen, cantidad_sel, productos[0].id_talla);
				}
			}
		}

		$(tbody).html("");
		$('#modelos_p').modal("hide");
	});

	$("#finalizar").on("click", function(){
		tbody = $("#tabla_salidas").find("tbody");
		id_almacen = $("#almacen").val();
		count_tr = $(tbody).find("tr").length;

		if(id_almacen != 0){
			if(count_tr > 1){
				bootbox.confirm("Estás seguro de finalizar la salida?", function(result) {
					if(result){
						get_values_outlet(tbody);
					}
				});
			}else{
				bootbox.alert("Necesitas agregar productos para registrar una salida.");
			}
		}else{
			bootbox.alert("Debes seleccionar el almacén donde se hará la salida.");
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
			bootbox.confirm("Estás seguro de cancelar la salida?", function(result) {
				if(result){
					tbody_clean();
				}
			});
		}else{
			bootbox.alert("Necesitas agregar productos para cancelar una salida.");
		}
	});

	$("#almacen").on("change", function(){
		tbody_clean();
	});
});

function add_quantity(tr_current, quantity){
	td_cantidad = $(tr_current).find(".cantidad");
	quantity_current = parseInt($(td_cantidad).text());
	quantity_new = quantity_current + parseInt(quantity);
	$(tr_current).find("td.cantidad").text(quantity_new);
}

function add_quantity_prod(id_prod, id_talla, quantity){
	quantity_current = parseInt($(".prod_" + id_prod).find(".tallaid_" + id_talla).text());
	
	quantity_new = quantity_current - parseInt(quantity);
	$(".prod_" + id_prod).find(".tallaid_" + id_talla).text(quantity_new).css("background-color", "greenyellow");
}

function remove_quantity_prod(tr_current, quantity){
	classP = $(tr_current).attr("class").split(" ");
	id_prod = classP[1].split("_");
	classT = $(tr_current).find("TD").eq(3).attr("class");
	id_talla = classT.split("_");
	quantity_current = parseInt($(".prod_" + id_prod[1]).find(".tallaid_" + id_talla[1]).text());
	
	quantity_new = quantity_current + quantity;

	if(quantity_new > 0){
		$(".prod_" + id_prod[1]).find(".tallaid_" + id_talla[1]).text(quantity_new).css("background-color", "greenyellow");
	}else{
		$(".prod_" + id_prod[1]).find(".tallaid_" + id_talla[1]).text(quantity_new).css("background-color", "white");
	}

	return id_prod[1];
}

function crea_tr(producto){
	html_tr = "<tr class='text-center producto_" + producto[0].id_producto + "'>";
	html_tr += 	"<td class='marca'>" + producto[0].marca + "</td>";
	html_tr += 	"<td class='modelo'>" + producto[0].modelo + "</td>";
	html_tr += 	"<td class='descripcion'>" + producto[0].descripcion + "</td>";
	html_tr += 	"<td class='talla_" + producto[0].id_talla + "'>" + producto[0].talla + "</td>";
	html_tr += 	"<td class='cantidad'>1</td>";
	html_tr += 	"<td>";
	html_tr +=		"<button type='button' class='btn btn-danger btn-sm' onclick='remove_tr(this);'>";
	html_tr +=			"<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>";
	html_tr +=		"</button>";
	html_tr +=	"</td>";
	html_tr += "</tr>";

	return html_tr;
}

function remove_tr(obj_button){
	tr_current = $(obj_button).parent().parent();

	if($(tr_current).find(".cantidad").children().prop("tagName") == 'INPUT'){
		quantity_current = 1;
	}else{
		quantity_current = parseInt($(tr_current).find(".cantidad").text());
	}

	if(quantity_current > 1){
		ask_quantity(quantity_current, tr_current);
	}else{
		bootbox.confirm("Estás seguro de borrar el producto de la salida?", function(result) {
			if(result){
				id_prod = remove_quantity_prod(tr_current, 1);
				$(tr_current).remove();
				remove_tr_prod(id_prod);
				update_quantity("r", 1);
			}
		});
	}
	
}

function remove_tr_prod(id_prod){
	count_class = $("#tabla_salidas").find(".producto_" + id_prod).length;

	if(count_class == 0){
		$(".producto_" + id_prod[1]).find(".talla_" + id_talla[1]).css("background-color", "white");
		$(".prod_" + id_prod).remove();
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
							bootbox.alert('La cantidad a borrar debe ser menor o igual a la cantidad en la salida.');
						}else{
							quantity_new_ask = quantity_current - quantity_remove;
							if(quantity_new_ask == 0){
								id_prod = remove_quantity_prod(tr_current, quantity_remove);
								$(tr_current).remove();
								remove_tr_prod(id_prod);
							}else{
								id_prod = remove_quantity_prod(tr_current, quantity_remove);
								$(tr_current).find(".cantidad").text(quantity_new_ask);
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
	quantity_current = parseInt($("#total_s").text());

	if (operation == "s"){
		quantity_new = quantity_current + parseInt(quantity_remove);
	}else{
		quantity_new = quantity_current - parseInt(quantity_remove);
	}
	
	$("#total_s").text(quantity_new);
}

function crea_tr_prod(producto, talla_cantidad){
	html_productos = "<tr class='prod_" + producto[0].id_producto + " text-center'>";
	html_productos += 	"<td class='marc'>" + producto[0].marca + "</td>";
	html_productos += 	"<td class='mod'>" + producto[0].modelo + "</td>";
	html_productos += 	"<td class='desc'>" + producto[0].descripcion + "</td>";
	cont = 0;
	for (var talla in talla_cantidad) {
		cont++;
		html_productos += "<td class='tallaid_" + cont + "'>" + talla_cantidad[talla].cantidad + "</td>";
	}

	html_productos += "</tr>";

	return html_productos;
}

function obtener_talla_cantidad(producto, cantidad){
	almacen = $("#almacen").val();

	$.ajax({
	    // la URL para la petición
	    url : "obtener_talla_cantidad",
	 
	    // especifica si será una petición POST o GET
	    type : "POST",

	    //datos pasados por el metodo post
	    data: { id_prod : producto[0].id_producto,
	    		id_talla : producto[0].id_talla,
	    		id_almacen : almacen },

	    //especifica el tipo de dato que espera recibir
	    dataType: 'json',

	    // código a ejecutar si la petición es satisfactoria;
	    // la respuesta es pasada como argumento a la función
	    success : function(talla_cantidad) {
	    	tr_new_prod = crea_tr_prod(producto, talla_cantidad);
			$("#tabla_productos tbody").prepend(tr_new_prod);
			$(".prod_" + producto[0].id_producto).find(".tallaid_" + producto[0].id_talla).css("background-color", "greenyellow");
			cant = parseInt(cantidad);

			if(cant > 0){
				current_q = $(".prod_" + producto[0].id_producto).find(".tallaid_" + producto[0].id_talla).text();
				new_q = parseInt(current_q) - cant;
				$(".prod_" + producto[0].id_producto).find(".tallaid_" + producto[0].id_talla).text(new_q);
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

function get_values_outlet(tbody_outlet){
	outlet = new Object;
	outlet_detail = new Array();

	outlet.cantidad = parseInt($("#total_s").text());
	outlet.id_almacen = id_almacen;

	for(i = 0 ; i < (count_tr - 1) ; i++){
		tr_id = $(tbody_outlet).find("tr").eq(i).attr("class");
		tr_id_producto = tr_id.split(" ");
		id_producto_tr = tr_id_producto[1].split("_");

		tr_talla = $(tbody_outlet).find("tr").eq(i).find("td").eq(3).attr("class");
		tr_id_talla = tr_talla.split("_");
		id_talla_tr = tr_id_talla[1];

		cantidad = $(tbody_outlet).find("tr").eq(i).find(".cantidad").text();

		outlet_detail[i] = new Object;

		outlet_detail[i].id_producto = id_producto_tr[1];
		outlet_detail[i].id_talla = id_talla_tr;
		outlet_detail[i].cantidad = cantidad;
	}

	send_values_outlet(outlet, outlet_detail);
}

function send_values_outlet(outlet, outlet_detail){
	$.ajax({
	    // la URL para la petición
	    url : "registrar_salida",
	 
	    // especifica si será una petición POST o GET
	    type : "POST",

	    //datos enviados mediante post
	    data: { obj_outlet : outlet,
	    		obj_outlet_detail: outlet_detail },

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
		    	//if(cantidad_sel <= cantidad_max){
		    		html_tr = '<tr class="text-center producto_' + productos[0].id_producto + '">';
					html_tr += 		'<td class="marca">' + productos[0].marca + '</td>';
					html_tr += 		'<td class="modelo">' + productos[0].modelo + '</td>';
					html_tr += 		'<td class="descripcion">' + productos[0].descripcion + '</td>';
					html_tr +=		'<td class="talla_' + productos[0].id_talla + '">' + productos[0].talla + '</td>';
					html_tr +=		'<td class="cantidad">' + cantidad_sel + '</td>';
					html_tr += 	"<td>";
					html_tr +=		"<button type='button' class='btn btn-danger btn-sm' onclick='remove_tr(this);'>";
					html_tr +=			"<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>";
					html_tr +=		"</button>";
					html_tr +=	"</td>";
					html_tr += '</tr>';

					$("#tabla_salidas tbody").prepend(html_tr);

					if(tr_class.length == 0){
						obtener_talla_cantidad(productos, cantidad_sel);
					}

					add_quantity_prod(productos[0].id_producto, productos[0].id_talla, cantidad_sel);
					update_quantity("s", cantidad_sel);
					add_quantity($(td_current).parent(), cantidad_sel);
		    	/*}else{
		    		bootbox.alert("La cantidad de salida no puede ser mayor a la cantidad en el inventario fisico del almacén.");
		    	}*/
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
			if(respuesta_modelo[i].confirmacion == '1'){
				cant_max += parseInt(respuesta_modelo[i].cantidad);
			}
		}else{
			if(respuesta_modelo[i].id_tipo_movimiento == '3' && respuesta_modelo[i].confirmacion == '-1'){
				cant_max -= 0;
			}else{
				if(respuesta_modelo[i].confirmacion == '1'){
					cant_max -= parseInt(respuesta_modelo[i].cantidad);
				}
			}
		}
	}

	return cant_max;
}

function tbody_clean(){
	tbody_s = $("#tabla_salidas").find("tbody");
	tr_current = $(tbody_s).find("tr");
	tr_count = $(tr_current).length;

	for (i = 0 ; i < tr_count - 1 ; i++) {
		$(tr_current).eq(i).remove();
	}

	$("#total_s").text("0");

	tbody_p = $("#tabla_productos").find("tbody");
	tr_current_p = $(tbody_p).find("tr");
	tr_count_p = $(tr_current_p).length;

	for (i = 0 ; i < tr_count_p - 1 ; i++) {
		$(tr_current_p).eq(i).remove();
	}

	$("#tr_tallas").find("td.tallas_c").text("0");	
}