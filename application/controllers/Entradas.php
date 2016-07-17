<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Entradas extends CI_Controller {

	public function index(){
		$this->load->model('entradas_m');
		
		if (!isset($_SESSION["id_usuario"])) {
			header('Location: /inventarios/login/');
		}
		
		$data["id_usuario"] = $_SESSION["id_usuario"];
		$data["nombre"] = $_SESSION["nombre"];
		$data["titulo"] = "Sistema de inventarios | Entradas";
		$data["login"] = false;
		$data["modulo"] = "Entradas";
		$data["pagina_retorno"] = "/inventarios/inicio/index/" . $_SESSION["id_usuario"];
		$data["archivo_js"] = "entradas.js";

		$almacenes = $this->entradas_m->obtener_almacenes($data["id_usuario"]);
		$tallas = $this->entradas_m->obtener_tallas();

		$data["almacenes"] = $almacenes;
		$data["tallas"] = $tallas;
		$data["productos"] = array();

		$this->load->view('plantillas/header',$data);
		$this->load->view('entradas_v');
		$this->load->view('plantillas/footer',$data);
	}

	public function obtener_producto(){
		$this->load->model('entradas_m');
		$codigo_barras = trim($this->input->post("codigo_barras"));
		$producto = array();
		$tr_html = "";

		if ($codigo_barras != "") {
			$producto = $this->entradas_m->obtener_producto($codigo_barras);
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
		$this->load->model('entradas_m');
		$producto_marcas = $this->entradas_m->obtener_marcas();
		echo json_encode($producto_marcas);
	}

	public function obtener_producto_modelo($marca = null, $modelo = null){
		if (trim($modelo) == ""){
			$modelo = null;
		}

		$this->load->model('entradas_m');
		$producto_modelo = $this->entradas_m->obtener_producto_modelo(trim($marca), trim($modelo));
		echo json_encode($producto_modelo);
	}

	public function obtener_tallas_select(){
		$this->load->model('entradas_m');
		$tallas = $this->entradas_m->obtener_tallas();

		$html_select = "<select class='form-control talla_select' name='talla_select' onchange='valida_talla(this)'>";
		$html_select .= "<option value='0'>Seleccionar...</option>'";
		foreach ($tallas as $talla) {
			$html_select .= "<option value='" . $talla->id_talla . "'>" . $talla->talla . "</option>'";
		}
		$html_select .= "</select>";

		echo $html_select;
	}

	public function obtener_talla_cantidad(){
		$this->load->model('entradas_m');
		$talla_cantidad = $this->entradas_m->obtener_talla_cantidad(trim($this->input->post("id_prod")));
		$count_tallas = count($this->entradas_m->obtener_tallas());
		$talla_cantidad_def = $this->crea_arr_talla_cantidad($talla_cantidad, $count_tallas, trim($this->input->post("id_talla")));
		echo json_encode($talla_cantidad_def);
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

	public function registrar_entrada(){
		$this->load->model('entradas_m');
		$entrada = $this->input->post("obj_entrie");
		$entrada_detalle = $this->input->post("obj_entrie_detail");
		$respuesta_entrada = $this->entradas_m->registrar_entrada($entrada, $entrada_detalle);
		echo $respuesta_entrada;
	}

	public function crear_json(){
		$this->load->helper('file');
		$obj_json = $this->input->post("datos_json");
		$id_almacen = $obj_json[0]["idalmacen"];
		$remove_str = array("\\t", "\\n");
		$obj_json = str_replace($remove_str, "", json_encode($obj_json));
		$array_json = (array)json_decode($obj_json);
		$respuesta = array();

		if(is_file('../inventarios/assets/json/entradas.json')){
			$file_json = file_get_contents('../inventarios/assets/json/entradas.json');
			$json_php = (array)json_decode($file_json);
			$cont = 0;

			foreach ($json_php as $json_p) {
				foreach ($json_p as $arr_j) {
					$search = array_search($id_almacen, $arr_j);var_dump($search);die;
					if($search == "idalmacen"){
						$cont++;
					}
				}
			}
			var_dump($cont);die;
			if($cont > 0){
				$respuesta["resp"] = "No se puede pausar la entrada debido a que ya existe otra en pausa en el mismo almacén.|f";
			}else{
				array_push($json_php, $array_json);
				$file_j = fopen('../inventarios/assets/json/entradas.json', "w+");
				$response = fwrite($file_j, json_encode($json_php));
				$respuesta["resp"] = "Se creó el archivo correctamente.|t";
			}

		}else{
			$file_j = fopen('../inventarios/assets/json/entradas.json', "w+");
			$response = fwrite($file_j, $obj_json);
		}

		echo json_encode($respuesta);
	}

}