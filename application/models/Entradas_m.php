<?php
class Entradas_m extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	function obtener_almacenes($id_usuario){

		$this->db->select('a.id_almacen, a.almacen');
		$this->db->from('almacenes a');
		$this->db->join('usuario_almacen ua', 'ua.id_almacen = a.id_almacen');
		$this->db->where('ua.id_usuario', $id_usuario);
		$this->db->order_by('a.id_almacen', 'ASC');
		
		$query = $this->db->get();

		$row = $query->result();

		if (empty($row)) {
			$result = null;
		} else {
			$result = $row;
		}
		
		return $result;

	}

	function obtener_tallas(){

		$this->db->select('*');
		$this->db->from('talla');
		
		$query = $this->db->get();

		$row = $query->result();

		if (empty($row)) {
			$result = null;
		} else {
			$result = $row;
		}
		
		return $result;

	}

	function obtener_marcas(){

		$this->db->select('*');
		$this->db->from('marca');
		
		$query = $this->db->get();

		$row = $query->result();

		if (empty($row)) {
			$result = null;
		} else {
			$result = $row;
		}
		
		return $result;

	}

	function obtener_producto($codigo_barras = null){

		$this->db->select('pt.id_producto_talla,p.id_producto,m.marca,p.modelo,p.descripcion,t.id_talla,t.talla');
		$this->db->from('producto_talla pt');
		$this->db->join('productos p', 'p.id_producto = pt.id_producto');
		$this->db->join('marca m', 'm.id_marca = p.id_marca');
		$this->db->join('talla t', 't.id_talla = pt.id_talla');

		if (!is_null($codigo_barras)){
			$this->db->where('pt.codigo_barras', $codigo_barras);
		}

		$query = $this->db->get();

		$row = $query->result();

		if (empty($row)) {
			$result = null;
		} else {
			$result = $row;
		}
		
		return $result;

	}

	function obtener_producto_modelo($marca = null, $modelo = null){

		$this->db->select('p.id_producto,m.marca,p.modelo,p.descripcion');
		$this->db->from('productos p');
		$this->db->join('marca m', 'm.id_marca = p.id_marca');

		if ($marca != 0){
			$this->db->where('p.id_marca', $marca);
		}

		if (!is_null($modelo) && ($modelo != "") && ($modelo != '0')){
			$this->db->where('p.modelo', $modelo);
		}
		//echo $this->db->get_compiled_select();die;
		$query = $this->db->get();

		$row = $query->result();

		if (empty($row)) {
			$result = null;
		} else {
			$result = $row;
		}
		
		return $result;

	}

	function obtener_talla_cantidad($id_producto){

		$this->db->select('id_talla,cantidad');
		$this->db->from('producto_talla');
		$this->db->where('id_producto', $id_producto);
		$this->db->order_by('id_talla', 'ASC');

		//echo $this->db->get_compiled_select();die;
		$query = $this->db->get();

		$row = $query->result();

		if (empty($row)) {
			$result = null;
		} else {
			$result = $row;
		}
		
		return $result;

	}

	function registrar_entrada($entrada, $entrada_detalle){

		$fecha_entrada = date("Y-m-d H:m:s");

		$this->db->trans_begin();

		$this->db->select('id_movimiento');
		$this->db->from('movimientos');
		$this->db->where('id_tipo_movimiento', 1);

		//echo $this->db->get_compiled_select();die;
		$query = $this->db->get();

		if ($this->db->trans_status() === FALSE){
			$mensaje = "Error al consultar las entradas existentes.";
			$mensaje .= "|f";
		    $this->db->trans_rollback();
		    return $mensaje;
		}

		$row = count($query->result());

		$folio = 'E' . ($row + 1);

		$data = array(
			'folio' => $folio,
	        'id_tipo_movimiento' => 1,
	        'cantidad' => $entrada["cantidad"],
	        'precio' => 0,
	        'fecha' => $fecha_entrada,
	        'id_almacen' => $entrada["id_almacen"],
	        'confirmacion' => 1
		);

		$this->db->insert('movimientos', $data);

		if ($this->db->trans_status() === FALSE){
		    $mensaje = "Error al insertar el movimiento de entrada.";
			$mensaje .= "|f";
		    $this->db->trans_rollback();
		    return $mensaje;
		}

		$id_ultimo_e = $this->db->insert_id();
		
		for($i = 0; $i < count($entrada_detalle) ; $i++){

			$this->db->select('cantidad');
			$this->db->from('producto_talla');
			$this->db->where('id_producto', $entrada_detalle[$i]["id_producto"]);
			$this->db->where('id_talla', $entrada_detalle[$i]["id_talla"]);

			//echo $this->db->get_compiled_select();die;
			$query = $this->db->get();

			if ($this->db->trans_status() === FALSE){
			    $mensaje = "Error al consultar la cantidad del producto.";
				$mensaje .= "|f";
			    $this->db->trans_rollback();
			    return $mensaje;
			}

			$row = $query->result();

			if (empty($row)) {
				$data_pt = array(
			        'id_producto' => $entrada_detalle[$i]["id_producto"],
			        'id_talla' => $entrada_detalle[$i]["id_talla"],
			        'codigo_barras' => '',
			        'id_almacen' => NULL,
			        'cantidad' => $entrada_detalle[$i]["cantidad"]
				);

				$this->db->insert('producto_talla', $data_pt);

			}else{
				$cantidad = (int)$row[0]->cantidad + (int)$entrada_detalle[$i]["cantidad"];
				$this->db->set('cantidad', $cantidad);
				$this->db->where('id_producto', $entrada_detalle[$i]["id_producto"]);
				$this->db->where('id_talla', $entrada_detalle[$i]["id_talla"]);
				$this->db->update('producto_talla');
			}

			if ($this->db->trans_status() === FALSE){
			    $mensaje = "Error al insertar/actualizar la cantidad del producto.";
				$mensaje .= "|f";
			    $this->db->trans_rollback();
			    return $mensaje;
			}

			$data_det = array(
		        'id_movimiento' => $id_ultimo_e,
		        'id_producto' => $entrada_detalle[$i]["id_producto"],
		        'id_talla' => $entrada_detalle[$i]["id_talla"],
		        'cantidad' => $entrada_detalle[$i]["cantidad"],
		        'precio' => 0
			);

			$this->db->insert('detalle_movimiento', $data_det);

			if ($this->db->trans_status() === FALSE){
			    $mensaje = "Error al insertar el detalle del producto.";
				$mensaje .= "|f";
			    $this->db->trans_rollback();
			    return $mensaje;
			}
		}

		$mensaje = "Se realizó correctamente la entrada.";
		$mensaje .= "|t";
		$this->acciones_m->set_user_action($_SESSION["id_usuario"], "Se registro la entrada con id: " . $id_ultimo_e);
		$this->db->trans_commit();

		return $mensaje;

	}

}