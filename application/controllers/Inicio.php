<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inicio extends CI_Controller {

	public function index($idusuario = null)
	{
		$this->load->model('inicio_m');
		if($idusuario != null){
			$datos_usuario = $this->inicio_m->obtener_datos_usuario($idusuario);
			$newdata = array(
				'id_usuario' => $datos_usuario[0]->id_usuario,
		        'nombre'  => $datos_usuario[0]->usuario,
		        'logeado' => TRUE
			);

			$this->session->set_userdata($newdata);

			$permisos_usuario = $this->inicio_m->obtener_usuario_permisos($idusuario);
		}
		$data["id_usuario"] = $_SESSION["id_usuario"];
		$data["nombre"] = $_SESSION["nombre"];
		$data["titulo"] = "Sistema de inventarios | Inicio";
		$data["login"] = false;
		$data["archivo_js"] = "inicio.js";
		$data["permisos"] = $permisos_usuario;

		$this->load->view('plantillas/header',$data);
		$this->load->view('inicio_v');
		$this->load->view('plantillas/footer',$data);
	}
}