/* Funciones de administracion */
$(document).ready(function(){
	$(".editar_u").click(function(){
		if($(this).attr('class') == 'btn btn-info btn-sm editar_u'){
			tipo_td = $(this).parent().parent().children("td").eq(2).find("input").attr("type");

			if(tipo_td != 'text'){
				tr_parent = $(this).parent().parent();
				num_td = tr_parent.find("td").length - 2;
				
				for (var i = 1; i < num_td; i++) {
					td_table = tr_parent.children("td").eq(i);
					tipo = td_table.find("input").attr('type');
					if(tipo == "checkbox"){
						td_table.find("input").prop("disabled", false);
					}else{
						td_valor = td_table.html();
						html_input = "<input type='text' class='input_req' value='" + td_valor + "' />";
						td_table.html(html_input);
					}
				}

				$(this).removeClass("btn-info editar_u").addClass("btn-success actualizar_u");
				$(this).find("span").removeClass("glyphicon-edit").addClass("glyphicon-ok");
			}
		}else{
			actualizar_usuario($(this));
		}
	});

	$(".input_req").click(function(){
		if($(this).prop("checked")){
			$(this).parent().removeClass("no-check").addClass("check");
		}else{
			$(this).parent().removeClass("check").addClass("no-check");
		}
	});

	$(".borrar_u").click(function(){
		tr_parent = $(this).parent().parent();
		valor_tr = tr_parent.attr('id');;
		usuario = valor_tr.split("_");
		bootbox.confirm("Seguro que desea borrar el usuario?", function(result) {
			if(result){
				$.ajax({
				    // la URL para la petición
				    url : "borrar_usuario",
				 
				    // especifica si será una petición POST o GET
				    type : "POST",

				    // envia los valores del form
				    data : { datos_u : usuario[1] },

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

	$("#agregar_u").click(function(){
		tr_html = "<tr id='usuario_2'>";
		tr_html +=	"<td class='text-center no-item'></td>";
		tr_html +=	"<td class='text-center'>";
		tr_html +=	"	<input class='input_req' value='' type='text'>";
		tr_html +=	"</td>";
		tr_html +=	"<td class='text-center'>";
		tr_html +=	"	<input class='input_req' value='' type='text'>";
		tr_html +=	"</td>";
		tr_html +=	"<td class='text-center no-check'>";
		tr_html +=		"<input class='input_req' onclick='presionar(this)' type='checkbox'>";
		tr_html +=	"</td>";
		tr_html +=	"<td class='text-center no-check'>";
		tr_html +=		"<input class='input_req' onclick='presionar(this)' type='checkbox'>";
		tr_html +=	"</td>";
		tr_html +=	"<td class='text-center no-check'>";
		tr_html +=		"<input class='input_req' onclick='presionar(this)' type='checkbox'>";
		tr_html +=	"</td>";
		tr_html +=	"<td class='text-center no-check'>";
		tr_html +=		"<input class='input_req' onclick='presionar(this)' type='checkbox'>";
		tr_html +=	"</td>";
		tr_html +=	"<td class='text-center no-check'>";
		tr_html +=		"<input class='input_req' onclick='presionar(this)' type='checkbox'>";
		tr_html +=	"</td>";
		tr_html +=	"<td class='text-center no-check'>";
		tr_html +=		"<input class='input_req' onclick='presionar(this)' type='checkbox'>";
		tr_html +=	"</td>";
		tr_html +=	"<td class='text-center no-check'>";
		tr_html +=		"<input class='input_req' onclick='presionar(this)' type='checkbox'>";
		tr_html +=	"</td>";
		tr_html +=	"<td class='text-center no-check'>";
		tr_html +=		"<input class='input_req' onclick='presionar(this)' type='checkbox'>";
		tr_html +=	"</td>";
		tr_html +=	"<td class='text-center'>";
		tr_html +=		"<button type='button' class='btn btn-success btn-sm insertar_u' onclick='insertar_usuario(this)'>";
		tr_html +=			"<span class='glyphicon glyphicon-ok' aria-hidden='true'></span>";
		tr_html +=		"</button>";
		tr_html +=	"</td>";
		tr_html +=	"<td class='text-center'>";
		tr_html +=		"<button type='button' class='btn btn-danger btn-sm borrar_u'>";
		tr_html +=			"<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>";
		tr_html +=		"</button>";
		tr_html +=	"</td>";
		tr_html += "</tr>";

		$("#tabla_usuarios > tbody").append(tr_html);
	});
});

function actualizar_usuario(obj_boton){
	tr_parent = $(obj_boton).parent().parent();
	valor_tr = tr_parent.attr('id');
	usuario = valor_tr.split("_");
	num_td = tr_parent.find("td").length - 2;
	actualizar = true;
	datos_usuario = usuario[1];
	
	for (var i = 1; i < num_td; i++) {
		td_table = tr_parent.children("td").eq(i);
		tipo = td_table.find("input").attr('type');
		
		if(tipo == "checkbox"){
			if(td_table.find("input").prop("checked")){
				datos_usuario += "-1";
			}else{
				datos_usuario += "-0";
			}
		}else if(tipo == "text"){
			if(td_table.find("input").val() == "" || td_table.find("input").val() == " "){
				td_table.find("input").focus();
				bootbox.alert("El campo Usuario y Contraseña no pueden estar vacíos.");
				actualizar = false;
			}else{
				valor_input = td_table.find("input").val();
				datos_usuario += "-" + valor_input;
			}
		}
	}

	if(actualizar){
		$.ajax({
		    // la URL para la petición
		    url : "actualizar_usuario",
		 
		    // especifica si será una petición POST o GET
		    type : "POST",

		    // envia los valores del form
		    data : { datos_u : datos_usuario },

		    //especifica el tipo de dato que espera recibir
		    dataType: 'html',

		    // código a ejecutar si la petición es satisfactoria;
		    // la respuesta es pasada como argumento a la función
		    success : function(respuesta_actualizar) {
		    	bootbox.alert(respuesta_actualizar);
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

function insertar_usuario(obj_boton){
	tr_parent = $(obj_boton).parent().parent();
	num_td = tr_parent.find("td").length - 2;
	datos_usuario = "0";
	insertar = true;
	
	for (var i = 1; i < num_td; i++) {
		td_table = tr_parent.children("td").eq(i);
		tipo = td_table.find("input").attr('type');
		
		if(tipo == "checkbox"){
			if(td_table.find("input").prop("checked")){
				datos_usuario += "-1";
			}else{
				datos_usuario += "-0";
			}
		}else if(tipo == "text"){
			if(td_table.find("input").val() == "" || td_table.find("input").val() == " "){
				td_table.find("input").focus();
				bootbox.alert("El campo Usuario y Contraseña no pueden estar vacíos.");
				actualizar = false;
			}else{
				valor_input = td_table.find("input").val();
				datos_usuario += "-" + valor_input;
			}
		}
	}

	if(insertar){
		$.ajax({
		    // la URL para la petición
		    url : "insertar_usuario",
		 
		    // especifica si será una petición POST o GET
		    type : "POST",

		    // envia los valores del form
		    data : { datos_u : datos_usuario },

		    //especifica el tipo de dato que espera recibir
		    dataType: 'html',

		    // código a ejecutar si la petición es satisfactoria;
		    // la respuesta es pasada como argumento a la función
		    success : function(respuesta_actualizar) {
		    	bootbox.alert(respuesta_actualizar);
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

function presionar(obj_check){
	if($(obj_check).prop("checked")){
		$(obj_check).parent().removeClass("no-check").addClass("check");
	}else{
		$(obj_check).parent().removeClass("check").addClass("no-check");
	}
}