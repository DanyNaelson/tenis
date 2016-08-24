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

		$movimientos_count = $this->reportes_m->obtener_movimientos($id_almacen, null, null, $folio, $tipo_movimiento, $fecha_inicio, $fecha_fin);
		$movimientos = $this->reportes_m->obtener_movimientos($id_almacen, $limit, $offset, $folio, $tipo_movimiento, $fecha_inicio, $fecha_fin);
		array_push($movimientos, count($movimientos_count));
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

	public function obtener_cantidad($talla_cant){
		$cantidad_real = 0;

		foreach($talla_cant as $talla_c){
			$cantidad_real += $talla_c->cantidad;
		}

		return $cantidad_real;
	}

	public function crear_csv(){	
		
		$this->load->model('reportes_m');
		$id_almacen = trim($this->input->post("almacen"));
		$tipo_m = trim($this->input->post("tipo_mov"));
		$producto = array();
		$tr_html = "";
		$respuesta = "false";

		$producto = $this->reportes_m->obtener_movimiento_producto($id_almacen, $tipo_m);

		if (empty($producto)) {
			$producto = 'null';
		} else {
			
			$count_tallas = count($this->reportes_m->obtener_tallas());
			
			foreach ($producto as $prod) {
				$prod->cantidades = array();
				$cantidad_t = 0;
				$cant = 0;

				for($j = 1 ; $j <= $count_tallas ; $j++){
					$talla_cant = $this->reportes_m->obtener_talla_cantidad($id_almacen, $prod->id_producto, $j, $tipo_m);

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

			$fp = fopen('../inventarios/assets/csv/reporte_movimiento_' . $tipo_m . '.csv', 'w+');
			fwrite($fp, $html_productos);
			fclose($fp);

			$respuesta = "true";
		}

		echo json_encode($respuesta);
	}
}