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

}