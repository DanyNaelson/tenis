/*Funciones de inventarios*/
$(document).ready(function(){
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
			 
			    // el tipo de información que se espera de respuesta
			    //dataType : 'json',
			 
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

	function validar_usuario(usuarios){
		if(usuarios != '0'){
    		$("#usuario").parent().children(".alert-danger").html("").slideUp('fast');
    		$("#usuario").parent().children(".alert-success").html("Perfecto! Ahora ingresa tu contraseña.").slideDown('fast');
    		$("#password").prop("disabled", false).focus();
    	}else{
    		$("#usuario").parent().children(".alert-success").html("").slideUp('fast');
    		$("#password").prop("disabled", true).html("");
    		$("#usuario").parent().children(".alert-danger").html("El usuario no existe, favor de ingresarlo nuevamente.").slideDown('fast');
    	}
	}
});