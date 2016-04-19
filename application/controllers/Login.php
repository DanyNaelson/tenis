<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function index()
	{
		$this->session->sess_destroy();
		$data["titulo"] = "Sistema de inventarios";
		$data["login"] = true;
		$data["archivo_js"] = "login.js";
		$this->load->view('plantillas/header',$data);
		$this->load->view('login_v');
		$this->load->view('plantillas/footer',$data);
	}

	public function valida_usuario($usuario = null, $pass = null)
	{
		$this->load->model('login_m');
		$usuarios = $this->login_m->obtener_usuarios($usuario, $pass);
		if($usuarios != null){
			echo json_encode($usuarios);
		}else{
			echo json_encode(null);
		}
	}
}
