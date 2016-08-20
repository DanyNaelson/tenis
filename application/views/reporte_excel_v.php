<?php

	header("Cache-control: private");
	header("Content-disposition: filename=reporte_movimientos.xls");
	header("Content-Type: application/msexcel; charset=iso-8859-1");

	$html = "<table border='1'>";
	$html .= 	"<tr>";
	$html .= 		"<th>Tipo Movimiento</th>";
	$html .= 		"<th>Folio</th>";
	$html .= 		"<th>Almacen</th>";
	$html .= 		"<th>Fecha/Hora</th>";
	$html .= 		"<th>Cantidad</th>";
	$html .= 		"<th>Precio</th>";
	$html .= 		"<th>Estatus</th>";
	$html .= 	"</tr>";
	$html .=	 $excel_body;
	$html .= "</table>";

	echo $html;