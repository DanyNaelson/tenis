/*Funciones de inventarios*/
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
	$('#password').change(function(){
		if($(this).val() == ''){
			alerta = 'Favor de ingresar contraseña.';
			$(this).next(".alert-danger").html(alerta).slideDown('fast');
		}else{
			$.ajax({
			    // la URL para la petición
			    url : '/inventarios/login/valida_usuario/' + $("#usuario").val() + '/' + $(this).val(),
			 
			    // especifica si será una petición POST o GET
			    type : 'POST',

			    // código a ejecutar si la petición es satisfactoria;
			    // la respuesta es pasada como argumento a la función
			    success : function(usuarios) {
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
	if(usuarios != 'null'){
		$("#usuario").parent().children(".alert-danger").html("").hide();
		$("#usuario").parent().children(".alert-success").html("Perfecto! Ahora ingresa tu contraseña.").slideDown('fast');
		$("#password").prop("disabled", false).focus();
	}else{
		$("#usuario").parent().children(".alert-success").html("").hide();
		$("#password").prop("disabled", true).val("").parent().children(".alert-danger").hide();
		$("#usuario").focus().parent().children(".alert-danger").html("El usuario no existe, favor de ingresarlo nuevamente.").slideDown('fast');
	}
}

function validar_contraseña(usuarios){
	if(usuarios != 'null'){
		$("#password").parent().children(".alert-danger").html("").hide();
		$("#password").parent().children(".alert-success").html("Perfecto!").slideDown('fast');
		location.href = "/inventarios/inicio/";
	}else{
		$("#usuario").parent().children(".alert-success").html("").hide();
		$("#password").parent().children(".alert-success").html("").hide();
		$("#password").focus().parent().children(".alert-danger").html("Contraseña incorrecta, favor de ingresarla nuevamente.").slideDown('fast');
	}
}