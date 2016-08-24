<?php
class Reportes_m extends CI_Model{

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

	function obtener_tipo_movimiento(){

		$this->db->select('*');
		$this->db->from('tipo_movimiento');
		$this->db->order_by('id_tipo_movimiento', 'ASC');
		
		$query = $this->db->get();

		$row = $query->result();

		if (empty($row)) {
			$result = null;
		} else {
			$result = $row;
		}
		
		return $result;

	}

	function obtener_movimientos($id_almacen, $limit = null, $offset = null, $folio = null, $tipo_movimiento = null, $fecha_inicio = null, $fecha_fin = null){

		$this->db->select('m.id_movimiento,tm.id_tipo_movimiento,tm.tipo_movimiento,m.folio,a.almacen,m.fecha,m.cantidad,m.precio,m.confirmacion');
		$this->db->from('movimientos m');
		$this->db->join('tipo_movimiento tm', 'tm.id_tipo_movimiento = m.id_tipo_movimiento');
		$this->db->join('almacenes a', 'a.id_almacen = m.id_almacen');
		$this->db->where('a.id_almacen', $id_almacen);

		if (!is_null($folio)){
			$this->db->where('m.folio', $folio);
		}

		if (!is_null($tipo_movimiento) && $tipo_movimiento != "0"){
			$this->db->where('tm.id_tipo_movimiento', $tipo_movimiento);
		}

		if (!is_null($fecha_inicio)){
			if (is_null($fecha_fin)){
				$fecha_fin = date("Y-m-d");
			}

			$this->db->where('m.fecha >= CAST(\'' . $fecha_inicio . ' 00:00:00\' AS DATETIME)');
			$this->db->where('m.fecha <= CAST(\'' . $fecha_fin . ' 23:59:59\' AS DATETIME)');
		}

		if(!is_null($limit)){
			$this->db->limit($limit, $offset);
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

	function obtener_detalles_movimiento($id_movimiento){

		$this->db->select('pt.id_producto,dm.cantidad,mc.marca,p.modelo,p.descripcion,t.talla,dm.precio');
		$this->db->from('movimientos m');
		$this->db->join('detalle_movimiento dm', 'dm.id_movimiento = m.id_movimiento');
		$this->db->join('producto_talla pt', 'pt.id_producto = dm.id_producto AND pt.id_talla = dm.id_talla');
		$this->db->join('talla t', 't.id_talla = pt.id_talla');
		$this->db->join('productos p', 'p.id_producto = pt.id_producto');
		$this->db->join('marca mc', 'mc.id_marca = p.id_marca');
		$this->db->where('m.id_movimiento', $id_movimiento);

		$this->db->order_by('pt.id_producto,pt.id_talla', 'ASC');

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

	function obtener_movimiento_producto($id_almacen,$id_tipo_movimiento){

		$this->db->select('p.id_producto,m.marca,p.modelo,p.descripcion,mv.id_tipo_movimiento');
		$this->db->from('producto_talla pt');
		$this->db->join('productos p', 'p.id_producto = pt.id_producto');
		$this->db->join('marca m', 'm.id_marca = p.id_marca');
		$this->db->join('detalle_movimiento dm', 'dm.id_producto = pt.id_producto AND dm.id_talla = pt.id_talla');
		$this->db->join('movimientos mv', 'mv.id_movimiento = dm.id_movimiento AND mv.id_almacen = ' . $id_almacen . ' AND mv.id_tipo_movimiento = ' . $id_tipo_movimiento);
		$this->db->group_by('p.id_producto,m.marca,p.modelo,p.descripcion');
		$this->db->order_by('m.marca,p.modelo', 'ASC');

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

	function obtener_talla_cantidad($id_almacen, $id_producto, $id_talla, $id_tipo_movimiento){

		$this->db->select('pt.id_talla,mv.id_tipo_movimiento,dm.cantidad,mv.confirmacion');
		$this->db->from('producto_talla pt');
		$this->db->join('detalle_movimiento dm', 'dm.id_producto = pt.id_producto AND dm.id_talla = pt.id_talla');
		$this->db->join('movimientos mv', 'mv.id_movimiento = dm.id_movimiento AND mv.id_almacen = ' . $id_almacen . " AND mv.id_tipo_movimiento = " . $id_tipo_movimiento);
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

}