<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes extends CI_Controller {

	public function index()
	{
		$this->load->model('productos_m');
		
		if (!isset($_SESSION["id_usuario"])) {
			header('Location: /inventarios/login/');
		}
		
		$data["id_usuario"] = $_SESSION["id_usuario"];
		$data["nombre"] = $_SESSION["nombre"];
		$data["titulo"] = "Sistema de inventarios | Reportes";
		$data["login"] = false;
		$data["modulo"] = "Reportes";
		$data["pagina_retorno"] = "/inventarios/inicio/index/" . $_SESSION["id_usuario"];
		$data["archivo_js"] = "reportes.js";

		$this->load->model("reportes_m");
		$almacenes = $this->reportes_m->obtener_almacenes($data["id_usuario"]);
		$tipos_m = $this->reportes_m->obtener_tipo_movimiento();
		/*if ($modelos != 0) {
			if(($modelos % 10) == 0){
				$paginas = $modelos/10;
			}else{
				$paginas = ceil($modelos/10);
			}
		} else {*/
		$paginas = 1;
		//}

		$data["almacenes"] = $almacenes;
		$data["tipos_m"] = $tipos_m;
		$data["paginas"] = $paginas;

		$this->load->view('plantillas/header',$data);
		$this->load->view('reportes_v');
		$this->load->view('plantillas/footer',$data);
	}

	public function obtener_movimientos(){
		$this->load->model('reportes_m');
		$id_almacen = $this->input->post("almacen");
		
		$folio = $this->input->post("folio");
		if ($folio == "") {
			$folio = null;
		}

		$tipo_movimiento = $this->input->post("tipo_movimiento");
		if ($tipo_movimiento == "0") {
			$tipo_movimiento = null;
		}

		$fecha_inicio = $this->input->post("fecha_inicio");
		if ($fecha_inicio == "") {
			$fecha_inicio = null;
		}

		$fecha_fin = $this->input->post("fecha_fin");
		if ($fecha_fin == "") {
			$fecha_fin = null;
		}

		$limit = $this->input->post("limit");

		$offset = $this->input->post("offset");

		$movimientos = $this->reportes_m->obtener_movimientos($id_almacen, $limit, $offset, $folio, $tipo_movimiento, $fecha_inicio, $fecha_fin);
		echo json_encode($movimientos);
	}

	public function obtener_detalles_movimiento(){
		$this->load->model('reportes_m');
		$id_movimiento = $this->input->post("movimiento");
		$detalles_movimiento = $this->reportes_m->obtener_detalles_movimiento($id_movimiento);
		echo json_encode($detalles_movimiento);
	}

	public function cancelar_movimiento(){
		$this->load->model('reportes_m');
		$id_movimiento = $this->input->post("movimiento");
		$cancelacion_movimiento = $this->reportes_m->cancelar_movimiento($id_movimiento);
		echo json_encode($cancelacion_movimiento);
	}

	public function reporte_excel($id_almacen, $tipo, $folio, $fecha_i, $fecha_f){
		
		if($folio == "0"){
			$folio = null;
		}

		if($fecha_i == "0"){
			$fecha_i = null;
		}

		if($fecha_f == "0"){
			$fecha_f = null;
		}

		$this->load->model('reportes_m');
		$movimientos = $this->reportes_m->obtener_movimientos($id_almacen, null, null, $folio, $tipo, $fecha_i, $fecha_f);

		$excel_body = "";

		foreach ($movimientos as $movimiento) {
			$detalles = $this->reportes_m->obtener_detalles_movimiento($movimiento->id_movimiento);
			$excel_body .= $this->excel_body_movements($movimiento, $detalles);
		}

		$data["excel_body"] = $excel_body;
		$this->load->view("reporte_excel_v", $data);
		/*echo "<html>";
		echo "<head>";
		echo 	"<meta http-equiv=”Content-Type” content=”text/html; charset=utf-8″ />";
		echo "</head>";
		echo "<body>";
		echo "<table border=1>";
		echo 	"<tr> ";
		echo 		"<th>Tipo Movimiento</th>";
		echo 		"<th>Folio</th>";
		echo 		"<th>Almacen</th>";
		echo 		"<th>Fecha/Hora</th>";
		echo 		"<th>Cantidad</th>";
		echo 		"<th>Precio</th>";
		echo 		"<th>Estatus</th>";
		echo 	"</tr> ";
		echo $excel_body;
		echo "</table>";
		echo "</body>";
		echo "</html>";*/
	}

	private function excel_body_movements($movimiento, $detalles){
		switch($movimiento->confirmacion){
			case "0":
		        $confirmacion = "Sin confirmar";
		        break;
		    case "1":
		        $confirmacion = "Confirmado";
		        break;
		    case "-1":
		        $confirmacion = "Cancelado";
		        break;
		    default:
		        $confirmacion = "Sin status";
		}

		$table_excel = "";

		$table_excel .= "<tr>";
		$table_excel .= 	"<td>" . $movimiento->tipo_movimiento . "</td>";
		$table_excel .= 	"<td>" . $movimiento->folio . "</td>";
		$table_excel .= 	"<td>" . $movimiento->almacen . "</td>";
		$table_excel .= 	"<td>" . $movimiento->fecha . "</td>";
		$table_excel .= 	"<td>" . $movimiento->cantidad . "</td>";
		$table_excel .= 	"<td>" . $movimiento->precio . "</td>";
		$table_excel .= 	"<td>" . $confirmacion . "</td>";
		$table_excel .= "</tr> ";
		/*$table_excel .= "<tr>";
		$table_excel .= 	"<td colspan='1'></td>";
		$table_excel .= 	"<td colspan='5'>";
		$table_excel .= 		"<table border=1>";
		$table_excel .= 			"<tr> ";
		$table_excel .= 				"<th>Cantidad</th>";
		$table_excel .= 				"<th>Marca</th>";
		$table_excel .= 				"<th>Modelo</th>";
		$table_excel .= 				"<th>Descripcion</th>";
		$table_excel .= 				"<th>Talla</th>";
		$table_excel .= 				"<th>Precio</th>";
		$table_excel .= 			"</tr> ";

		foreach ($detalles as $detalle) {
			$table_excel .= 			"<tr>";
			$table_excel .= 				"<td>" . $detalle->cantidad . "</td>";
			$table_excel .= 				"<td>" . $detalle->marca . "</td>";
			$table_excel .= 				"<td>" . $detalle->modelo . "</td>";
			$table_excel .= 				"<td>" . $detalle->descripcion . "</td>";
			$table_excel .= 				"<td>" . $detalle->talla . "</td>";
			$table_excel .= 				"<td>" . $detalle->precio . "</td>";
			$table_excel .= 			"</tr> ";
		}

		$table_excel .= 		"</table>";
		$table_excel .= 	"<td colspan='1'></td>";
		$table_excel .= 	"</td>";
		$table_excel .= "</tr> ";*/

		return $table_excel;
	}
}