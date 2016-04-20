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
		$data["archivo_js"] = "administracion.js";

		$data["permisos"] = $this->administracion_m->obtener_modulos();
		$data["usuarios"] = $this->administracion_m->obtener_usuarios();
		$contador_permisos = $this->administracion_m->contador_permisos();
		$data["u_permisos"] = $this->administracion_m->obtener_u_permisos();
		$data["cont_permisos"] = $contador_permisos[0]->c_permisos;

		$this->load->view('plantillas/header',$data);
		$this->load->view('administracion_v');
		$this->load->view('plantillas/footer',$data);
	}
}