/* Funciones de producto */
$(document).ready(function(){

	$("#almacen").on("change", function(event){

		almacen = $("#almacen").val();
		
		if(almacen > 0){
			$.ajax({
			    // la URL para la petición
			    url : "obtener_producto_almacen",
			 
			    // especifica si será una petición POST o GET
			    type : "POST",

			    //datos enviados mediante post
			    data: { id_almacen : almacen },

			    //especifica el tipo de dato que espera recibir
			    dataType: 'json',

			    // código a ejecutar si la petición es satisfactoria;
			    // la respuesta es pasada como argumento a la función
			    success : function(respuesta_producto) {
			    	if (respuesta_producto == null) {
			    		$("#tabla_productos").find("tbody").html("");
			    		bootbox.alert('No existen movimientos en este almacén.');
			    	}else{
			    		crea_tr_prod(respuesta_producto);
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
			bootbox.alert("Debes seleccionar un almacén para realizar la búsqueda del producto.");
		}
	});

	$("#excel").on("click", function(){
		tbody = $("#tabla_productos").find("tbody");
		var id_almacen = $("#almacen").val();

		if(id_almacen != 0){
			$.ajax({
			    // la URL para la petición
			    url : "crear_csv",
			 
			    // especifica si será una petición POST o GET
			    type : "POST",

			    //datos enviados mediante post
			    data: { id_almacen : almacen },

			    //especifica el tipo de dato que espera recibir
			    dataType: 'json',

			    // código a ejecutar si la petición es satisfactoria;
			    // la respuesta es pasada como argumento a la función
			    success : function(respuesta_producto) {
			    	if (respuesta_producto == false) {
			    		$("#tabla_productos").find("tbody").html("");
			    		bootbox.alert('No existen movimientos en este almacén.');
			    	}else{
			    		location.href = "/inventarios/assets/csv/historico" + id_almacen + ".csv";
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
			bootbox.alert("Debes seleccionar el almacén para exportar a excel.");
			$("#almacen").focus();
		}
	});

});

function crea_tr_prod(producto){
	var html_productos = "";
	var color_td = "";

	for(var i = 0; i < producto.length ; i++){
		html_productos += "<tr class='prod_" + producto[i].id_producto + " text-center'>";
		html_productos += 	"<td class='marc'>" + producto[i].marca + "</td>";
		html_productos += 	"<td class='mod'>" + producto[i].modelo + "</td>";
		html_productos += 	"<td class='desc'>" + producto[i].descripcion + "</td>";

		for (var j = 0; j < producto[i].cantidades.length ; j++) {
			if(producto[i].cantidades[j] > 0){
				color_td = "greenyellow";
			}else{
				color_td = "#FFF";
			}

			html_productos += "<td style='background-color:" + color_td + ";' class='tallaid_" + j + "'>" + producto[i].cantidades[j] + "</td>";
		}

		html_productos += "</tr>";
	}

	$("#tabla_productos").find("tbody").html(html_productos);
}