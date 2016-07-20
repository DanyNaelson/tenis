<?php
class Acciones_m extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	function set_user_action($id_usuario, $accion_usuario){
		$fecha_actual = date("Y-m-d H:m:s");

		$data = array(
			        'id_usuario' => $id_usuario,
			        'accion' => $accion_usuario,
			        'fecha_hora' => $fecha_actual
				);
						
		$this->db->insert('acciones_usuarios', $data);
	}

}