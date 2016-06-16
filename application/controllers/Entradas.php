<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Entradas extends CI_Controller {

	public function index(){
		$this->load->model('entradas_m');
		
		if (!isset($_SESSION["id_usuario"])) {
			header('Location: /inventarios/login/');
		}
		
		$data["id_usuario"] = $_SESSION["id_usuario"];
		$data["nombre"] = $_SESSION["nombre"];
		$data["titulo"] = "Sistema de inventarios | Entradas";
		$data["login"] = false;
		$data["pagina_retorno"] = "/inventarios/inicio/index/" . $_SESSION["id_usuario"];
		$data["archivo_js"] = "entradas.js";

		$almacenes = $this->entradas_m->obtener_almacenes();
		$tallas = $this->entradas_m->obtener_tallas();

		$data["almacenes"] = $almacenes;
		$data["tallas"] = $tallas;
		$data["productos"] = array();

		$this->load->view('plantillas/header',$data);
		$this->load->view('entradas_v');
		$this->load->view('plantillas/footer',$data);
	}

	public function obtener_producto(){
		$this->load->model('entradas_m');
		$codigo_barras = trim($this->input->post("codigo_barras"));
		$producto = $this->entradas_m->obtener_producto($codigo_barras);
		$tr_html = $this->crea_tr($producto);
		echo $tr_html;
	}

	public function crea_tr($producto){
		$html_tr = "<tr id='producto_" . $producto[0]->id_producto . "'>";
		$html_tr .= 	"<td></td>";
		$html_tr .= 	"<td>" . $producto[0]->marca . "</td>";
		$html_tr .= 	"<td>" . $producto[0]->modelo . "</td>";
		$html_tr .= 	"<td>" . $producto[0]->descripcion . "</td>";
		$html_tr .= 	"<td>" . $producto[0]->talla . "</td>";
		$html_tr .= 	"<td>1</td>";
		$html_tr .= 	"<td>
							<button type='button' class='btn btn-danger btn-sm' id='cancelar'>
								<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>
							</button>
						</td>";
		$html_tr .= "</tr>";

		return $html_tr;
	}

}