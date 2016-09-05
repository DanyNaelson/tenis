<?php
class Cambios_m extends CI_Model{

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

	function obtener_usuarios(){

		$this->db->select('id_usuario, usuario');
		$this->db->from('usuarios');
		//$this->db->join('usuario_almacen ua', 'ua.id_almacen = a.id_almacen');
		//$this->db->where('ua.id_usuario', $id_usuario);
		$this->db->order_by('usuario', 'ASC');
		
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

	function obtener_producto_folio($folio = null, $id_almacen = null){

		$this->db->select('mc.id_movimiento_confirmacion');
		$this->db->from('movimientos mv');
		$this->db->join('movimiento_confirmacion mc', 'mc.id_movimiento_sal = mv.id_movimiento');
		$this->db->where('mv.folio', $folio);
		$this->db->where('mv.id_almacen', $id_almacen);

		//echo $this->db->get_compiled_select();die;
		$query = $this->db->get();

		$row = $query->result();

		if (!empty($row)) {
			$result = null;
		} else {
			$this->db->select('pt.id_producto_talla,p.id_producto,m.marca,p.modelo,p.descripcion,t.id_talla,t.talla,mv.id_movimiento,mv.id_tipo_movimiento,dm.cantidad,dm.precio');
			$this->db->from('producto_talla pt');
			$this->db->join('productos p', 'p.id_producto = pt.id_producto');
			$this->db->join('marca m', 'm.id_marca = p.id_marca');
			$this->db->join('talla t', 't.id_talla = pt.id_talla');
			$this->db->join('detalle_movimiento dm', 'dm.id_producto = pt.id_producto AND dm.id_talla = pt.id_talla');
			$this->db->join('movimientos mv', 'mv.id_movimiento = dm.id_movimiento AND mv.id_almacen = ' . $id_almacen);
			$this->db->where('mv.id_tipo_movimiento IN (4,5)');

			if (!is_null($folio)){
				$this->db->where('mv.folio', $folio);
			}

			//echo $this->db->get_compiled_select();die;
			$query = $this->db->get();

			$row = $query->result();

			if (empty($row)) {
				$result = null;
			} else {
				$result = $row;
			}
		}
		
		return $result;

	}

	function obtener_producto($codigo_barras = null, $id_almacen = null){

		$this->db->select('pt.id_producto_talla,p.id_producto,m.marca,p.modelo,p.descripcion,t.id_talla,t.talla,mv.id_tipo_movimiento,dm.cantidad,mv.confirmacion');
		$this->db->from('producto_talla pt');
		$this->db->join('productos p', 'p.id_producto = pt.id_producto');
		$this->db->join('marca m', 'm.id_marca = p.id_marca');
		$this->db->join('talla t', 't.id_talla = pt.id_talla');
		$this->db->join('detalle_movimiento dm', 'dm.id_producto = pt.id_producto AND dm.id_talla = pt.id_talla');
		$this->db->join('movimientos mv', 'mv.id_movimiento = dm.id_movimiento AND mv.id_almacen = ' . $id_almacen);

		if (!is_null($codigo_barras)){
			$this->db->where('pt.codigo_barras', $codigo_barras);
			$this->db->where('dm.cantidad > 0');
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

	function obtener_producto_modelo($marca = null, $modelo = null, $id_almacen = null){

		$this->db->select('p.id_producto,m.marca,p.modelo,p.descripcion');
		$this->db->from('productos p');
		$this->db->join('marca m', 'm.id_marca = p.id_marca');
		$this->db->join('detalle_movimiento dm', 'dm.id_producto = p.id_producto');
		$this->db->join('movimientos mv', 'mv.id_movimiento = dm.id_movimiento');

		if ($marca != 0){
			$this->db->where('p.id_marca', $marca);
		}

		if (!is_null($modelo) && $modelo != "" && $modelo != '0'){
			$this->db->where('p.modelo', $modelo);
		}

		if (!is_null($id_almacen) && $id_almacen != ""){
			$this->db->where('mv.id_almacen', $id_almacen);
		}

		$this->db->group_by('p.id_producto,m.marca,p.modelo,p.descripcion');
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

	function obtener_talla_cantidad($id_producto, $id_almacen, $id_talla = null){

		$this->db->select('pt.id_talla,mv.id_tipo_movimiento,dm.cantidad,mv.confirmacion');
		$this->db->from('producto_talla pt');
		$this->db->join('detalle_movimiento dm', 'dm.id_producto = pt.id_producto AND dm.id_talla = pt.id_talla');
		$this->db->join('movimientos mv', 'mv.id_movimiento = dm.id_movimiento AND mv.id_almacen = ' . $id_almacen);
		$this->db->where('pt.id_producto', $id_producto);

		if(!is_null($id_talla)){
			$this->db->where('pt.id_talla', $id_talla);
		}

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

	function obtener_cantidad_producto($id_producto, $id_talla, $id_almacen){

		$this->db->select('mv.id_tipo_movimiento,dm.cantidad,mv.confirmacion');
		$this->db->from('producto_talla pt');
		$this->db->join('detalle_movimiento dm', 'dm.id_producto = pt.id_producto AND dm.id_talla = pt.id_talla');
		$this->db->join('movimientos mv', 'mv.id_movimiento = dm.id_movimiento AND mv.id_almacen = ' . $id_almacen);
		$this->db->where('pt.id_producto', $id_producto);
		$this->db->where('pt.id_talla', $id_talla);
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

	function registrar_cambio($cambio_v, $cambio_detalle_v, $cambio_c, $cambio_detalle_c, $id_almacen, $id_movimiento_venta){

		$fecha_cambio = date("Y-m-d H:m:s");
		$respuesta = array("mensaje" => "Se realizó el cambio correctamente.", "resp" => "t");

		$this->db->trans_begin();

		$this->db->select('id_movimiento');
		$this->db->from('movimientos');
		$this->db->where('id_tipo_movimiento', 8);

		//echo $this->db->get_compiled_select();die;
		$query = $this->db->get();

		if ($this->db->trans_status() === FALSE){
			$respuesta = array("mensaje" => "No se pudieron obtener los cambios de entrada previos.", "resp" => "f");
		    $this->db->trans_rollback();
		    return $respuesta;
		}

		$row = count($query->result());

		$folio = 'CE' . ($row + 1);

		$data = array(
			'folio' => $folio,
	        'id_tipo_movimiento' => 8,
	        'cantidad' => $cambio_v["cantidad"],
	        'fecha' => $fecha_cambio,
	        'id_almacen' => $id_almacen,
	        'precio' => $cambio_v["precio"],
	        'confirmacion' => 1
		);

		$this->db->insert('movimientos', $data);

		if ($this->db->trans_status() === FALSE){
		    $respuesta = array("mensaje" => "No se pudo insertar el cambio de entrada.", "resp" => "f");
		    $this->db->trans_rollback();
		    return $respuesta;
		}

		$id_ultimo_e = $this->db->insert_id();

		$data_e = array(
	        'id_movimiento' => $id_ultimo_e,
			'id_movimiento_sal' => $id_movimiento_venta
		);

		$this->db->insert('movimiento_confirmacion', $data_e);

		if ($this->db->trans_status() === FALSE){
		    $respuesta = array("mensaje" => "No se pudo insertar el movimiento de confirmacion.", "resp" => "f");
		    $this->db->trans_rollback();
		    return $respuesta;
		}

		for($i = 0; $i < count($cambio_detalle_v) ; $i++){

			$this->db->select('cantidad');
			$this->db->from('producto_talla');
			$this->db->where('id_producto', $cambio_detalle_v[$i]["id_producto"]);
			$this->db->where('id_talla', $cambio_detalle_v[$i]["id_talla"]);

			//echo $this->db->get_compiled_select();die;
			$query = $this->db->get();

			if ($this->db->trans_status() === FALSE){
			    $respuesta = array("mensaje" => "No se pudo obtener la cantidad actual del producto.", "resp" => "f");
			    $this->db->trans_rollback();
			    return $respuesta;
			}

			$row = $query->result();

			if (empty($row)) {
				$data_pt = array(
			        'id_producto' => $cambio_detalle_v[$i]["id_producto"],
			        'id_talla' => $cambio_detalle_v[$i]["id_talla"],
			        'codigo_barras' => '',
			        'id_almacen' => NULL,
			        'cantidad' => $cambio_detalle_v[$i]["cantidad"]
				);

				$this->db->insert('producto_talla', $data_pt);
			}else{
				$cantidad = (int)$row[0]->cantidad + (int)$cambio_detalle_v[$i]["cantidad"];

				$this->db->set('cantidad', $cantidad);
				$this->db->where('id_producto', $cambio_detalle_v[$i]["id_producto"]);
				$this->db->where('id_talla', $cambio_detalle_v[$i]["id_talla"]);
				$str = $this->db->update('producto_talla');
			}

			if ($this->db->trans_status() === FALSE){
			    $respuesta = array("mensaje" => "No se pudo insertar/actualizar la cantidad del producto.", "resp" => "f");
			    $this->db->trans_rollback();
			    return $respuesta;
			}

			$data_det = array(
		        'id_movimiento' => $id_ultimo_e,
		        'id_producto' => $cambio_detalle_v[$i]["id_producto"],
		        'id_talla' => $cambio_detalle_v[$i]["id_talla"],
		        'cantidad' => $cambio_detalle_v[$i]["cantidad"],
		        'precio' => $cambio_detalle_v[$i]["precio"]
			);

			$this->db->insert('detalle_movimiento', $data_det);

			if ($this->db->trans_status() === FALSE){
			    $respuesta = array("mensaje" => "No se pudo insertar el detalle del producto.", "resp" => "f");
			    $this->db->trans_rollback();
			    return $respuesta;
			}
		}

		$this->db->select('id_movimiento');
		$this->db->from('movimientos');
		$this->db->where('id_tipo_movimiento', 5);

		//echo $this->db->get_compiled_select();die;
		$query = $this->db->get();

		if ($this->db->trans_status() === FALSE){
		    $respuesta = array("mensaje" => "No se pudieron obtener los cambios de salida previos.", "resp" => "f");
		    $this->db->trans_rollback();
		    return $respuesta;
		}

		$row = count($query->result());

		$folio = 'CS' . ($row + 1);

		$data = array(
			'folio' => $folio,
	        'id_tipo_movimiento' => 5,
	        'cantidad' => $cambio_c["cantidad"],
	        'fecha' => $fecha_cambio,
	        'id_almacen' => $id_almacen,
	        'precio' => $cambio_c["precio"],
	        'confirmacion' => 1
		);

		$this->db->insert('movimientos', $data);

		if ($this->db->trans_status() === FALSE){
		    $respuesta = array("mensaje" => "No se pudo insertar el cambio de salida.", "resp" => "f");
		    $this->db->trans_rollback();
		    return $respuesta;
		}

		$id_ultimo_s = $this->db->insert_id();

		$data_s = array(
	        'id_movimiento' => $id_ultimo_s,
			'id_movimiento_sal' => $id_movimiento_venta
		);

		$this->db->insert('movimiento_confirmacion', $data_s);

		if ($this->db->trans_status() === FALSE){
		    $respuesta = array("mensaje" => "No se pudo insertar el movimiento de confirmacion.", "resp" => "f");
		    $this->db->trans_rollback();
		    return $respuesta;
		}

		for($i = 0; $i < count($cambio_detalle_c) ; $i++){

			$this->db->select('cantidad');
			$this->db->from('producto_talla');
			$this->db->where('id_producto', $cambio_detalle_c[$i]["id_producto"]);
			$this->db->where('id_talla', $cambio_detalle_c[$i]["id_talla"]);

			//echo $this->db->get_compiled_select();die;
			$query = $this->db->get();

			if ($this->db->trans_status() === FALSE){
			    $respuesta = array("mensaje" => "No se pudo obtener la cantidad actual del producto.", "resp" => "f");
			    $this->db->trans_rollback();
			    return $respuesta;
			}

			$row = $query->result();

			if (empty($row)) {
				$data_pt = array(
			        'id_producto' => $cambio_detalle_c[$i]["id_producto"],
			        'id_talla' => $cambio_detalle_c[$i]["id_talla"],
			        'codigo_barras' => '',
			        'id_almacen' => NULL,
			        'cantidad' => $cambio_detalle_c[$i]["cantidad"]
				);

				$this->db->insert('producto_talla', $data_pt);

			}else{
				$cantidad = (int)$row[0]->cantidad - (int)$cambio_detalle_c[$i]["cantidad"];

				$this->db->set('cantidad', $cantidad);
				$this->db->where('id_producto', $cambio_detalle_c[$i]["id_producto"]);
				$this->db->where('id_talla', $cambio_detalle_c[$i]["id_talla"]);
				$str = $this->db->update('producto_talla');
			}

			if ($this->db->trans_status() === FALSE){
			    $respuesta = array("mensaje" => "No se pudo insertar/actualizar la cantidad del producto.", "resp" => "f");
			    $this->db->trans_rollback();
			    return $respuesta;
			}

			$data_det = array(
		        'id_movimiento' => $id_ultimo_s,
		        'id_producto' => $cambio_detalle_c[$i]["id_producto"],
		        'id_talla' => $cambio_detalle_c[$i]["id_talla"],
		        'cantidad' => $cambio_detalle_c[$i]["cantidad"],
		        'precio' => $cambio_detalle_c[$i]["precio"]
			);

			$this->db->insert('detalle_movimiento', $data_det);

			if ($this->db->trans_status() === FALSE){
			    $respuesta = array("mensaje" => "No se pudo insertar el detalle del producto.", "resp" => "f");
			    $this->db->trans_rollback();
			    return $respuesta;
			}
		}

		$this->acciones_m->set_user_action($_SESSION["id_usuario"], "Se registro el cambio con id: " . $id_ultimo_e . ", " . $id_ultimo_s);
		$this->db->trans_commit();
		return $respuesta;

	}
}