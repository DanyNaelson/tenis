<?php

	header("Content-type: application/x-msdownload");
	header("Content-Disposition: filename=ficheroExcel.xls");
	header("Pragma: no-cache");
	header("Expires: 0");

	/*echo "<html>";
	echo "<head>";
	echo 	"<meta http-equiv=”Content-Type” content=”text/html; charset=utf-8″ />";
	echo "</head>";
	echo "<body>";*/
	echo "<table>";
	echo 	"<tr> ";
	echo 		"<th>Tipo Movimiento</th>";
	echo 		"<th>Folio</th>";
	echo 		"<th>Almacen</th>";
	echo 		"<th>Fecha/Hora</th>";
	echo 		"<th>Cantidad</th>";
	echo 		"<th>Precio</th>";
	echo 		"<th>Estatus</th>";
	echo 	"</tr> ";
	//echo $excel_body;
	echo "</table>";
	/*echo "</body>";
	echo "</html>";*/