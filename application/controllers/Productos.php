<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Productos extends CI_Controller {

	public function index()
	{
		$this->load->model('productos_m');
		
		$data["id_usuario"] = $_SESSION["id_usuario"];
		$data["nombre"] = $_SESSION["nombre"];
		$data["titulo"] = "Sistema de inventarios | Productos";
		$data["login"] = false;
		$data["pagina_retorno"] = "/inventarios/inicio/index/" . $_SESSION["id_usuario"];
		$data["archivo_js"] = "productos.js";

		$tallas = $this->productos_m->obtener_tallas();
		$productos = $this->productos_m->obtener_productos();
		$producto_talla = $this->productos_m->obtener_producto_talla();
		$productos_tallas = $this->crear_arreglo_producto($tallas, $productos, $producto_talla);

		$data["tallas"] = $tallas;
		$data["productos"] = $productos;
		$data["productos_tallas"] = $productos_tallas;

		$this->load->view('plantillas/header',$data);
		$this->load->view('productos_v');
		$this->load->view('plantillas/footer',$data);
	}

	public function crear_arreglo_producto($tallas, $productos, $producto_talla)
	{
		$arreglo_tallas = array();
		$arreglo_tmp = array();
		$ini = 1;

		for ($i = $ini ; $i <= count($productos) ; $i++) {
			$ini_j = 0;
			for($j = $ini ; $j <= count($tallas) ; $j++){
				if(isset($producto_talla[$j-1]->id_producto)){
					if($producto_talla[$j-1]->id_producto == $productos[$i-1]->id_producto){
						$arreglo_tmp[$i-1][$ini_j] = $producto_talla[$j-1]->id_talla;
						$ini_j++;
					}
				}
			}
		}

		for ($i = $ini ; $i <= count($productos) ; $i++) {
			$ini_j = 0;
			for ($j = $ini ; $j <= count($tallas) ; $j++) {
				if(isset($arreglo_tmp[$i-1][$ini_j])){
					if($arreglo_tmp[$i-1][$ini_j] == $j){
						$arreglo_tallas[$i-1][$j-1] = $producto_talla[$ini_j]->codigo_barras;
						$ini_j++;
					}else{
						$arreglo_tallas[$i-1][$j-1] = '';
					}
				}else{
					$arreglo_tallas[$i-1][$j-1] = '';
				}
			}
		}

		unset($arreglo_tmp);

		return $arreglo_tallas;
	}

	public function actualizar_usuario()
	{
		$this->load->model('administracion_m');
		$respuesta = $this->administracion_m->actualizar_usuario($_POST["datos_u"]);
		echo $respuesta;
	}

	public function borrar_usuario(){
		$this->load->model('administracion_m');
		$respuesta = $this->administracion_m->borrar_usuario($_POST["datos_u"]);
		echo $respuesta;
	}

	public function insertar_usuario(){
		$this->load->model('administracion_m');
		$respuesta = $this->administracion_m->insertar_usuario($_POST["datos_u"]);
		echo $respuesta;
	}
}