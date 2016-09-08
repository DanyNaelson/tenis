<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Productos extends CI_Controller {

	public function index()
	{
		$this->load->model('productos_m');
		
		if (!isset($_SESSION["id_usuario"])) {
			header('Location: /inventarios/login/');
		}
		
		$data["id_usuario"] = $_SESSION["id_usuario"];
		$data["nombre"] = $_SESSION["nombre"];
		$data["titulo"] = "Sistema de inventarios | Productos";
		$data["login"] = false;
		$data["modulo"] = "Productos";
		$data["pagina_retorno"] = "/inventarios/inicio/index/" . $_SESSION["id_usuario"];
		$data["archivo_js"] = "productos.js";

		$tallas = $this->productos_m->obtener_tallas();
		$modelos = $this->obtener_modelos();
		if ($modelos != 0) {
			if(($modelos % 10) == 0){
				$paginas = $modelos/10;
			}else{
				$paginas = ceil($modelos/10);
			}
		} else {
			$paginas = 1;
		}
		
		$productos = $this->productos_m->obtener_productos();
		$cadena_p = $this->cadena_productos($productos);
		$producto_talla = $this->productos_m->obtener_producto_talla(null, $cadena_p);
		$productos_tallas = $this->crear_arreglo_producto($tallas, $productos, $producto_talla);

		$data["tallas"] = $tallas;
		$data["productos"] = $productos;
		$data["productos_tallas"] = $productos_tallas;
		$data["paginas"] = $paginas;

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
			for($j = $ini ; $j <= count($producto_talla) + 2 ; $j++){
				if(isset($producto_talla[$j-1]->id_producto)){
					if($producto_talla[$j-1]->id_producto == $productos[$i-1]->id_producto){
						$arreglo_tmp[$i-1][$ini_j] = $producto_talla[$j-1]->id_talla . "-" . $producto_talla[$j-1]->codigo_barras;
						$ini_j++;
					}
				}
			}
		}
		/*echo "<pre>";
		print_r($arreglo_tmp);
		echo "</pre>";
		exit;*/

		for ($i = $ini ; $i <= count($productos) ; $i++) {
			$ini_j = 0;
			for ($j = $ini ; $j <= count($tallas) + 2 ; $j++) {
				if(isset($arreglo_tmp[$i-1][$ini_j])){
					$valores_tmp = explode("-", $arreglo_tmp[$i-1][$ini_j]);
					if($valores_tmp[0] == $j){
						$arreglo_tallas[$i-1][$j-1] = $valores_tmp[1];
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

	public function obtener_modelos($id_marca = null, $modelo = null){
		$this->load->model('productos_m');
		$respuesta = $this->productos_m->obtener_modelos($id_marca, $modelo);
		return $respuesta;
	}

	public function obtener_marcas($select = null){
		$this->load->model('productos_m');
		$respuesta = $this->productos_m->obtener_marcas();
		if($select == null){
			echo json_encode($respuesta);
		}else{
			$html_marcas = "<select name='marcas_select' class='form-control'>";
			$html_marcas .= 	"<option value='0'>Seleccionar...</option>";
			foreach ($respuesta as $option) {
				$html_marcas .= "<option value='" . $option->id_marca . "'>" . $option->marca . "</option>";
			}
			$html_marcas .= "</select>";
			echo $html_marcas;
		}
		
	}

	public function actualizar_producto()
	{
		$this->load->model('productos_m');
		$respuesta = $this->productos_m->actualizar_producto($this->input->post("datos_p"));
		echo $respuesta;
	}

	public function borrar_producto(){
		$this->load->model('productos_m');
		$respuesta = $this->productos_m->borrar_producto($this->input->post("datos_p"));
		echo $respuesta;
	}

	public function insertar_producto(){
		$this->load->model('productos_m');
		$respuesta = $this->productos_m->insertar_producto($this->input->post("datos_p"));
		echo $respuesta;
	}

	public function obtener_codigo(){
		$this->load->model('productos_m');
		$respuesta = $this->productos_m->obtener_codigo($this->input->post("d_codigo"));
		echo json_encode($respuesta);
	}

	public function comprobar_movimiento(){
		$this->load->model('productos_m');
		$respuesta = $this->productos_m->comprobar_movimiento($this->input->post("barcode"));
		echo json_encode($respuesta);
	}

	public function validar_marca(){
		$this->load->model('productos_m');
		$respuesta = $this->productos_m->validar_marca($this->input->post("p_marca"));
		echo json_encode($respuesta);
	}

	public function validar_modelo(){
		$this->load->model('productos_m');
		$respuesta = $this->productos_m->validar_modelo($this->input->post("p_modelo"));
		echo json_encode($respuesta);
	}

	public function busqueda_producto($pag = 1, $offset = 0){
		$marca = trim($this->input->post("marcas_select"));
		$modelo = trim($this->input->post("modelo"));
		$codigo_barras = trim($this->input->post("codigo_barras"));
		$registros = trim($this->input->post("registros"));
		$orden_busqueda = 't';
		$offset = ($pag * $registros) - $registros;
		$paginas = 1;
		$limit = $registros;
		$tallas = array();
		$productos = array();
		$productos_tallas = array();

		if ($marca == "0") {
			$marca = null;
		}

		if ($modelo == "") {
			$modelo = null;
		}

		if ($codigo_barras == "") {
			$codigo_barras = null;
		}else{
			$orden_busqueda = 'f';
		}

		$this->load->model('productos_m');
		$tallas = $this->productos_m->obtener_tallas();

		if($orden_busqueda == 't'){
			$productos = $this->productos_m->obtener_productos($marca, $modelo, $limit, $offset);

			if(!empty($productos)){
				$cadena_p = $this->cadena_productos($productos);
				$producto_talla = $this->productos_m->obtener_producto_talla($codigo_barras, $cadena_p);
				$productos_tallas = $this->crear_arreglo_producto($tallas, $productos, $producto_talla);
				$modelos = $this->obtener_modelos($marca, $modelo);
			
				if ($modelos != 0) {
					if(($modelos % $registros) == 0){
						$paginas = $modelos/$registros;
					}else{
						$paginas = ceil($modelos/$registros);
					}
				}
			}
		}else{
			$producto_talla = $this->productos_m->obtener_producto_talla($codigo_barras);

			if(!empty($producto_talla)){
				$productos = $this->productos_m->obtener_productos($producto_talla[0]->id_marca, $producto_talla[0]->modelo, $limit, $offset);
				$cadena_p = $this->cadena_productos($productos);
				$producto_talla = $this->productos_m->obtener_producto_talla(null, $cadena_p);
				$productos_tallas = $this->crear_arreglo_producto($tallas, $productos, $producto_talla);

				$modelos = count($productos);

				if ($modelos != 0) {
					if(($modelos % $registros) == 0){
						$paginas = $modelos/$registros;
					}else{
						$paginas = ceil($modelos/$registros);
					}
				}
			}
		}

		$data["tallas"] = $tallas;
		$data["productos"] = $productos;
		$data["productos_tallas"] = $productos_tallas;

		$respuesta = $this->create_table_html($tallas, $productos, $productos_tallas, $offset);

		$html_pags = '<ul class="pagination">';
		$html_pags .= 	'<li class="first">';
		$html_pags .= 		'<a href="#" aria-label="Previous" onclick="obtener_productos(this, 1)">';
		$html_pags .= 			'<span aria-hidden="true">&laquo;</span>';
		$html_pags .= 		'</a>';
		$html_pags .= 	'</li>';

		for ($i = 1 ; $i <= $paginas ; $i++) { 
			$html_pags .= '<li class="';
			if($i == $pag){
				$html_pags .= 'active ';
			}
			$html_pags .= 'pag_' . $i . '">';
			$html_pags .= 	'<a href="#" onclick="obtener_productos(this, ' . $i . ')">' . $i . '</a>';
			$html_pags .= '</li>';
		}

		$html_pags .= 	'<li class="last">';
		$html_pags .= 		'<a href="#" aria-label="Next" onclick="obtener_productos(this, ' . $paginas . ')">';
		$html_pags .= 			'<span aria-hidden="true">&raquo;</span>';
		$html_pags .= 		'</a>';
		$html_pags .= 	'</li>';
		$html_pags .= '</ul>';
		
		echo $respuesta . "|||" . $html_pags;
	}

	public function cadena_productos($arreglo_productos){
		$productos = array();

		foreach ($arreglo_productos as $id_producto) {
			$productos[] = $id_producto->id_producto;
		}

		$cadena_p = implode(",", $productos);
		return $cadena_p;
	}

	public function create_table_html($tallas, $productos, $productos_tallas, $offset){
		$html = '<thead>
				<tr class="th-blue">
					<th class="text-center">#</th>
					<th class="text-center" class="marca">Marca</th>
					<th class="text-center" class="modelo">Modelo</th>
					<th class="text-center" class="descripcion">Descripcion</th>';
		
		foreach ($tallas as $talla){
			$html .= '<th class="text-center">' . ucfirst($talla->talla) . '</th>';
		}

				$html .= '<th class="text-center" class="precio">Precio</th>
					<th class="text-center">Editar</th>
					<th class="text-center">Borrar</th>
				</tr>
			</thead>
			<tbody>';
		for ($i = 0 ; $i < count($productos) ; $i++){
			$html .= '<tr id="producto_' . $productos[$i]->id_producto . '">
					<td class="text-center no-item">' . ($offset+$i+1) . '</td>
					<td class="text-center marca" id="marca_' . $productos[$i]->id_marca . '">' . $productos[$i]->marca . '</td>
					<td class="text-center modelo" onchange="validar_modelo(this)">' . $productos[$i]->modelo . '</td>
					<td class="text-center descripcion">' . $productos[$i]->descripcion . '</td>';
			for ($j = 0 ; $j < count($tallas) ; $j++){
				if($productos_tallas[$i][$j] != ''){
					$html .= '<td class="text-center i-codigo check talla_' . ($j + 1) . '" onchange="validar_codigo(this)" onkeypress="enter_tab(event, this)">' . $productos_tallas[$i][$j] . '</td>';
				}else{
					$html .= '<td class="text-center i-codigo no-check talla_' . ($j + 1) . '" onchange="validar_codigo(this)" onkeypress="enter_tab(event, this)"></td>';
				}
			}
			$html .= '<td class="text-center precio">' . $productos[$i]->precio . '</td>
					<td class="text-center">
						<button type="button" class="btn btn-info btn-sm editar_p" onclick="editar_p(this)">
							<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
						</button>
					</td>
					<td class="text-center">
						<button type="button" class="btn btn-danger btn-sm borrar_p" onclick="borrar_p(this)">
							<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
						</button>
					</td>
				</tr>';
		}
		$html .= "</tbody>";
		$html .= '<tfoot>
				<tr class="th-blue">
					<th class="text-center">#</th>
					<th class="text-center" class="marca">Marca</th>
					<th class="text-center" class="modelo">Modelo</th>
					<th class="text-center" class="descripcion">Descripcion</th>';
		
		foreach ($tallas as $talla){
			$html .= '<th class="text-center">' . ucfirst($talla->talla) . '</th>';
		}

				$html .= '<th class="text-center" class="precio">Precio</th>
					<th class="text-center">Editar</th>
					<th class="text-center">Borrar</th>
				</tr>
			</tfoot>';

		return $html;
	}
}