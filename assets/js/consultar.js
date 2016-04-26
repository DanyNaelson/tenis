$(document).ready(function(){
	/*Funcion que valida la existencia de un usuario y contraseña*/
	$('#consultar').click(function(){
		$.ajax({
		    // la URL para la petición
		    url : '/inventarios/productos/consultar_productos/',
		 
		    // especifica si será una petición POST o GET
		    type : 'POST',

		    //especifica el tipo de dato que espera recibir
		    dataType: 'html',

		    //antes de enviar los datos
		    beforeSend :  function() {
		    	//$("#contenido_consulta").html("Iniciando Sesión").slideDown('fast');
		    },

		    // código a ejecutar si la petición es satisfactoria;
		    // la respuesta es pasada como argumento a la función
		    success : function(consultar_datos) {
		    	$("#contenido_consulta").html(consultar_datos);
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