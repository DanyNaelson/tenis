/* Funciones de producto */
$(document).ready(function(){

	$.ajax({
	    // la URL para la petición
	    url : "obtener_marcas/t",
	 
	    // especifica si será una petición POST o GET
	    type : "POST",

	    //especifica el tipo de dato que espera recibir
	    dataType: 'html',

	    // código a ejecutar si la petición es satisfactoria;
	    // la respuesta es pasada como argumento a la función
	    success : function(respuesta_marcas) {
	    	$("#marca_select").append(respuesta_marcas);
	    },
	 
	    // código a ejecutar si la petición falla;
	    // son pasados como argumentos a la función
	    // el objeto de la petición en crudo y código de estatus de la petición
	    error : function(xhr, status) {
	        bootbox.alert('Disculpe, existió un problema');
	    }
	});

	$("#buscar").on("submit", function(e){
		e.preventDefault();
		form_productos = $(this);
		if($("#codigo_barras").val() != ""){
			$("#marca_select option").each(function(index){
				if(index == '0'){
					$(this).prop("selected", true);
				}else{
					$(this).prop("selected", false);
				}
			});
			$("#modelo").val("");
		}

		talla_sel = $("#tallas").val();

		$.ajax({
		    // la URL para la petición
		    url : "busqueda_producto",
		 
		    // especifica si será una petición POST o GET
		    type : "POST",

		    // envia los valores del form
		    data : form_productos.serialize(),

		    //especifica el tipo de dato que espera recibir
		    dataType: 'html',

		    // código a ejecutar si la petición es satisfactoria;
		    // la respuesta es pasada como argumento a la función
		    success : function(respuesta_busqueda) {
		    	respuesta_busqueda = respuesta_busqueda.split("|||");
	    		$("#tabla_productos").html(respuesta_busqueda[0]);
	    		$("#pags").html(respuesta_busqueda[1]);
	    		if($("#codigo_barras").val() != ""){
		    		for (i = 4; i <= 29; i++) {
		    			td_current = $("#tabla_productos").find("tr").find("td").eq(i);
		    			if(td_current.html() == $("#codigo_barras").val()){
		    				td_current.css({ 'background-color' : 'lightgreen', 'color' : 'blue' });
		    			}
		    		}
		    	}

		    	if (talla_sel != '0'){
			    	tr_table = $("#tabla_productos").find("tr");

			    	for(j = 0 ; j <= tr_table.length ; j++){
			    		tr_parent = tr_table.eq(j);
				    	for (k = 4 ; k < 29 ; k++) {
				    		td_current = tr_parent.children().eq(k);

				    		if((k - 3) != talla_sel){
				    			td_current.css('display', 'none');
				    		}
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
	});

	$(".editar_p").click(function(){
		editar_p($(this));
	});

	$(".input_req").click(function(){
		if($(this).val() == ""){
			$(this).parent().removeClass("check").addClass("no-check");
		}else{
			$(this).parent().removeClass("no-check").addClass("check");
		}
	});

	$(".borrar_p").click(function(){
		borrar_p($(this));
	});

	$("#agregar_p").click(function(){
		$("#tabla_productos").find($("td, th")).css("display", "");

		tr_html = "<tr id='producto' style='background: lightgray;'>";
		tr_html +=	"<td class='text-center no-item'></td>";
		tr_html +=	"<td class='text-center' id='marca'></td>";
		tr_html +=	"<td class='text-center' onchange='validar_modelo(this)'>";
		tr_html +=	"	<input class='input_req' value='' type='text' size='10'>";
		tr_html +=	"</td>";
		tr_html +=	"<td class='text-center'>";
		tr_html +=	"	<input class='input_req' value='' type='text' size='10'>";
		tr_html +=	"</td>";
		for(i = 1 ; i <= 26 ; i++){
			tr_html +=	"<td class='text-center no-check' onchange='validar_codigo(this)' style='background: lightgray;'>";
			tr_html +=		"<input class='input_req' onchange='validar(this)' onkeypress='enter_tab(event, this)' type='text' size='10'>";
			tr_html +=	"</td>";
		}
		tr_html +=	"<td class='text-center no-check' style='background: lightgray;'>";
		tr_html +=		"<input class='input_req' onchange='validar(this)' type='text' size='10'>";
		tr_html +=	"</td>";
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
		    	html_marcas = "<select class='select_marcas form-control' onchange='valida_opcion(this)'>";
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
		validar_codigo($(this));
	});

	$("#crear_cb").on("click", function(){
		bootbox.prompt("Ingresar código de barras: ", function(result) {                
		  	if (result !== null) {                                             
		    	$.ajax({
				    // la URL para la petición
				    url : "/inventarios/barcode",
				 
				    // especifica si será una petición POST o GET
				    type : "POST",

				    //datos pasados por metodo post
				    data: { codigo : result },

				    //especifica el tipo de dato que espera recibir
				    dataType: 'html',

				    // código a ejecutar si la petición es satisfactoria;
				    // la respuesta es pasada como argumento a la función
				    success : function(respuesta_codigo) {
				    	bootbox.alert(respuesta_codigo);
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
});

function enter_tab(event, obj_input){
	if(event.keyCode == 13){
		$(obj_input).parent().next("td").children("input").focus();
	}
}

function valida_opcion(obj_select){
	if($(obj_select).val() == 't'){
		input_marca = "<input type='text' name='marca' onchange='validar_marca(this)' class='marca_input'>";
		$(obj_select).parent().html(input_marca);
	}
}

function validar_codigo(obj_td){
	input_codigo = $(obj_td).find("input");
	codigo = input_codigo.val();
	if(codigo != ""){
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
		    		$(input_codigo).tooltip({title: "<p>YA EXISTE EL CODIGO: </p><p>Marca: " + respuesta[0].marca + "</p><p>Modelo: " + respuesta[0].modelo + "</p>", html: true, placement: "bottom", trigger: "focus"});
		    		input_codigo.val("").focus();
		    	}else{
		    		tr_parent = $(obj_td).parent();
		    		num_td = tr_parent.find("td").length;
		    		codigos = 0;
		    		for (i = 4; i <= num_td - 2; i++) {
		    			td_current = tr_parent.find("td").eq(i);
		    			if(codigo == $(td_current).find("input").val()){
		    				codigos++;
		    			}
		    		}
		    		if (codigos > 1){
    					$(input_codigo).tooltip({title: "<p>YA EXISTE EL CODIGO EN ESTE MISMO MODELO.</p>", html: true, placement: "bottom", trigger: "focus"});
    		input_codigo.val("").focus();
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
}

function validar_marca(object_input){
	marca = $(object_input).val();
	if(marca != ""){
		$.ajax({
		    // la URL para la petición
		    url : "validar_marca",
		 
		    // especifica si será una petición POST o GET
		    type : "POST",

		    // envia los valores del form
		    data : { p_marca : marca },

		    //especifica el tipo de dato que espera recibir
		    dataType: 'json',

		    // código a ejecutar si la petición es satisfactoria;
		    // la respuesta es pasada como argumento a la función
		    success : function(respuesta) {
		    	if(respuesta != null){
		    		obtener_m("marca_" + respuesta[0].id_marca, $(object_input).parent());
		    		bootbox.alert('La marca ya existe.');
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
		obtener_m("marca_1", $(object_input).parent());
	}
}

function validar_modelo(object_td){
	input_modelo = $(object_td).find("input");
	modelo = input_modelo.val();
	$.ajax({
	    // la URL para la petición
	    url : "validar_modelo",
	 
	    // especifica si será una petición POST o GET
	    type : "POST",

	    //especifica el tipo de dato que espera recibir
	    dataType: 'json',

	    //datos pasados por metodo post
	    data: { p_modelo : modelo },

	    // código a ejecutar si la petición es satisfactoria;
	    // la respuesta es pasada como argumento a la función
	    success : function(respuesta) {
	    	if(respuesta != null){
	    		$(input_modelo).tooltip({title: "<p>YA EXISTE EL MODELO EN: </p><p>Marca: " + respuesta[0].marca + "</p><p>Modelo: " + respuesta[0].modelo + "</p></p>", html: true, placement: "bottom", trigger: "focus"});
	    		input_modelo.val("").focus();
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
			datos_producto += "|" + td_table.find("select").val() + 'select';
		}else if(etiqueta == "input"){
			if(tr_parent.children("td").eq(1).find("input").val() == "" || tr_parent.children("td").eq(2).find("input").val() == "" || tr_parent.children("td").eq(3).find("input").val() == ""){
				td_table.find("input").focus();
				actualizar = false;
				bootbox.alert("El campo Marca, Modelo, Descripción y precio no pueden estar vacíos.");
				return false;
			}else{
				valor_input = td_table.find("input").val();
				datos_producto += "|" + valor_input;
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
		    	mensaje = respuesta_actualizar.split("|");
		    	bootbox.alert(mensaje[0], function() {
		    		if (mensaje[1] == 't') {
		    			tr_parent.find(".actualizar_p").removeClass("btn btn-success btn-sm actualizar_p").addClass("btn btn-info btn-sm editar_p");
						tr_parent.find(".editar_p").find("span").removeClass("glyphicon-ok").addClass("glyphicon-edit");
		    			tr_parent.css('background', 'none');
		    			td_count = tr_parent.find("td").length -2;

		    			for (i = 1 ; i < td_count ; i++) {
		    				td_current = tr_parent.find("td").eq(i);
		    				if(td_current.children().prop("tagName").toLowerCase() == 'select'){
		    					valor_td = td_current.children().find("option:selected").text();
		    				}else{
		    					valor_td = td_current.children().val();
		    				}
		    				td_current.html(valor_td);
		    				validar_td(td_current, i);
		    			}
		    		}
				});
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

function validar_td(td_current, i){
	if(i >= 4 && i <= 29){
		if(valor_td == "") {
			td_current.css("background-color", "#f2dede");
		}else{
			td_current.css("background-color", "#dff0d8");
		}
	}else{
		td_current.css('background', 'none');
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
			if(tr_parent.children("td").eq(1).find("input").val() == "" || tr_parent.children("td").eq(2).find("input").val() == "" || tr_parent.children("td").eq(3).find("input").val() == ""){
				td_table.find("input").focus();
				actualizar = false;
				bootbox.alert("El campo Marca, Modelo, Descripción y precio no pueden estar vacíos.");
				return false;
			}else{
				valor_input = td_table.find("input").val();
				if (i == 1) {
					datos_producto += valor_input;
				}else{
					datos_producto += "|" + valor_input;
				}
			}
		}
	}

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
		    success : function(respuesta_insertar) {
		    	mensaje = respuesta_insertar.split("|");
		    	bootbox.alert(mensaje[0], function() {
		    		actualizar_tr(tr_parent, mensaje[1]);
				});
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

function actualizar_tr(tr_parent, mensaje){
	if (mensaje == 't') {
		tr_parent.find(".insertar_p").removeClass("btn btn-success btn-sm insertar_p").addClass("btn btn-info btn-sm editar_p");
		tr_parent.find(".editar_p").find("span").removeClass("glyphicon-ok").addClass("glyphicon-edit");
		tr_parent.find(".editar_p").attr("onclick", "");
		tr_parent.find(".editar_p").on("click", function(){
			editar_p($(this));
		});
		tr_parent.css('background', 'none');
		td_count = tr_parent.find("td").length -2;

		for (i = 1 ; i < td_count ; i++) {
			td_current = tr_parent.find("td").eq(i);
			
			if(td_current.children().prop("tagName").toLowerCase() == 'select'){
				valor_td = td_current.children().find("option:selected").text();
			}else{
				valor_td = td_current.children().val();
			}

			td_current.html(valor_td);
			validar_td(td_current, i);
		}
	}
}

function obtener_m(object_id, object_td){
	id_marca = object_id.split("_");
	selected_s = "";
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
	    	html_marcas = "<select class='form-control select_marcas' onchange='valida_opcion(this)' name='s_marcas'>";
	    	for (i = 0 ; i < respuesta.length ; i++) {
	    		if (respuesta[i].id_marca == id_marca[1]) {
	    			selected_s = "selected";
	    		}else{
	    			selected_s = "";
	    		}
	    		html_marcas += "<option value='" + respuesta[i].id_marca + "' " + selected_s + ">" + respuesta[i].marca + "</option>";
	    	}
	    	html_marcas += "<option value='t'>OTRO...</option>";
	    	html_marcas += "</select>";
	    	$(object_td).html(html_marcas);
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

function editar_p(obj_button){
	if($(obj_button).attr('class') == 'btn btn-info btn-sm editar_p'){
		$("#tabla_productos").find($("td, th")).css("display", "");
		tr_parent = $(obj_button).parent().parent();
		num_td = tr_parent.find("td").length - 2;
		marca_sel = tr_parent.css('background', 'lightgray').find("td").eq(1).attr("id");
		
		for (var i = 1; i < num_td; i++) {
			td_table = tr_parent.children("td").eq(i).css('background', 'lightgray');
			if(i == 1){
				html_marcas = obtener_m(marca_sel, td_table);
			}else{
				td_valor = td_table.html();
				html_input = "<input type='text' size='10' class='input_req' onchange='validar(this)' value='" + td_valor + "' onkeypress='enter_tab(event, this)' />";
				td_table.html(html_input);
			}
		}

		$(obj_button).removeClass("btn-info editar_p").addClass("btn-success actualizar_p");
		$(obj_button).find("span").removeClass("glyphicon-edit").addClass("glyphicon-ok");
	}else{
		actualizar_producto($(obj_button));
	}
}

function borrar_p(obj_button){
	tr_parent = $(obj_button).parent().parent();
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
			    	bootbox.alert(respuesta_borrar, function() {
					  location.href = "index";
					});
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

function obtener_productos(obj_a, pag){
	$(".pagination").find("li").removeClass("active");
	pag_li = $(obj_a).parent();
	$(".pag_" + pag).addClass("active");

	registros = $("#registros").val();

	marca = $("#marca_select select").val();
	modelo = $("#modelo").val();
	codigo_barras = $("#codigo_barras").val();

	variables_post = "marcas_select=" + marca + "&modelo=" + modelo + "&codigo_barras=" + codigo_barras + "&registros=" + registros;

	form_productos = $(this);
	if($("#codigo_barras").val() != ""){
		$("#marca_select option").each(function(index){
			if(index == '0'){
				$(this).prop("selected", true);
			}else{
				$(this).prop("selected", false);
			}
		});
		$("#modelo").val("");
	}

	$.ajax({
	    // la URL para la petición
	    url : "busqueda_producto/" + pag,
	 
	    // especifica si será una petición POST o GET
	    type : "POST",

	    // envia los valores del form
	    data : variables_post,

	    //especifica el tipo de dato que espera recibir
	    dataType: 'html',

	    // código a ejecutar si la petición es satisfactoria;
	    // la respuesta es pasada como argumento a la función
	    success : function(respuesta_busqueda) {
	    	respuesta_busqueda = respuesta_busqueda.split("|||");
	    	$("#tabla_productos").html(respuesta_busqueda[0]);
	    	$("#pags").html(respuesta_busqueda[1]);
	    	if($("#codigo_barras").val() != ""){
	    		for (var i = 4; i <= 29; i++) {
	    			td_current = $("#tabla_productos").find("tr").find("td").eq(i);
	    			if(td_current.html() == $("#codigo_barras").val()){
	    				td_current.css({ 'background-color' : 'lightgreen', 'color' : 'blue' });
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