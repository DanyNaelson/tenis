<?php
class Login_m extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	function obtener_usuarios($usuario = null, $pass = null, $fin = null, $inicio = null){
		$sql = "SELECT * FROM usuarios ";
		
		if($usuario != null){
        	$sql .= "where usuario = '" . $usuario . "'";
        }

        if($pass != null){
        	$sql .= " and password = '" . $pass . "'";
        }

        if($fin != null){
        	$sql .= " LIMIT " . $fin;
        	if($inicio != null){
        		$sql .= " OFFSET " . $inicio;
        	}
        }

        $sql .= ";";

        $query = $this->db->query($sql);
        $rows = $query->result();

        $det_usuario = array();

		if (isset($rows))
		{
		    return $rows;
		}else{
      		return null;
      	}
	}

}