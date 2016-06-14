<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Administracion extends CI_Controller {

	public function index()
	{
		$this->load->model('administracion_m');
		
		$data["id_usuario"] = $_SESSION["id_usuario"];
		$data["nombre"] = $_SESSION["nombre"];
		$data["titulo"] = "Sistema de inventarios | Administrador";
		$data["login"] = false;
		$data["pagina_retorno"] = "/inventarios/inicio/index/" . $_SESSION["id_usuario"];
		$data["archivo_js"] = "administracion.js";

		$data["permisos"] = $this->administracion_m->obtener_modulos();
		$data["usuarios"] = $this->administracion_m->obtener_usuarios();
		$contador_permisos = $this->administracion_m->contador_permisos();
		$data["cont_permisos"] = $contador_permisos[0]->c_permisos;
		$u_permisos = $this->administracion_m->obtener_u_permisos();
		$data["u_permisos"] = $this->crear_arreglo_permisos($u_permisos, $data["usuarios"], $data["cont_permisos"]);

		$this->load->view('plantillas/header',$data);
		$this->load->view('administracion_v');
		$this->load->view('plantillas/footer',$data);
	}

	public function crear_arreglo_permisos($u_permisos, $d_usuarios, $cont_permisos)
	{
		$arreglo_permisos = array();
		$arreglo_tmp = array();
		$ini = 1;

		for ($i = $ini ; $i <= count($d_usuarios) ; $i++) {
			$ini_j = 0;
			for($j = $ini ; $j <= count($u_permisos) ; $j++){
				if($u_permisos[$j-1]["id_usuario"] == $d_usuarios[$i-1]->id_usuario){
					$arreglo_tmp[$i-1][$ini_j] = $u_permisos[$j-1]["id_permiso"];
					$ini_j++;
				}
			}
		}

		for ($i = $ini ; $i <= count($d_usuarios) ; $i++) {
			$ini_j = 0;
			for ($j = $ini ; $j <= $cont_permisos ; $j++) {
				if(isset($arreglo_tmp[$i-1][$ini_j])){
					if($arreglo_tmp[$i-1][$ini_j] == $j){
						$arreglo_permisos[$i-1][$j-1] = '1';
						$ini_j++;
					}else{
						$arreglo_permisos[$i-1][$j-1] = '0';
					}
				}else{
					$arreglo_permisos[$i-1][$j-1] = '0';
				}
			}
		}

		unset($arreglo_tmp);

		return $arreglo_permisos;
	}

	public function actualizar_usuario()
	{
		$this->load->model('administracion_m');
		$respuesta = $this->administracion_m->actualizar_usuario($this->input->post("datos_u"));
		echo $respuesta;
	}

	public function borrar_usuario(){
		$this->load->model('administracion_m');
		$respuesta = $this->administracion_m->borrar_usuario($this->input->post("datos_u"));
		echo $respuesta;
	}

	public function insertar_usuario(){
		$this->load->model('administracion_m');
		$respuesta = $this->administracion_m->insertar_usuario($this->input->post("datos_u"));
		echo $respuesta;
	}
}