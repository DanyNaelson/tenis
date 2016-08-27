<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventario_fisico extends CI_Controller {

	public function index(){
		$this->load->model('inventario_fisico_m');
		
		if (!isset($_SESSION["id_usuario"])) {
			header('Location: /inventarios/login/');
		}
		
		$data["id_usuario"] = $_SESSION["id_usuario"];
		$data["nombre"] = $_SESSION["nombre"];
		$data["titulo"] = "Sistema de inventarios | Inventario Físico";
		$data["login"] = false;
		$data["modulo"] = "Inventario Físico";
		$data["pagina_retorno"] = "/inventarios/inicio/index/" . $_SESSION["id_usuario"];
		$data["archivo_js"] = "inventario_fisico.js";

		$almacenes = $this->inventario_fisico_m->obtener_almacenes($data["id_usuario"]);
		$tallas = $this->inventario_fisico_m->obtener_tallas();

		$data["almacenes"] = $almacenes;
		$data["tallas"] = $tallas;
		$data["productos"] = array();

		$this->load->view('plantillas/header',$data);
		$this->load->view('inventario_fisico_v');
		$this->load->view('plantillas/footer',$data);
	}

	public function obtener_producto(){
		$this->load->model('inventario_fisico_m');
		$codigo_barras = trim($this->input->post("codigo_barras"));
		$id_almacen = trim($this->input->post("id_almacen"));
		$producto = array();
		$tr_html = "";

		if ($codigo_barras != "") {
			$producto = $this->inventario_fisico_m->obtener_producto($codigo_barras, $id_almacen);
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
		$this->load->model('inventario_fisico_m');
		$producto_marcas = $this->inventario_fisico_m->obtener_marcas();
		echo json_encode($producto_marcas);
	}

	public function obtener_producto_modelo($marca = null, $modelo = null, $almacen = null){
		if (trim($modelo) == ""){
			$modelo = null;
		}

		$this->load->model('inventario_fisico_m');
		$producto_modelo = $this->inventario_fisico_m->obtener_producto_modelo(trim($marca), trim($modelo), trim($almacen));
		echo json_encode($producto_modelo);
	}

	public function obtener_tallas_select(){
		$this->load->model('inventario_fisico_m');
		$tallas = $this->inventario_fisico_m->obtener_tallas();

		$html_select = "<select class='form-control talla_select' name='talla_select' onchange='valida_talla(this)'>";
		$html_select .= "<option value='0'>Seleccionar...</option>'";
		foreach ($tallas as $talla) {
			$html_select .= "<option value='" . $talla->id_talla . "'>" . $talla->talla . "</option>'";
		}
		$html_select .= "</select>";

		echo $html_select;
	}

	public function obtener_talla_cantidad(){
		$this->load->model('inventario_fisico_m');
		$talla_cantidad = $this->inventario_fisico_m->obtener_talla_cantidad(trim($this->input->post("id_prod")), trim($this->input->post("id_almacen")));
		$count_tallas = count($this->inventario_fisico_m->obtener_tallas());
		$talla_cantidad_def = $this->crea_arr_talla_cantidad($talla_cantidad, $count_tallas, trim($this->input->post("id_talla")));
		echo json_encode($talla_cantidad_def);
	}

	public function obtener_cantidad_modelo(){
		$this->load->model('inventario_fisico_m');
		$cantidad_modelo = $this->inventario_fisico_m->obtener_talla_cantidad(trim($this->input->post("id_producto")), trim($this->input->post("id_almacen")), trim($this->input->post("id_talla")));
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

	public function finalizar_fisico(){
		$this->load->model('inventario_fisico_m');
		$fisico = $this->input->post("obj_physical");
		$respuesta_fisico = $this->inventario_fisico_m->finalizar_fisico($fisico);
		echo json_encode($respuesta_fisico);
	}

}