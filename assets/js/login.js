/*Funciones de login*/
$(document).ready(function(){
	/*Funcion que valida la existencia de un usuario*/
	$('#usuario').change(function(){
		if($(this).val() == ''){
			alerta = 'Favor de ingresar usuario.';
			$(this).next(".alert-danger").html(alerta).slideDown('fast');
		}else{
			$.ajax({
			    // la URL para la petición
			    url : '/inventarios/login/valida_usuario/' + $(this).val(),
			 
			    // especifica si será una petición POST o GET
			    type : 'POST',

			    //especifica el tipo de dato que espera recibir
			    dataType: 'json',

			    // código a ejecutar si la petición es satisfactoria;
			    // la respuesta es pasada como argumento a la función
			    success : function(usuarios) {
			    	validar_usuario(usuarios);
			    },
			 
			    // código a ejecutar si la petición falla;
			    // son pasados como argumentos a la función
			    // el objeto de la petición en crudo y código de estatus de la petición
			    error : function(xhr, status) {
			        alert('Disculpe, existió un problema');
			    }
			});
		}
	});

	/*Funcion que valida la existencia de un usuario y contraseña*/
	$('#entrar').click(function(){
		if($("#password").val() == ''){
			alerta = 'Favor de ingresar contraseña.';
			$("#password").next(".label-danger").html(alerta).slideDown('fast');
		}else{
			$.ajax({
			    // la URL para la petición
			    url : '/inventarios/login/valida_usuario/' + $("#usuario").val() + '/' + $("#password").val(),
			 
			    // especifica si será una petición POST o GET
			    type : 'POST',

			    //especifica el tipo de dato que espera recibir
			    dataType: 'json',

			    //antes de enviar los datos
			    beforeSend :  function() {
			    	$("#loading").html("Iniciando Sesión").slideDown('fast');
			    },

			    // código a ejecutar si la petición es satisfactoria;
			    // la respuesta es pasada como argumento a la función
			    success : function(usuarios) {
			    	$("#loading").html("").slideUp('fast');
			    	validar_contraseña(usuarios);
			    },
			 
			    // código a ejecutar si la petición falla;
			    // son pasados como argumentos a la función
			    // el objeto de la petición en crudo y código de estatus de la petición
			    error : function(xhr, status) {
			        alert('Disculpe, existió un problema');
			    }
			});
		}
	});
});

function validar_usuario(usuarios){
	if(usuarios[0].valida != null){
		$("#usuario").parent().removeClass("has-error").addClass("has-success").children(".glyphicon").removeClass("glyphicon-remove").addClass("glyphicon-ok");
		$("#usuario").parent().children(".label-danger").html("").hide();
		$("#password").prop("disabled", false).focus();
	}else{
		$("#usuario").parent().removeClass("has-success").addClass("has-error").children(".glyphicon").removeClass("glyphicon-ok").addClass("glyphicon-remove");
		$("#password").prop("disabled", true).val("").parent().children(".label-danger").hide();
		$("#usuario").focus().parent().children(".label-danger").html("El usuario no existe, favor de ingresarlo nuevamente.").slideDown('fast');
	}
}

function validar_contraseña(usuario){
	if(usuario[0].valida != null){
		$("#password").parent().children(".label-danger").html("").hide();
		$("#password").parent().removeClass("has-error").addClass("has-success").children(".glyphicon").removeClass("glyphicon-remove").addClass("glyphicon-ok");
		location.href = "/inventarios/inicio/index/" + usuario[1][0].id_usuario;
	}else{
		$("#password").parent().removeClass("has-success").addClass("has-error").children(".glyphicon").removeClass("glyphicon-ok").addClass("glyphicon-remove");
		$("#password").focus().parent().children(".label-danger").html("Contraseña incorrecta, favor de ingresarla nuevamente.").slideDown('fast');
	}
}

