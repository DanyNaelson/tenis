<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Traspasos extends CI_Controller {

	public function index(){
		$this->load->model('traspasos_m');
		
		if (!isset($_SESSION["id_usuario"])) {
			header('Location: /inventarios/login/');
		}
		
		$data["id_usuario"] = $_SESSION["id_usuario"];
		$data["nombre"] = $_SESSION["nombre"];
		$data["titulo"] = "Sistema de inventarios | Traspasos";
		$data["login"] = false;
		$data["modulo"] = "Traspasos";
		$data["pagina_retorno"] = "/inventarios/inicio/index/" . $_SESSION["id_usuario"];
		$data["archivo_js"] = "traspasos.js";

		$almacenes = $this->traspasos_m->obtener_almacenes($data["id_usuario"]);
		$almacenes_e = $this->traspasos_m->obtener_almacenes();

		$cadena_almacenes = $this->crea_cadenas($almacenes, 'id_almacen');

		$traspasos_e = $this->traspasos_m->obtener_traspasos_e($cadena_almacenes);
		$traspasos_s = $this->traspasos_m->obtener_traspasos_s($cadena_almacenes);

		$count_te = count($traspasos_e);
		$count_ts = count($traspasos_s);

		$id_movs_ent = $this->crea_cadenas($traspasos_e, 'id_movimiento');
		$id_movs_sal = $this->crea_cadenas($traspasos_s, 'id_movimiento');;

		$data["almacenes"] = $almacenes;
		$data["almacenes_e"] = $almacenes_e;
		$data["count_te"] = $count_te;
		$data["count_ts"] = $count_ts;
		$data["id_movs_ent"] = $id_movs_ent;
		$data["id_movs_sal"] = $id_movs_sal;

		$this->load->view('plantillas/header',$data);
		$this->load->view('traspasos_v');
		$this->load->view('plantillas/footer',$data);
	}

	public function crea_cadenas($objs, $campo){
		$arr_temp = array();
		$cadena = "";

		if(count($objs) > 0){
			foreach ($objs as $obj) {
				array_push($arr_temp, $obj->$campo);
			}

			$cadena = implode(",", $arr_temp);
		}

		return $cadena;
	}

	public function obtener_producto(){
		$this->load->model('traspasos_m');
		$codigo_barras = trim($this->input->post("codigo_barras"));
		$id_almacen = trim($this->input->post("id_almacen_s"));
		$producto = array();
		$tr_html = "";

		if ($codigo_barras != "") {
			$producto = $this->traspasos_m->obtener_producto($codigo_barras, $id_almacen);
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
		$this->load->model('traspasos_m');
		$producto_marcas = $this->traspasos_m->obtener_marcas();
		echo json_encode($producto_marcas);
	}

	public function obtener_producto_modelo($marca = null, $modelo = null){
		if (trim($modelo) == ""){
			$modelo = null;
		}

		$this->load->model('traspasos_m');
		$producto_modelo = $this->traspasos_m->obtener_producto_modelo(trim($marca), trim($modelo));
		echo json_encode($producto_modelo);
	}

	public function obtener_tallas_select(){
		$this->load->model('traspasos_m');
		$tallas = $this->traspasos_m->obtener_tallas();

		$html_select = "<select class='form-control talla_select' name='talla_select' onchange='valida_talla(this)'>";
		$html_select .= "<option value='0'>Seleccionar...</option>'";
		foreach ($tallas as $talla) {
			$html_select .= "<option value='" . $talla->id_talla . "'>" . $talla->talla . "</option>'";
		}
		$html_select .= "</select>";

		echo $html_select;
	}

	public function obtener_cantidad_producto($id_producto, $id_talla, $id_almacen){
		$this->load->model('traspasos_m');
		$producto_talla_cantidad = $this->traspasos_m->obtener_cantidad_producto($id_producto, $id_talla, $id_almacen);
		
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
		$this->load->model('traspasos_m');
		$cantidad_modelo = $this->traspasos_m->obtener_talla_cantidad(trim($this->input->post("id_producto")), trim($this->input->post("id_almacen_s")), trim($this->input->post("id_talla")));
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

	public function registrar_traspaso(){
		$this->load->model('traspasos_m');
		$traspaso = $this->input->post("obj_transfer");
		$traspaso_detalle = $this->input->post("obj_transfer_detail");
		$respuesta_traspaso = $this->traspasos_m->registrar_traspaso($traspaso, $traspaso_detalle);
		echo $respuesta_traspaso;
	}

	public function obtener_traspasos(){
		$this->load->model('traspasos_m');
		$movs_e = $this->input->post("movs_e");
		$id_movs_e = explode(",", $movs_e);
		$movs_s = $this->input->post("movs_s");
		$id_movs_s = explode(",", $movs_s);
		$transfer_t = array();

		$entry_transfer = $this->traspasos_m->obtener_traspasos($movs_e);
		$transfer["entry"] = array();
		$this->crea_transfer($transfer["entry"], $entry_transfer, $id_movs_e);

		$output_transfer = $this->traspasos_m->obtener_traspasos($movs_s);
		$transfer["outlet"] = array();
		$this->crea_transfer($transfer["outlet"], $output_transfer, $id_movs_s);

		array_push($transfer_t, $transfer);
		echo json_encode($transfer_t);
	}

	private function crea_transfer(&$arr_transfers, $transfers, $id_movs){
		foreach($id_movs as $mov){
			$id_mov = array();
			foreach($transfers as $transfer){
				if($transfer->id_movimiento == $mov){
					$t_move = array(
									"id_almacen_e" => $transfer->almacen_e,
									"id_almacen_s" => $transfer->id_almacen,
									"id_movimiento" => $transfer->id_movimiento,
									"cantidad" => $transfer->cantidad,
									"marca" => $transfer->marca,
									"modelo" => $transfer->modelo,
									"descripcion" => $transfer->descripcion,
									"talla" => $transfer->talla
								);

					array_push($id_mov, $t_move);
				}
			}

			array_push($arr_transfers, $id_mov);
		}
	}

	public function confirmar_movimientos(){
		$this->load->model('traspasos_m');
		$id_movimientos = $this->input->post("movs");
		$confirmacion_traspasos = $this->traspasos_m->confirmar_traspasos($id_movimientos);
		echo json_encode($confirmacion_traspasos);
	}

	public function cancelar_movimientos(){
		$this->load->model('traspasos_m');
		$id_movimientos = $this->input->post("movs");
		$cancelacion_traspasos = $this->traspasos_m->cancelar_movimientos($id_movimientos);
		echo json_encode($cancelacion_traspasos);
	}
}