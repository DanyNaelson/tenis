<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajuste_inventario extends CI_Controller {

	public function index(){
		$this->load->model('ajuste_inventario_m');
		
		if (!isset($_SESSION["id_usuario"])) {
			header('Location: /inventarios/login/');
		}
		
		$data["id_usuario"] = $_SESSION["id_usuario"];
		$data["nombre"] = $_SESSION["nombre"];
		$data["titulo"] = "Sistema de inventarios | Ajuste de Inventario";
		$data["login"] = false;
		$data["modulo"] = "Ajuste de Inventario";
		$data["pagina_retorno"] = "/inventarios/inicio/index/" . $_SESSION["id_usuario"];
		$data["archivo_js"] = "ajuste_inventario.js";

		$almacenes = $this->ajuste_inventario_m->obtener_almacenes($data["id_usuario"]);
		$tipo_mov = $this->ajuste_inventario_m->obtener_tipo_movimiento();
		$tallas = $this->ajuste_inventario_m->obtener_tallas();

		$data["almacenes"] = $almacenes;
		$data["tipo_mov"] = $tipo_mov;
		$data["tallas"] = $tallas;
		$data["productos"] = array();

		$this->load->view('plantillas/header',$data);
		$this->load->view('ajuste_inventario_v');
		$this->load->view('plantillas/footer',$data);
	}

	public function obtener_datos_ajuste(){
		$this->load->model('ajuste_inventario_m');
		$id_tipo_movimiento = trim($this->input->post("tipo"));
		$id_almacen = trim($this->input->post("almacen"));
		$producto_ajuste = array();

		$producto_ajuste = $this->ajuste_inventario_m->obtener_datos_ajuste($id_almacen, $id_tipo_movimiento);
		echo json_encode($producto_ajuste);
	}

	public function registrar_ajuste(){
		$this->load->model('ajuste_inventario_m');
		$ajuste = $this->input->post("obj_fit");
		$ajuste_detalle = $this->input->post("obj_fit_detail");
		$respuesta_ajuste = $this->ajuste_inventario_m->registrar_ajuste($ajuste, $ajuste_detalle);
		echo $respuesta_ajuste;
	}

}