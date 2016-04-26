/*Funciones de agregar*/
$(document).ready(function(){
	/*Funcion que agrega producto*/
	$('#agregar').click(function(){
		form = $(this).parent();
		$.ajax({
		    // la URL para la petición
		    url : form.attr("action"),
		 
		    // especifica si será una petición POST o GET
		    type : form.attr("method"),

		    // envia los valores del form
		    data : form.serialize(),

		    //especifica el tipo de dato que espera recibir
		    dataType: 'html',

		    // código a ejecutar si la petición es satisfactoria;
		    // la respuesta es pasada como argumento a la función
		    success : function(respuesta_agregar) {
		    	bootbox.alert(respuesta_agregar);
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