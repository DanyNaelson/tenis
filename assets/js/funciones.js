/*Funciones de la pagina inicio*/
$(document).ready(function(){
	/*Funcion para cerrar sesion*/
	$("#cerrar_s").click(function(){
		bootbox.confirm("Seguro que desea cerrar sesión?", function(result) {
			if(result){
				location.href = "/inventarios/login/";
			}
		}); 
	});
});