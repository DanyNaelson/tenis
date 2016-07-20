<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ventas extends CI_Controller {

	public function index(){
		$this->load->model('ventas_m');
		
		if (!isset($_SESSION["id_usuario"])) {
			header('Location: /inventarios/login/');
		}
		
		$data["id_usuario"] = $_SESSION["id_usuario"];
		$data["nombre"] = $_SESSION["nombre"];
		$data["titulo"] = "Sistema de inventarios | Ventas";
		$data["login"] = false;
		$data["modulo"] = "Ventas";
		$data["pagina_retorno"] = "/inventarios/inicio/index/" . $_SESSION["id_usuario"];
		$data["archivo_js"] = "ventas.js";

		$almacenes = $this->ventas_m->obtener_almacenes($data["id_usuario"]);
		$vendedores = $this->ventas_m->obtener_usuarios();

		$data["almacenes"] = $almacenes;
		$data["vendedores"] = $vendedores;

		$this->load->view('plantillas/header',$data);
		$this->load->view('ventas_v');
		$this->load->view('plantillas/footer',$data);
	}

	public function obtener_producto(){
		$this->load->model('ventas_m');
		$codigo_barras = trim($this->input->post("codigo_barras"));
		$id_almacen = trim($this->input->post("id_almacen"));
		$producto = array();
		$tr_html = "";

		if ($codigo_barras != "") {
			$producto = $this->ventas_m->obtener_producto($codigo_barras, $id_almacen);
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
		$this->load->model('ventas_m');
		$producto_marcas = $this->ventas_m->obtener_marcas();
		echo json_encode($producto_marcas);
	}

	public function obtener_producto_modelo($marca = null, $modelo = null){
		if (trim($modelo) == ""){
			$modelo = null;
		}

		$this->load->model('ventas_m');
		$producto_modelo = $this->ventas_m->obtener_producto_modelo(trim($marca), trim($modelo));
		echo json_encode($producto_modelo);
	}

	public function obtener_tallas_select(){
		$this->load->model('ventas_m');
		$tallas = $this->ventas_m->obtener_tallas();

		$html_select = "<select class='form-control talla_select' name='talla_select' onchange='valida_talla(this)'>";
		$html_select .= "<option value='0'>Seleccionar...</option>'";
		foreach ($tallas as $talla) {
			$html_select .= "<option value='" . $talla->id_talla . "'>" . $talla->talla . "</option>'";
		}
		$html_select .= "</select>";

		echo $html_select;
	}

	public function obtener_cantidad_producto($id_producto, $id_talla, $id_almacen){
		$this->load->model('ventas_m');
		$producto_talla_cantidad = $this->ventas_m->obtener_cantidad_producto($id_producto, $id_talla, $id_almacen);
		
		$cant_max = 0;
		foreach ($producto_talla_cantidad as $producto_talla) {
			if ($producto_talla->id_tipo_movimiento == 1) {
				$cant_max += $producto_talla->cantidad;
			} else {
				$cant_max -= $producto_talla->cantidad;
			}
		}

		echo $cant_max;
	}

	public function obtener_cantidad_modelo(){
		$this->load->model('ventas_m');
		$cantidad_modelo = $this->ventas_m->obtener_talla_cantidad(trim($this->input->post("id_producto")), trim($this->input->post("id_almacen")), trim($this->input->post("id_talla")));
		echo json_encode($cantidad_modelo);
	}

	public function crea_arr_talla_cantidad($talla_cantidad, $count_tallas, $talla){
		$talla_cantidad_def = array();
		$id_talla = 0;

		for ($i = 0 ; $i < $count_tallas ; $i++) {
			$id_talla = $i + 1;

			if(isset($talla_cantidad[0]->id_talla)){
				if($talla_cantidad[0]->id_talla == $id_talla){
					$talla_cantidad_def[$i]["cantidad"] = $talla_cantidad[0]->cantidad;

					array_shift($talla_cantidad);
				}else{
					$talla_cantidad_def[$i]["cantidad"] = "0";
				}
			}else{
				$talla_cantidad_def[$i]["cantidad"] = "0";
			}
		}

		return $talla_cantidad_def;
	}

	public function registrar_venta(){
		$this->load->model('ventas_m');
		$venta = $this->input->post("obj_sale");
		$venta_detalle = $this->input->post("obj_sale_detail");
		$respuesta_venta = $this->ventas_m->registrar_venta($venta, $venta_detalle);
		echo $respuesta_venta;
	}

	public function obtener_ventas(){
		$this->load->model('ventas_m');
		$id_almacen = $this->input->post("almacen");
		$respuesta_ventas = $this->ventas_m->obtener_ventas($id_almacen);
		echo json_encode($respuesta_ventas);
	}

	public function confirmar_movimientos(){
		$this->load->model('ventas_m');
		$id_movimientos = $this->input->post("movs");
		$confirmacion_ventas = $this->ventas_m->confirmar_ventas($id_movimientos);
		echo json_encode($confirmacion_ventas);
	}
}