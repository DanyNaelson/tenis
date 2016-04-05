<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inicio extends CI_Controller {

	public function index()
	{
		$data["titulo"] = "Sistema de inventarios";
		$data["login"] = false;
		$this->load->view('plantillas/header',$data);
		$this->load->view('inicio_v');
		$this->load->view('plantillas/footer',$data);
	}
}