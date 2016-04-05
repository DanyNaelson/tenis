<?php
class Login_m extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	function obtener_usuarios($usuario = NULL, $inicio = NULL, $fin = NULL){
		$sql = "SELECT * FROM usuarios ";
		
		if($usuario != NULL){
        	$sql .= "where usuario = '" . $usuario . "'";
        }

        if($inicio != NULL && $fin != NULL){
        	$sql .= " LIMIT " . $fin . " OFFSET " . $inicio;
        }

        $sql .= ";";

        $query = $this->db->query($sql);
        $rows = $query->result();

        $det_usuario = array();

		if (isset($rows))
		{
		    return $rows;
		}else{
      		return 0;
      	}
	}

}