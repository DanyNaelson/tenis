<?php
class Administracion_m extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	function obtener_modulos(){
		$sql = "SELECT permiso FROM permisos ORDER BY id_permiso";

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
		$sql = "SELECT id_usuario, usuario, password FROM usuarios ORDER BY id_usuario";

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
				INNER JOIN permisos p ON (p.id_permiso = u_p.id_permiso)
				ORDER BY u_p.id_usuario, u_p.id_permiso";

		$query = $this->db->query($sql);
        $rows = $query->result_array();

		if (isset($rows))
		{
		    return $rows;
		}else{
      		return null;
      	}
	}

	function actualizar_usuario($datos_usuario){
		$d_usuario = explode("-", $datos_usuario);
		$mensaje = "Se actualizaron correctamente los datos del usuario";

		$this->db->trans_begin();

		$this->db->set('usuario', $d_usuario[1]);
		$this->db->set('password', $d_usuario[2]);
		$this->db->where('id_usuario', $d_usuario[0]);
		$this->db->update('usuarios');

		if ($this->db->trans_status() === FALSE){
			$mensaje = "Error al actualizar datos de acceso del usuario.";
		    $this->db->trans_rollback();
		    return $mensaje;
		}

		$this->db->where('id_usuario', $d_usuario[0]);
		$this->db->delete('usuario_permisos');

		if ($this->db->trans_status() === FALSE){
		    $mensaje = "Error al borrar el usuario.";
		    $this->db->trans_rollback();
		    return $mensaje;
		}

		for($i = 3; $i < count($d_usuario) ; $i++){
			if($d_usuario[$i] == 1){
				$data = array(
			        'id_usuario' => $d_usuario[0],
			        'id_permiso' => $i - 2
				);

				$this->db->insert('usuario_permisos', $data);

				if ($this->db->trans_status() === FALSE){
				    $mensaje = "Error al insertar datos del usuario.";
				    $this->db->trans_rollback();
				    return $mensaje;
				}	
			}
		}

		$this->acciones_m->set_user_action($_SESSION["id_usuario"], "Se actualizaron los permisos del usuario: " . $d_usuario[1]);
		$this->db->trans_commit();
		
		return $mensaje;
	}

	function borrar_usuario($datos_usuario){
		$id_usuario = $datos_usuario;
		$mensaje = "Se eliminó correctamente el usuario";

		$this->db->trans_begin();

		$this->db->where('id_usuario', $id_usuario);
		$this->db->delete('usuarios');

		if ($this->db->trans_status() === FALSE){
		    $mensaje = "Error al eliminar el usuario.";
		    $this->db->trans_rollback();
		    return $mensaje;
		}

		$this->db->where('id_usuario', $id_usuario);
		$this->db->delete('usuario_permisos');

		if ($this->db->trans_status() === FALSE){
		    $mensaje = "Error al eliminar los permisos del usuario.";
		    $this->db->trans_rollback();
		    return $mensaje;
		}
		
		$this->acciones_m->set_user_action($_SESSION["id_usuario"], "Se eliminó el id_usuario: " . $id_usuario);
	    $this->db->trans_commit();

		return $mensaje;
	}

	function insertar_usuario($datos_usuario){
		$d_usuario = explode("-", $datos_usuario);
		$mensaje = "Se agregó correctamente al usuario.";

		$this->db->trans_begin();

		$data = array(
	        'usuario' => $d_usuario[1],
	        'password' => $d_usuario[2]
		);

		$this->db->insert('usuarios', $data);

		if ($this->db->trans_status() === FALSE){
		    $mensaje = "Error al insertar el usuario.";
		    $this->db->trans_rollback();
		    return $mensaje;
		}

		$id_ultimo = $this->db->insert_id();

		for($i = 3; $i < count($d_usuario) ; $i++){
			if($d_usuario[$i] == 1){
				$data = array(
			        'id_usuario' => $id_ultimo,
			        'id_permiso' => $i - 2
				);

				$this->db->insert('usuario_permisos', $data);

				if ($this->db->trans_status() === FALSE){
				    $mensaje = "Error al crear el permiso del usuario.";
				    $this->db->trans_rollback();
				    return $mensaje;
				}
			}
		}

		$this->acciones_m->set_user_action($_SESSION["id_usuario"], "Se ingresaron correctamente los datos y permisos del usuario: " . $d_usuario[1]);
		$this->db->trans_commit();

		return $mensaje;
	}
}