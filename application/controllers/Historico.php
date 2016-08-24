<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Historico extends CI_Controller {

	public function index(){
		$this->load->model('historico_m');
		
		if (!isset($_SESSION["id_usuario"])) {
			header('Location: /inventarios/login/');
		}
		
		$data["id_usuario"] = $_SESSION["id_usuario"];
		$data["nombre"] = $_SESSION["nombre"];
		$data["titulo"] = "Sistema de inventarios | Histórico";
		$data["login"] = false;
		$data["modulo"] = "Histórico";
		$data["pagina_retorno"] = "/inventarios/inicio/index/" . $_SESSION["id_usuario"];
		$data["archivo_js"] = "historico.js";

		$almacenes = $this->historico_m->obtener_almacenes($data["id_usuario"]);
		$tallas = $this->historico_m->obtener_tallas();

		$data["almacenes"] = $almacenes;
		$data["tallas"] = $tallas;
		$data["productos"] = array();

		$this->load->view('plantillas/header',$data);
		$this->load->view('historico_v');
		$this->load->view('plantillas/footer',$data);
	}

	public function obtener_producto_almacen(){
		
		$this->load->model('historico_m');
		$id_almacen = trim($this->input->post("id_almacen"));
		$producto = array();
		$tr_html = "";

		$producto = $this->historico_m->obtener_movimiento_producto($id_almacen);

		if (empty($producto)) {
			$respuesta = 'null';
		} else {
			
			$count_tallas = count($this->historico_m->obtener_tallas());
			
			foreach ($producto as $prod) {
				$prod->cantidades = array();
				$cantidad_t = 0;
				$cant = 0;

				for($j = 1 ; $j <= $count_tallas ; $j++){
					$talla_cant = $this->historico_m->obtener_talla_cantidad($id_almacen, $prod->id_producto, $j);

					if(empty($talla_cant)){
						$cant = 0;
					}else{
						$cant = $this->obtener_cantidad($talla_cant);
					}

					$cantidad_t += $cant;
					array_push($prod->cantidades, $cant);
				}

				array_push($prod->cantidades, $cantidad_t);
			}

			$respuesta = json_encode($producto);
			//var_dump($respuesta);die;
		}
		
		echo $respuesta;

	}

	public function obtener_cantidad($talla_cant){
		$cantidad_real = 0;

		foreach($talla_cant as $talla_c){
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
		}

		return $cantidad_real;
	}

	public function crear_csv(){	
		
		$this->load->model('historico_m');
		$id_almacen = trim($this->input->post("id_almacen"));
		$producto = array();
		$tr_html = "";
		$respuesta = "false";

		$producto = $this->historico_m->obtener_movimiento_producto($id_almacen);

		if (empty($producto)) {
			$producto = 'null';
		} else {
			
			$count_tallas = count($this->historico_m->obtener_tallas());
			
			foreach ($producto as $prod) {
				$prod->cantidades = array();
				$cantidad_t = 0;
				$cant = 0;

				for($j = 1 ; $j <= $count_tallas ; $j++){
					$talla_cant = $this->historico_m->obtener_talla_cantidad($id_almacen, $prod->id_producto, $j);

					if(empty($talla_cant)){
						$cant = 0;
					}else{
						$cant = $this->obtener_cantidad($talla_cant);
					}

					$cantidad_t += $cant;
					array_push($prod->cantidades, $cant);
				}

				array_push($prod->cantidades, $cantidad_t);
			}	

			$html_productos = "";

			$html_productos .= "Marca,Modelo,Descripcion,1,1.5,2,2.5,3,3.5,4,4.5,5,5.5,6,6.5,7,7.5,8,8.5,9,9.5,10,10.5,11,11.5,12,12.5,13,13.5,Total" . PHP_EOL;

			for($i = 0; $i < count($producto) ; $i++){
				if($producto[$i]->cantidades[count($producto[$i]->cantidades) - 1] > 0){
					$html_productos .= 	$producto[$i]->marca . ",";
					$html_productos .= 	$producto[$i]->modelo . ",";
					$html_productos .= 	$producto[$i]->descripcion . ",";

					for ($j = 0; $j < count($producto[$i]->cantidades) ; $j++) {
						if($producto[$i]->cantidades[$j] > 0){
							$color_td = "greenyellow";
						}else{
							$color_td = "#FFF";
						}

						$html_productos .= $producto[$i]->cantidades[$j];
						
						if ($j < count($producto[$i]->cantidades) - 1) {
							$html_productos .= ",";
						}
					}

					$html_productos .= PHP_EOL;
				}
			}

			$fp = fopen('../inventarios/assets/csv/historico' . $id_almacen . '.csv', 'w+');
			fwrite($fp, $html_productos);
			fclose($fp);

			$respuesta = "true";
		}

		echo json_encode($respuesta);
	}

}