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
}