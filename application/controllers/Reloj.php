<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reloj extends CI_Controller {

	public function index()
	{
		$hora = date("H");
		$minuto = date("i");
		$segundo = date("s");

		$datos_hora = array(
						"hora" => $hora,
						"minuto" => $minuto,
						"segundo" => $segundo
					);

		echo json_encode($datos_hora);
	}
}