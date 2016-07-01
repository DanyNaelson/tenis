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
		$insert = "";

		$this->db->trans_begin();

		$this->db->set('usuario', $d_usuario[1]);
		$this->db->set('password', $d_usuario[2]);
		$this->db->where('id_usuario', $d_usuario[0]);
		$str = $this->db->update('usuarios');

		if ($this->db->trans_status() === FALSE){
		    $this->db->trans_rollback();
		}

		if ($str == 1)
		{
			$this->db->where('id_usuario', $d_usuario[0]);
			$str = $this->db->delete('usuario_permisos');

			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			}

			if ($str == 1)
			{
				for($i = 3; $i < count($d_usuario) ; $i++){
					if($d_usuario[$i] == 1){
						$data = array(
					        'id_usuario' => $d_usuario[0],
					        'id_permiso' => $i - 2
						);

						$str = $this->db->insert('usuario_permisos', $data);

						if ($str == 1)
						{
							$insert .= "-1";
						}
						else
						{
							$insert = "0";
						}

						if ($this->db->trans_status() === FALSE){
						    $this->db->trans_rollback();
						}else{
							$this->acciones_m->set_user_action($_SESSION["id_usuario"], "Se actualizó el id_permiso: " . ($i - 2) . " del usuario: " . $d_usuario[1]);
						    $this->db->trans_commit();
						}
					}

					$tipo_m = explode("-", $insert);

					if ($tipo_m[0] != '0') {
						$mensaje = "Se actualizaron los datos del usuario correctamente.";
					} else {
						$mensaje = "Error al insertar permisos del usuario.";
					}
				}
			}
			else
			{
				$mensaje = "Error al borrar permisos del usuario.";
			}
		}
		else
		{
			$mensaje = "Error al actualizar los datos de usuario.";
		}

		return $mensaje;
	}

	function borrar_usuario($datos_usuario){
		$id_usuario = $datos_usuario;

		$this->db->trans_begin();

		$this->db->where('id_usuario', $id_usuario);
		$str = $this->db->delete('usuarios');

		if ($this->db->trans_status() === FALSE){
		    $this->db->trans_rollback();
		}

		if ($str == 1)
		{
			$this->db->where('id_usuario', $id_usuario);
			$str = $this->db->delete('usuario_permisos');

			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			}else{
				$this->acciones_m->set_user_action($_SESSION["id_usuario"], "Se eliminó el id_usuario: " . $id_usuario);
			    $this->db->trans_commit();
			}

			if ($str == 1)
			{
				$mensaje = "Se borraron los permisos del usuario correctamente.";
			}
			else
			{
				$mensaje = "Error al borrar permisos del usuario.";
			}
		}
		else
		{
			$mensaje = "Error al borrar los datos de usuario.";
		}

		return $mensaje;
	}

	function insertar_usuario($datos_usuario){
		$d_usuario = explode("-", $datos_usuario);
		$insert = "";

		$this->db->trans_begin();

		$data = array(
	        'usuario' => $d_usuario[1],
	        'password' => $d_usuario[2]
		);

		$str = $this->db->insert('usuarios', $data);

		if ($this->db->trans_status() === FALSE){
		    $this->db->trans_rollback();
		}

		$id_ultimo = $this->db->insert_id();

		if ($str == 1)
		{
			for($i = 3; $i < count($d_usuario) ; $i++){
				if($d_usuario[$i] == 1){
					$data = array(
				        'id_usuario' => $id_ultimo,
				        'id_permiso' => $i - 2
					);

					$str = $this->db->insert('usuario_permisos', $data);

					if ($str == 1)
					{
						$insert .= "-1";
					}
					else
					{
						$insert = "0";
					}
				}

				$tipo_m = explode("-", $insert);

				if ($tipo_m[0] != '0') {
					$mensaje = "Se ingresó el usuario correctamente.";
				} else {
					$mensaje = "Error al insertar permisos del usuario.";
				}

				if ($this->db->trans_status() === FALSE){
				    $this->db->trans_rollback();
				}else{
					$this->acciones_m->set_user_action($_SESSION["id_usuario"], "Se ingreso el id_permiso: " . ($i - 2) . " del usuario: " . $d_usuario[1]);
				    $this->db->trans_commit();
				}
			}
		}
		else
		{
			$mensaje = "Error al actualizar los datos de usuario.";
		}

		return $mensaje;
	}
}