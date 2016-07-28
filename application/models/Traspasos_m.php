<?php
class traspasos_m extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	function obtener_almacenes($id_usuario = null){

		$this->db->select('a.id_almacen, a.almacen');
		$this->db->from('almacenes a');

		if(!is_null($id_usuario)){
			$this->db->join('usuario_almacen ua', 'ua.id_almacen = a.id_almacen');
			$this->db->where('ua.id_usuario', $id_usuario);
		}

		$this->db->where("a.almacen <> 'TRASPASO' AND a.almacen <> 'TRANSITO'");
		$this->db->order_by('a.id_almacen', 'ASC');
		//echo $this->db->get_compiled_select();die;
		$query = $this->db->get();

		$row = $query->result();

		if (empty($row)) {
			$result = null;
		} else {
			$result = $row;
		}
		
		return $result;

	}

	function obtener_traspasos_e($cadena_almacenes){

		$this->db->select('id_movimiento');
		$this->db->from('transito');
		$this->db->where("id_almacen IN (" . $cadena_almacenes . ")");
		$this->db->where("id_tipo_movimiento", 3);
		$this->db->order_by('id_movimiento', 'ASC');
		//echo $this->db->get_compiled_select();die;
		$query = $this->db->get();

		$row = $query->result();

		if (empty($row)) {
			$result = null;
		} else {
			$result = $row;
		}
		
		return $result;

	}

	function obtener_traspasos_s($cadena_almacenes){

		$this->db->select('id_movimiento');
		$this->db->from('movimientos');
		$this->db->where("id_almacen IN (" . $cadena_almacenes . ")");
		$this->db->where("id_tipo_movimiento", 3);
		$this->db->where("confirmacion", 0);
		$this->db->order_by('id_movimiento', 'ASC');
		//echo $this->db->get_compiled_select();die;
		$query = $this->db->get();

		$row = $query->result();

		if (empty($row)) {
			$result = null;
		} else {
			$result = $row;
		}
		
		return $result;

	}

	function obtener_usuarios(){

		$this->db->select('id_usuario, usuario');
		$this->db->from('usuarios');
		//$this->db->join('usuario_almacen ua', 'ua.id_almacen = a.id_almacen');
		//$this->db->where('ua.id_usuario', $id_usuario);
		$this->db->order_by('usuario', 'ASC');
		
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

	function obtener_marcas(){

		$this->db->select('*');
		$this->db->from('marca');
		
		$query = $this->db->get();

		$row = $query->result();

		if (empty($row)) {
			$result = null;
		} else {
			$result = $row;
		}
		
		return $result;

	}

	function obtener_producto($codigo_barras = null, $id_almacen = null){

		$this->db->select('pt.id_producto_talla,p.id_producto,m.marca,p.modelo,p.descripcion,t.id_talla,t.talla,mv.id_tipo_movimiento,dm.cantidad,mv.confirmacion');
		$this->db->from('producto_talla pt');
		$this->db->join('productos p', 'p.id_producto = pt.id_producto');
		$this->db->join('marca m', 'm.id_marca = p.id_marca');
		$this->db->join('talla t', 't.id_talla = pt.id_talla');
		$this->db->join('detalle_movimiento dm', 'dm.id_producto = pt.id_producto AND dm.id_talla = pt.id_talla');
		$this->db->join('movimientos mv', 'mv.id_movimiento = dm.id_movimiento AND mv.id_almacen = ' . $id_almacen);

		if (!is_null($codigo_barras)){
			$this->db->where('pt.codigo_barras', $codigo_barras);
			$this->db->where('dm.cantidad > 0');
		}
		//echo $this->db->get_compiled_select();die;
		$query = $this->db->get();

		$row = $query->result();

		if (empty($row)) {
			$result = null;
		} else {
			$result = $row;
		}
		
		return $result;

	}

	function obtener_producto_modelo($marca = null, $modelo = null, $id_almacen = null){

		$this->db->select('p.id_producto,m.marca,p.modelo,p.descripcion');
		$this->db->from('productos p');
		$this->db->join('marca m', 'm.id_marca = p.id_marca');
		$this->db->join('detalle_movimiento dm', 'dm.id_producto = p.id_producto');
		$this->db->join('movimientos mv', 'mv.id_movimiento = dm.id_movimiento');

		if ($marca != 0){
			$this->db->where('p.id_marca', $marca);
		}

		if (!is_null($modelo) && $modelo != "" && $modelo != 0){
			$this->db->where('p.modelo', $modelo);
		}

		if (!is_null($id_almacen) && $id_almacen != ""){
			$this->db->where('mv.id_almacen', $id_almacen);
		}

		$this->db->group_by('p.id_producto,m.marca,p.modelo,p.descripcion');
		//echo $this->db->get_compiled_select();die;
		$query = $this->db->get();

		$row = $query->result();

		if (empty($row)) {
			$result = null;
		} else {
			$result = $row;
		}
		
		return $result;

	}

	function obtener_talla_cantidad($id_producto, $id_almacen, $id_talla = null){

		$this->db->select('pt.id_talla,mv.id_tipo_movimiento,dm.cantidad');
		$this->db->from('producto_talla pt');
		$this->db->join('detalle_movimiento dm', 'dm.id_producto = pt.id_producto AND dm.id_talla = pt.id_talla');
		$this->db->join('movimientos mv', 'mv.id_movimiento = dm.id_movimiento AND mv.id_almacen = ' . $id_almacen);
		$this->db->where('pt.id_producto', $id_producto);

		if(!is_null($id_talla)){
			$this->db->where('pt.id_talla', $id_talla);
		}

		$this->db->order_by('id_talla', 'ASC');

		//echo $this->db->get_compiled_select();die;
		$query = $this->db->get();

		$row = $query->result();

		if (empty($row)) {
			$result = null;
		} else {
			$result = $row;
		}
		
		return $result;

	}

	function obtener_cantidad_producto($id_producto, $id_talla, $id_almacen){

		$this->db->select('mv.id_tipo_movimiento,dm.cantidad');
		$this->db->from('producto_talla pt');
		$this->db->join('detalle_movimiento dm', 'dm.id_producto = pt.id_producto AND dm.id_talla = pt.id_talla');
		$this->db->join('movimientos mv', 'mv.id_movimiento = dm.id_movimiento AND mv.id_almacen = ' . $id_almacen);
		$this->db->where('pt.id_producto', $id_producto);
		$this->db->where('pt.id_talla', $id_talla);
		$this->db->order_by('id_talla', 'ASC');

		//echo $this->db->get_compiled_select();die;
		$query = $this->db->get();

		$row = $query->result();

		if (empty($row)) {
			$result = null;
		} else {
			$result = $row;
		}
		
		return $result;

	}

	function registrar_traspaso($traspaso, $traspaso_detalle){

		$fecha_traspaso = date("Y-m-d H:m:s");
		$insert = "";
		$mensaje = "";
		$str = 1;

		$this->db->trans_begin();

		$this->db->select('id_movimiento');
		$this->db->from('movimientos');
		$this->db->where('id_tipo_movimiento', 3);

		//echo $this->db->get_compiled_select();die;
		$query = $this->db->get();

		if ($this->db->trans_status() === FALSE){
		    $this->db->trans_rollback();
		}

		$row = count($query->result());

		if (is_int($row)) {

			$folio = 'TS' . ($row + 1);

			$data = array(
				'folio' => $folio,
		        'id_tipo_movimiento' => 3,
		        'cantidad' => $traspaso["cantidad"],
		        'fecha' => $fecha_traspaso,
		        'id_almacen' => $traspaso["id_almacen_s"],
		        'precio' => 0,
		        'confirmacion' => 0
			);

			$str = $this->db->insert('movimientos', $data);

			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			}

			$id_ultimo_e = $this->db->insert_id();

			if ($str == 1)
			{
				
				for($i = 0; $i < count($traspaso_detalle) ; $i++){

					$data_det = array(
				        'id_movimiento' => $id_ultimo_e,
				        'id_producto' => $traspaso_detalle[$i]["id_producto"],
				        'id_talla' => $traspaso_detalle[$i]["id_talla"],
				        'cantidad' => $traspaso_detalle[$i]["cantidad"],
				        'precio' => 0
					);

					$str = $this->db->insert('detalle_movimiento', $data_det);

					if ($str == 1)
					{
						$insert .= "-1";
					}
					else
					{
						$insert = "0";
					}

					$tipo_m = explode("-", $insert);

					if ($tipo_m[0] != '0') {
						$mensaje = "Se ingresaron los detalles de traspaso de salida correctamente.";
						$mensaje .= "|t";
					} else {
						$mensaje = "Error al insertar el detalle_movimiento, inténtelo de nuevo y si persiste el problema consulte al administrador del sistema.";
						$mensaje .= "|f";
					}

					if ($this->db->trans_status() === FALSE){
					    $this->db->trans_rollback();
					}

				}

				$data_t = array(
					'id_movimiento' => $id_ultimo_e,
			        'id_almacen' => $traspaso["id_almacen_e"],
			        'id_tipo_movimiento' => 3
				);

				$str = $this->db->insert('transito', $data_t);

				if ($this->db->trans_status() === FALSE){
				    $this->db->trans_rollback();
				    $mensaje = "Error al insertar el movimiento, inténtelo de nuevo y si persiste el problema consulte al administrador del sistema.";
					$mensaje .= "|f";
				}else{
					$this->acciones_m->set_user_action($_SESSION["id_usuario"], "Se registro el traspaso de salida con id: " . $id_ultimo_e);
				    $this->db->trans_commit();
				}
			}
			else
			{
				$mensaje = "Error al insertar el movimiento, inténtelo de nuevo y si persiste el problema consulte al administrador del sistema.";
				$mensaje .= "|f";
			}

		}else{
			$mensaje = "Error al consultar los movimientos de traspaso, intentelo nuevamente.";
			$mensaje .= "|f";
		}

		return $mensaje;

	}

	function obtener_traspasos($movs){

		$this->db->select('tra.id_almacen as almacen_e,a.id_almacen,mv.id_movimiento,m.marca,p.modelo,p.descripcion,t.talla,dm.cantidad');
		$this->db->from('producto_talla pt');
		$this->db->join('productos p', 'p.id_producto = pt.id_producto');
		$this->db->join('marca m', 'm.id_marca = p.id_marca');
		$this->db->join('talla t', 't.id_talla = pt.id_talla');
		$this->db->join('detalle_movimiento dm', 'dm.id_producto = pt.id_producto AND dm.id_talla = pt.id_talla');
		$this->db->join('movimientos mv', 'mv.id_movimiento = dm.id_movimiento');
		$this->db->join('almacenes a', 'a.id_almacen = mv.id_almacen');
		$this->db->join('transito tra', 'tra.id_movimiento = mv.id_movimiento', 'left');
		$this->db->where('mv.id_movimiento IN (' . $movs . ')');
		$this->db->order_by('mv.id_movimiento,dm.id_detalle_movimiento', 'ASC');

		//echo $this->db->get_compiled_select();die;
		$query = $this->db->get();

		$row = $query->result();

		if (empty($row)) {
			$result = null;
		} else {
			$result = $row;
		}
		
		return $result;

	}

	function confirmar_traspasos($id_movimientos){
		$fecha_confirmacion = date("Y-m-d H:m:s");
		$insert = "";
		$str = 1;
		$mov_te = 0;
		$respuesta = array('mensaje' => 'Se han confirmado correctamente los movimientos de traspaso.', 'resp' => 't');

		$this->db->trans_begin();

		$this->db->select('m.id_movimiento,m.cantidad,m.id_almacen as id_almacen_s,t.id_almacen as id_almacen_e');
		$this->db->from('movimientos m');
		$this->db->join('transito t', 't.id_movimiento = m.id_movimiento');
		$this->db->where('m.id_movimiento IN (' . $id_movimientos . ')');

		//echo $this->db->get_compiled_select();die;
		$query = $this->db->get();

		if ($this->db->trans_status() === FALSE){
		    $this->db->trans_rollback();
		}

		$row = $query->result();
		$count_row = count($row);

		if ($count_row > 0){

			$this->db->select('id_movimiento');
			$this->db->from('movimientos');
			$this->db->where('id_tipo_movimiento', 7);

			//echo $this->db->get_compiled_select();die;
			$query_e = $this->db->get();

			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			}

			$row_e = $query_e->result();
			$count_row_e = count($row_e);

			if($count_row_e > 0){
				$mov_te = $count_row_e; 
			}

			foreach($row as $movimiento){

				$folio = 'TE' . ($mov_te += 1);

				$data = array(
					'folio' => $folio,
			        'id_tipo_movimiento' => 7,
			        'cantidad' => $movimiento->cantidad,
			        'fecha' => $fecha_confirmacion,
			        'id_almacen' => $movimiento->id_almacen_e,
			        'precio' => 0,
			        'confirmacion' => 1
				);

				$str = $this->db->insert('movimientos', $data);

				if ($this->db->trans_status() === FALSE){
				    $this->db->trans_rollback();
				}

				$id_ultimo_e = $this->db->insert_id();

				$this->db->select('*');
				$this->db->from('detalle_movimiento');
				$this->db->where('id_movimiento', $movimiento->id_movimiento);

				//echo $this->db->get_compiled_select();die;
				$query_dm = $this->db->get();

				if ($this->db->trans_status() === FALSE){
				    $this->db->trans_rollback();
				}

				$row_dm = $query_dm->result();

				foreach($row_dm as $detalle_m){
					
					$data = array(
				        'id_movimiento' => $id_ultimo_e,
				        'id_producto' => $detalle_m->id_producto,
				        'id_talla' => $detalle_m->id_talla,
				        'cantidad' => $detalle_m->cantidad,
				        'precio' => 0
					);

					$str = $this->db->insert('detalle_movimiento', $data);

					if ($this->db->trans_status() === FALSE){
					    $this->db->trans_rollback();
					}
					
				}
				

				$this->db->set('confirmacion', 1);
				$this->db->where('id_movimiento', $movimiento->id_movimiento);
				$this->db->update('movimientos');

				if($this->db->trans_status() === FALSE){
					$respuesta = array('mensaje' => 'No se actualizo el movimiento con id: ' . $movimiento->id_movimiento . '.', 'resp' => 'f');
				    $this->db->trans_rollback();
				    return $respuesta;
				}

				$this->db->where('id_movimiento', $movimiento->id_movimiento);
				$this->db->where('id_almacen', $movimiento->id_almacen_e);
				$this->db->delete('transito');

				if($this->db->trans_status() === FALSE){
					$respuesta = array('mensaje' => 'No se elimino el movimiento de transito con id: ' . $movimiento->id_movimiento . '.', 'resp' => 'f');
				    $this->db->trans_rollback();
				    return $respuesta;
				}

				$data_e = array(
					'id_movimiento' => $id_ultimo_e,
			        'id_movimiento_sal' => $movimiento->id_movimiento
				);

				$this->db->insert('movimiento_confirmacion', $data_e);

				if($this->db->trans_status() === FALSE){
					$respuesta = array('mensaje' => 'No se inserto el movimiento de confirmacion con id: ' . $movimiento->id_movimiento . '.', 'resp' => 'f');
				    $this->db->trans_rollback();
				    return $respuesta;
				}

			}
		}else{
			$respuesta = array('mensaje' => 'No existen traspasos pendientes movimiento o ya se confirmaron los traspasos pendientes.', 'resp' => 'f');
		    $this->db->trans_rollback();
		    return $respuesta;
		}

		$this->acciones_m->set_user_action($_SESSION["id_usuario"], "Se confirmo el o los traspasos con los id_movimientos: " . $id_movimientos);
		$this->db->trans_commit();

		return $respuesta;
	}

	function cancelar_movimientos($id_movimientos){
		$id_movimiento = explode(",", $id_movimientos);
		$fecha_cancelacion = date("Y-m-d H:m:s");
		$respuesta = array('mensaje' => 'Se han cancelado correctamente los movimientos de traspaso.', 'resp' => 't');

		$this->db->trans_begin();

		foreach ($id_movimiento as $movimiento) {
			$mov = explode("|", $movimiento);
			$this->db->set('confirmacion', -1);
			$this->db->where('id_movimiento', $mov[0]);
			$this->db->where('id_almacen', $mov[1]);
			$str = $this->db->update('movimientos');

			if ($this->db->trans_status() === FALSE){
				$respuesta = array('mensaje' => 'No se actualizo el movimiento de cancelacion con id: ' . $mov[0] . '.', 'resp' => 'f');
			    $this->db->trans_rollback();
			    return $mensaje;
			}
			
			$this->db->where('id_movimiento', $mov[0]);
			$this->db->where('id_almacen', $mov[1]);
			$this->db->delete('transito');

			if ($this->db->trans_status() === FALSE){
				$respuesta = array('mensaje' => 'No se borro el movimiento de transito con id: ' . $mov[0] . '.', 'resp' => 'f');
			    $this->db->trans_rollback();
			    return $mensaje;
			}
		}

		$this->acciones_m->set_user_action($_SESSION["id_usuario"], "Se cancelo el o los traspasos con los id_movimientos: " . $id_movimientos);
		$this->db->trans_commit();
		return $respuesta;

	}
}