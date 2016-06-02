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

	function obtener_usuario_permisos($idusuario){
		$sql = "SELECT u_p.id_permiso, p.permiso FROM usuario_permisos u_p INNER JOIN permisos p ON (p.id_permiso = u_p.id_permiso) WHERE u_p.id_usuario = '" . $idusuario . "' ORDER BY p.id_permiso";

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