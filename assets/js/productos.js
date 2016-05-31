/* Funciones de producto */
$(document).ready(function(){
	$(".editar_p").click(function(){
		if($(this).attr('class') == 'btn btn-info btn-sm editar_p'){
			tr_parent = $(this).parent().parent();
			num_td = tr_parent.find("td").length - 2;
			
			for (var i = 1; i < num_td; i++) {
				td_table = tr_parent.children("td").eq(i);
				if(i == 1){
					html_marcas = obtener_m();
					alert('Favor de actualizar el producto.');
					td_table.html(html_marcas);
				}else{
					td_valor = td_table.html();
					html_input = "<input type='text' size='10' class='input_req' onchange='validar(this)' value='" + td_valor + "' />";
					td_table.html(html_input);
				}
			}

			$(this).removeClass("btn-info editar_p").addClass("btn-success actualizar_p");
			$(this).find("span").removeClass("glyphicon-edit").addClass("glyphicon-ok");
		}else{
			actualizar_producto($(this));
		}
	});

	$(".input_req").click(function(){
		if($(this).val() == ""){
			$(this).parent().removeClass("check").addClass("no-check");
		}else{
			$(this).parent().removeClass("no-check").addClass("check");
		}
	});

	$(".borrar_p").click(function(){
		tr_parent = $(this).parent().parent();
		valor_tr = tr_parent.attr('id');;
		producto = valor_tr.split("_");
		bootbox.confirm("Seguro que desea borrar el producto?", function(result) {
			if(result){
				$.ajax({
				    // la URL para la petición
				    url : "borrar_producto",
				 
				    // especifica si será una petición POST o GET
				    type : "POST",

				    // envia los valores del form
				    data : { datos_p : producto[1] },

				    //especifica el tipo de dato que espera recibir
				    dataType: 'html',

				    // código a ejecutar si la petición es satisfactoria;
				    // la respuesta es pasada como argumento a la función
				    success : function(respuesta_borrar) {
				    	bootbox.alert(respuesta_borrar);
				    	location.href = "index";
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
	});

	$("#agregar_p").click(function(){
		tr_html = "<tr id='prodcuto'>";
		tr_html +=	"<td class='text-center no-item'></td>";
		tr_html +=	"<td class='text-center' id='marca'></td>";
		tr_html +=	"<td class='text-center'>";
		tr_html +=	"	<input class='input_req' value='' type='text' size='10'>";
		tr_html +=	"</td>";
		tr_html +=	"<td class='text-center'>";
		tr_html +=	"	<input class='input_req' value='' type='text' size='10'>";
		tr_html +=	"</td>";
		for(i = 1 ; i <= 26 ; i++){
			tr_html +=	"<td class='text-center no-check'>";
			tr_html +=		"<input class='input_req' onchange='validar(this)' type='text' size='10'>";
			tr_html +=	"</td>";
		}
		tr_html +=	"<td class='text-center'>";
		tr_html +=		"<button type='button' class='btn btn-success btn-sm insertar_p' onclick='insertar_producto(this)'>";
		tr_html +=			"<span class='glyphicon glyphicon-ok' aria-hidden='true'></span>";
		tr_html +=		"</button>";
		tr_html +=	"</td>";
		tr_html +=	"<td class='text-center'>";
		tr_html +=		"<button type='button' onclick='cancelar(this)' class='btn btn-danger btn-sm cancelar_p'>";
		tr_html +=			"<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>";
		tr_html +=		"</button>";
		tr_html +=	"</td>";
		tr_html += "</tr>";

		$("#tabla_productos > tbody").append(tr_html);

		$.ajax({
		    // la URL para la petición
		    url : "obtener_marcas",
		 
		    // especifica si será una petición POST o GET
		    type : "POST",

		    //especifica el tipo de dato que espera recibir
		    dataType: 'json',

		    // código a ejecutar si la petición es satisfactoria;
		    // la respuesta es pasada como argumento a la función
		    success : function(respuesta) {
		    	html_marcas = "<select class='select_marcas' onchange='valida_opcion(this)'>";
		    	for (i = 0 ; i < respuesta.length ; i++) {
		    		html_marcas += "<option value='" + respuesta[i].id_marca + "'>" + respuesta[i].marca + "</option>";
		    	}
		    	html_marcas += "<option value='t'>OTRO...</option>";
		    	html_marcas += "</select>";
		    	$("#marca").html(html_marcas);
		    },
		 
		    // código a ejecutar si la petición falla;
		    // son pasados como argumentos a la función
		    // el objeto de la petición en crudo y código de estatus de la petición
		    error : function(xhr, status) {
		        bootbox.alert('Disculpe, existió un problema');
		    }
		});
	});

	$(".i-codigo").on("change", function(){
		input_codigo = $(this).find("input");
		codigo = input_codigo.val();
		$.ajax({
		    // la URL para la petición
		    url : "obtener_codigo",
		 
		    // especifica si será una petición POST o GET
		    type : "POST",

		    //especifica el tipo de dato que espera recibir
		    dataType: 'json',

		    //datos pasados por metodo post
		    data: { d_codigo : codigo },

		    // código a ejecutar si la petición es satisfactoria;
		    // la respuesta es pasada como argumento a la función
		    success : function(respuesta) {
		    	if(respuesta != null){
		    		alert(respuesta[0].marca + "-" + respuesta[0].modelo);
		    		input_codigo.val("").focus();
		    	}
		    },
		 
		    // código a ejecutar si la petición falla;
		    // son pasados como argumentos a la función
		    // el objeto de la petición en crudo y código de estatus de la petición
		    error : function(xhr, status) {
		        bootbox.alert('Disculpe, existió un problema');
		    }
		});
	});
});

function valida_opcion(obj_select){
	if($(obj_select).val() == 't'){
		input_marca = "<input type='text' name='marca' class='marca_input'>";
		$(obj_select).parent().html(input_marca);
	}
}

function actualizar_producto(obj_boton){
	tr_parent = $(obj_boton).parent().parent();
	num_td = tr_parent.find("td").length - 2;
	id_producto = tr_parent.attr("id").split("-");
	datos_producto = id_producto;
	actualizar = true;
	
	for (var i = 1; i < num_td; i++) {
		td_table = tr_parent.children("td").eq(i);
		etiqueta = td_table.children().prop("tagName").toLowerCase();
		
		if(etiqueta == "select"){
			datos_producto += "-" + td_table.find("select").val() + 'select';
		}else if(etiqueta == "input"){
			if(td_table.find("input.marca").val() == "" || td_table.find("input.modelo").val() == "" || td_table.find("input.descripcion").val() == "" || td_table.find("input.precio").val() == ""){
				td_table.find("input").focus();
				actualizar = false;
				bootbox.alert("El campo Marca, Modelo, Descripción y precio no pueden estar vacíos.");
				return false;
			}else{
				valor_input = td_table.find("input").val();
				datos_producto += "-" + valor_input;
			}
		}
	}

	if(actualizar){
		$.ajax({
		    // la URL para la petición
		    url : "actualizar_producto",
		 
		    // especifica si será una petición POST o GET
		    type : "POST",

		    // envia los valores del form
		    data : { datos_p : datos_producto },

		    //especifica el tipo de dato que espera recibir
		    dataType: 'html',

		    // código a ejecutar si la petición es satisfactoria;
		    // la respuesta es pasada como argumento a la función
		    success : function(respuesta_actualizar) {
		    	$(".modal-title").html("INFO");
		    	$(".modal-body").find("p").html(respuesta_actualizar);
		    	$('#info').modal();
		    	location.href = "index";
		    },
		 
		    // código a ejecutar si la petición falla;
		    // son pasados como argumentos a la función
		    // el objeto de la petición en crudo y código de estatus de la petición
		    error : function(xhr, status) {
		        bootbox.alert('Disculpe, existió un problema');
		    }
		});
	}
}

function insertar_producto(obj_boton){
	tr_parent = $(obj_boton).parent().parent();
	num_td = tr_parent.find("td").length - 2;
	datos_producto = "";
	insertar = true;
	
	for (var i = 1; i < num_td; i++) {
		td_table = tr_parent.children("td").eq(i);
		etiqueta = td_table.children().prop("tagName").toLowerCase();
		
		if(etiqueta == "select"){
			datos_producto += td_table.find("select").val() + 'select';
		}else if(etiqueta == "input"){
			if(td_table.find("input.marca").val() == "" || td_table.find("input.modelo").val() == "" || td_table.find("input.descripcion").val() == "" || td_table.find("input.precio").val() == ""){
				td_table.find("input").focus();
				actualizar = false;
				bootbox.alert("El campo Marca, Modelo, Descripción y precio no pueden estar vacíos.");
				return false;
			}else{
				valor_input = td_table.find("input").val();
				if (i == 1) {
					datos_producto += valor_input;
				}else{
					datos_producto += "-" + valor_input;
				}
			}
		}
	}alert(datos_producto);

	if(insertar){
		$.ajax({
		    // la URL para la petición
		    url : "insertar_producto",
		 
		    // especifica si será una petición POST o GET
		    type : "POST",

		    // envia los valores del form
		    data : { datos_p : datos_producto },

		    //especifica el tipo de dato que espera recibir
		    dataType: 'html',

		    // código a ejecutar si la petición es satisfactoria;
		    // la respuesta es pasada como argumento a la función
		    success : function(respuesta_actualizar) {
		    	alert(respuesta_actualizar);
		    	location.href = "index";
		    },
		 
		    // código a ejecutar si la petición falla;
		    // son pasados como argumentos a la función
		    // el objeto de la petición en crudo y código de estatus de la petición
		    error : function(xhr, status) {
		        bootbox.alert('Disculpe, existió un problema');
		    }
		});
	}
}

function obtener_m(){
	$.ajax({
	    // la URL para la petición
	    url : "obtener_marcas",
	 
	    // especifica si será una petición POST o GET
	    type : "POST",

	    //especifica el tipo de dato que espera recibir
	    dataType: 'json',

	    // código a ejecutar si la petición es satisfactoria;
	    // la respuesta es pasada como argumento a la función
	    success : function(respuesta) {
	    	html_marcas = "<select class='select_marcas' onchange='valida_opcion(this)'>";
	    	for (i = 0 ; i < respuesta.length ; i++) {
	    		html_marcas += "<option value='" + respuesta[i].id_marca + "'>" + respuesta[i].marca + "</option>";
	    	}
	    	html_marcas += "<option value='t'>OTRO...</option>";
	    	html_marcas += "</select>";
	    	return html_marcas;
	    },
	 
	    // código a ejecutar si la petición falla;
	    // son pasados como argumentos a la función
	    // el objeto de la petición en crudo y código de estatus de la petición
	    error : function(xhr, status) {
	        bootbox.alert('Disculpe, existió un problema');
	    }
	});
}

function cancelar(obj_button){
	bootbox.confirm("Seguro que desea borrar el producto?", function(result) {
		if(result){
			$(obj_button).parent().parent().remove();
		}
	});
}

function validar(obj_check){
	if($(obj_check).val() == ""){
		$(obj_check).parent().removeClass("check").addClass("no-check");
	}else{
		$(obj_check).parent().removeClass("no-check").addClass("check");
	}
}