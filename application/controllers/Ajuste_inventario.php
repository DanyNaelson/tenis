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

	public function obtener_producto(){
		$this->load->model('ajuste_inventario_m');
		$codigo_barras = trim($this->input->post("codigo_barras"));
		$id_almacen = trim($this->input->post("id_almacen"));
		$producto = array();
		$tr_html = "";

		if ($codigo_barras != "") {
			$producto = $this->ajuste_inventario_m->obtener_producto($codigo_barras, $id_almacen);
		}

		if (empty($producto)) {
			$respuesta = 'null';
		} else {
			$this->load->library("movimientos_lib");
			$this->movimientos_lib->set_properties($producto[0]->id_producto, $producto[0]->marca, $producto[0]->modelo, $producto[0]->descripcion, $producto[0]->talla);
			$this->movimientos_lib->set_movimientos();
			$key = $this->movimientos_lib->find_modelo($codigo_barras);

			$respuesta = json_encode($producto);
		}
		
		echo $respuesta;
	}

	public function obtener_marcas(){
		$this->load->model('ajuste_inventario_m');
		$producto_marcas = $this->ajuste_inventario_m->obtener_marcas();
		echo json_encode($producto_marcas);
	}

	public function obtener_producto_modelo($marca = null, $modelo = null, $almacen = null){
		if (trim($modelo) == ""){
			$modelo = null;
		}

		$this->load->model('ajuste_inventario_m');
		$producto_modelo = $this->ajuste_inventario_m->obtener_producto_modelo(trim($marca), trim($modelo), trim($almacen));
		echo json_encode($producto_modelo);
	}

	public function obtener_tallas_select(){
		$this->load->model('ajuste_inventario_m');
		$tallas = $this->ajuste_inventario_m->obtener_tallas();

		$html_select = "<select class='form-control talla_select' name='talla_select' onchange='valida_talla(this)'>";
		$html_select .= "<option value='0'>Seleccionar...</option>'";
		foreach ($tallas as $talla) {
			$html_select .= "<option value='" . $talla->id_talla . "'>" . $talla->talla . "</option>'";
		}
		$html_select .= "</select>";

		echo $html_select;
	}

	public function obtener_talla_cantidad(){
		$this->load->model('ajuste_inventario_m');
		$talla_cantidad = $this->ajuste_inventario_m->obtener_talla_cantidad(trim($this->input->post("id_prod")), trim($this->input->post("id_almacen")));
		$count_tallas = count($this->ajuste_inventario_m->obtener_tallas());
		$talla_cantidad_def = $this->crea_arr_talla_cantidad($talla_cantidad, $count_tallas, trim($this->input->post("id_talla")));
		echo json_encode($talla_cantidad_def);
	}

	public function obtener_cantidad_modelo(){
		$this->load->model('ajuste_inventario_m');
		$cantidad_modelo = $this->ajuste_inventario_m->obtener_talla_cantidad(trim($this->input->post("id_producto")), trim($this->input->post("id_almacen")), trim($this->input->post("id_talla")));
		echo json_encode($cantidad_modelo);
	}

	public function crea_arr_talla_cantidad($talla_cantidad, $count_tallas, $talla){
		$talla_cantidad_def = array();
		$id_talla = 0;

		for ($i = 0 ; $i < $count_tallas ; $i++) {
			$id_talla = $i + 1;
			$cantidad_real = 0;
			$talla_cantidad_def[$i]["cantidad"] = "0";

			foreach ($talla_cantidad as $talla_c) {
				if(isset($talla_c->id_talla)){
					if($talla_c->id_talla == $id_talla){
						if($talla_c->id_tipo_movimiento == 1 || $talla_c->id_tipo_movimiento == 7 || $talla_c->id_tipo_movimiento == 8 || $talla_c->id_tipo_movimiento == 9){
							if($talla_c->confirmacion == 1){
								$cantidad_real += $talla_c->cantidad;
							}
						}else{
							if($talla_c->id_tipo_movimiento == 3 && $talla_c->confirmacion == -1){
								$cantidad_real -= 0;
							}else{
								if($talla_c->confirmacion == 1){
									$cantidad_real -= $talla_c->cantidad;
								}
							}
						}
						
						$talla_cantidad_def[$i]["cantidad"] = $cantidad_real;
						array_shift($talla_cantidad);
					}
				}
			}
		}

		return $talla_cantidad_def;
	}

	public function registrar_ajuste(){
		$this->load->model('ajuste_inventario_m');
		$ajuste = $this->input->post("obj_fit");
		$ajuste_detalle = $this->input->post("obj_fit_detail");
		$respuesta_ajuste = $this->ajuste_inventario_m->registrar_ajuste($ajuste, $ajuste_detalle);
		echo $respuesta_ajuste;
	}

}