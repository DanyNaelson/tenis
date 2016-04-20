<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Productos extends CI_Controller {

	public function agregar()
	{
		$data["nombre"] = $_SESSION["nombre"];
		$data["titulo"] = "Sistema de inventarios | Agregar Producto";
		$data["login"] = false;
		$data["archivo_js"] = "agregar.js";
		$this->load->view('plantillas/header',$data);
		$this->load->view('agregar_v');
		$this->load->view('plantillas/footer',$data);
	}

	public function agregar_producto(){
		$modelo = $_POST["modelo"]; 
		$talla = $_POST["talla"];
		$color = $_POST["color"];
		$precio = $_POST["precio"];
		$codigo_barras = $_POST["codigo_barras"];
		$imagen = "";
		$marca = $_POST["marca"];
		$this->load->model("productos_m");
		$insersion = $this->productos_m->agregar_producto($modelo, $talla, $color, $precio, $codigo_barras, $imagen, $marca);
		echo $insersion;
	}

	public function consultar(){
		$data["nombre"] = $_SESSION["nombre"];
		$data["titulo"] = "Sistema de inventarios | Consultar Productos";
		$data["login"] = false;
		$data["archivo_js"] = "consultar.js";

		$this->load->view('plantillas/header',$data);
		$this->load->view('consultar_v');
		$this->load->view('plantillas/footer',$data);
	}

	public function consultar_productos($id_producto = null){
		$this->load->model("productos_m");
		$consulta = $this->productos_m->consultar_producto($id_producto);
		echo $consulta;
	}
}