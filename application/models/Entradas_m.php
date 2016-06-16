<?php
class Entradas_m extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	function obtener_almacenes(){

		$this->db->select('*');
		$this->db->from('almacenes');

		$array_where = array("almacen <> " => "TRASPASO", "almacen <> " => "TRANSITO");

		$this->db->where($array_where);
		
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

	function obtener_producto($codigo_barras = null){

		$this->db->select('p.id_producto,m.marca,p.modelo,p.descripcion,t.talla');
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

}