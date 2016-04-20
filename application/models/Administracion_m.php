<?php
class Administracion_m extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	function obtener_modulos(){
		$sql = "SELECT permiso FROM permisos";

		$query = $this->db->query($sql);
        $rows = $query->result();

		if (isset($rows))
		{
		    return $rows;
		}else{
      		return null;
      	}
	}

	function obtener_usuarios(){
		$sql = "SELECT id_usuario, usuario, password FROM usuarios";

		$query = $this->db->query($sql);
        $rows = $query->result();

		if (isset($rows))
		{
		    return $rows;
		}else{
      		return null;
      	}
	}

	function contador_permisos(){
		$sql = "SELECT count(id_permiso) as c_permisos FROM permisos";

		$query = $this->db->query($sql);
        $rows = $query->result();

		if (isset($rows))
		{
		    return $rows;
		}else{
      		return null;
      	}
	}

	function obtener_u_permisos(){
		$sql = "SELECT u_p.id_usuario, u_p.id_permiso
				FROM usuario_permisos u_p
				INNER JOIN permisos p ON (p.id_permiso = u_p.id_permiso)";

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