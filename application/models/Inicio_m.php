<?php
class Inicio_m extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	function obtener_datos_usuario($idusuario = null){
		$sql = "SELECT * FROM usuarios where id_usuario = '" . $idusuario . "';";

		$query = $this->db->query($sql);
        $rows = $query->result();

		if (isset($rows))
		{
		    return $rows;
		}else{
      		return null;
      	}
	}

}