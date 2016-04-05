<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function index()
	{
		$data["titulo"] = "Sistema de inventarios";
		$this->load->view('plantillas/header',$data);
		$this->load->view('login_v');
		$this->load->view('plantillas/footer',$data);
	}

	public function valida_usuario($usuario = NULL)
	{
		$this->load->model('login_m');
		$usuarios = $this->login_m->obtener_usuarios($usuario);
		if(count($usuarios) > 0){
			$html_response = $this->crear_tabla_html($usuarios);
			echo $html_response;
		}else{
			echo 0;
		}
	}

	public function crear_tabla_html($datos_arreglo)
	{
		print_r($datos_arreglo);
	}
}
