<?php
class Ajuste_inventario_m extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	function obtener_almacenes($id_usuario){

		$this->db->select('a.id_almacen, a.almacen');
		$this->db->from('almacenes a');
		$this->db->join('usuario_almacen ua', 'ua.id_almacen = a.id_almacen');
		$this->db->where('ua.id_usuario', $id_usuario);
		$this->db->order_by('a.id_almacen', 'ASC');
		
		$query = $this->db->get();

		$row = $query->result();

		if (empty($row)) {
			$result = null;
		} else {
			$result = $row;
		}
		
		return $result;

	}

	function obtener_tipo_movimiento(){

		$this->db->select('*');
		$this->db->from('tipo_movimiento');
		$this->db->where('id_tipo_movimiento IN (6,9)');
		$this->db->order_by('id_tipo_movimiento', 'ASC');
		
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

	function obtener_datos_ajuste($id_almacen, $id_tipo_movimiento){

		$this->db->select('af.id_producto,af.id_talla,m.marca,p.modelo,p.descripcion,t.talla,af.cantidad_sistema,af.cantidad_fisica,af.diferencia');
		$this->db->from('ajuste_fisico af');
		$this->db->join('producto_talla pt', 'pt.id_producto = af.id_producto AND pt.id_talla = af.id_talla');
		$this->db->join('productos p', 'p.id_producto = pt.id_producto');
		$this->db->join('marca m', 'm.id_marca = p.id_marca');
		$this->db->join('talla t', 't.id_talla = pt.id_talla');
		$this->db->where('af.id_almacen', $id_almacen);

		if ($id_tipo_movimiento == 6){
			$this->db->where('af.diferencia > 0');
		}else{
			$this->db->where('af.diferencia < 0');
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

	function registrar_ajuste($ajuste, $ajuste_detalle){

		$fecha_ajuste = date("Y-m-d H:m:s");
		$respuesta = array("mensaje" => "Se registro correctamente el ajuste de inventario.", "resp" => "t");

		$this->db->trans_begin();

		$this->db->select('id_movimiento');
		$this->db->from('movimientos');
		$this->db->where('id_tipo_movimiento', $ajuste["id_tipo_movimiento"]);

		//echo $this->db->get_compiled_select();die;
		$query = $this->db->get();

		if ($this->db->trans_status() === FALSE){
		    $respuesta = array('mensaje' => 'No se pudieron consultar los datos de ajustes, inténtelo de nuevo y en caso de persistir el problema comuniquese con el administrador del sistema.', 'resp' => 'f');
		    $this->db->trans_rollback();
		    return $respuesta;
		}

		$row = count($query->result());

		if($ajuste["id_tipo_movimiento"] == 6){
			$tipo = 'AS';
		}else{
			$tipo = 'AE';
		}

		$folio = $tipo . ($row + 1);

		$data = array(
			'folio' => $folio,
	        'id_tipo_movimiento' => $ajuste["id_tipo_movimiento"],
	        'cantidad' => abs($ajuste["cantidad"]),
	        'precio' => 0,
	        'fecha' => $fecha_ajuste,
	        'id_almacen' => $ajuste["id_almacen"],
	        'confirmacion' => 1
		);

		$this->db->insert('movimientos', $data);

		if ($this->db->trans_status() === FALSE){
		    $respuesta = array('mensaje' => 'No se pudieron ingresar los datos del movimiento, inténtelo de nuevo y en caso de persistir el problema comuniquese con el administrador del sistema.', 'resp' => 'f');
		    $this->db->trans_rollback();
		    return $respuesta;
		}

		$id_ultimo_a = $this->db->insert_id();
		
		for($i = 0; $i < count($ajuste_detalle) ; $i++){

			if($ajuste_detalle[$i]["check"] == "true"){

				$this->db->select('cantidad');
				$this->db->from('producto_talla');
				$this->db->where('id_producto', $ajuste_detalle[$i]["id_producto"]);
				$this->db->where('id_talla', $ajuste_detalle[$i]["id_talla"]);

				//echo $this->db->get_compiled_select();die;
				$query = $this->db->get();

				if ($this->db->trans_status() === FALSE){
				    $respuesta = array('mensaje' => 'No se pudo obtener la cantidad del id_producto ' . $ajuste_detalle[$i]["id_producto"] . ' y id_talla ' . $ajuste_detalle[$i]["id_talla"] . ', inténtelo de nuevo y en caso de persistir el problema comuniquese con el administrador del sistema.', 'resp' => 'f');
				    $this->db->trans_rollback();
				    return $respuesta;
				}

				$row = $query->result();

				if (empty($row)) {
					$data_pt = array(
				        'id_producto' => $ajuste_detalle[$i]["id_producto"],
				        'id_talla' => $ajuste_detalle[$i]["id_talla"],
				        'codigo_barras' => '',
				        'id_almacen' => NULL,
				        'cantidad' => abs($ajuste_detalle[$i]["cantidad"])
					);

					$this->db->insert('producto_talla', $data_pt);
				}else{
					if($ajuste["id_tipo_movimiento"] == 6){
						$cantidad = $row[0]->cantidad - abs($ajuste_detalle[$i]["cantidad"]);
					}else{
						$cantidad = $row[0]->cantidad + abs($ajuste_detalle[$i]["cantidad"]);
					}

					if($cantidad < 0){
						$respuesta = array('mensaje' => 'No se puede actualizar cantidad del producto con id ' . $ajuste_detalle[$i]["id_producto"] . ' y id_talla ' . $ajuste_detalle[$i]["id_talla"] . ' ya que es menor a 0, inténtelo de nuevo y en caso de persistir el problema comuniquese con el administrador del sistema.', 'resp' => 'f');
					    $this->db->trans_rollback();
					    return $respuesta;
					}

					$this->db->set('cantidad', $cantidad);
					$this->db->where('id_producto', $ajuste_detalle[$i]["id_producto"]);
					$this->db->where('id_talla', $ajuste_detalle[$i]["id_talla"]);
					$str = $this->db->update('producto_talla');
				}

				if ($this->db->trans_status() === FALSE){
				    $respuesta = array('mensaje' => 'No se pudo insertar/actualizar el id_producto ' . $ajuste_detalle[$i]["id_producto"] . ' y id_talla ' . $ajuste_detalle[$i]["id_talla"] . ', inténtelo de nuevo y en caso de persistir el problema comuniquese con el administrador del sistema.', 'resp' => 'f');
				    $this->db->trans_rollback();
				    return $respuesta;
				}
					
				$data_det = array(
			        'id_movimiento' => $id_ultimo_a,
			        'id_producto' => $ajuste_detalle[$i]["id_producto"],
			        'id_talla' => $ajuste_detalle[$i]["id_talla"],
			        'cantidad' => abs($ajuste_detalle[$i]["cantidad"]),
			        'precio' => 0
				);

				$this->db->insert('detalle_movimiento', $data_det);

				if ($this->db->trans_status() === FALSE){
				    $respuesta = array('mensaje' => 'No se pudo insertar el id_producto ' . $ajuste_detalle[$i]["id_producto"] . ' y id_talla ' . $ajuste_detalle[$i]["id_talla"] . ', inténtelo de nuevo y en caso de persistir el problema comuniquese con el administrador del sistema.', 'resp' => 'f');
				    $this->db->trans_rollback();
				    return $respuesta;
				}

				$this->db->where('id_almacen', $ajuste["id_almacen"]);
				$this->db->where('id_producto', $ajuste_detalle[$i]["id_producto"]);
				$this->db->where('id_talla', $ajuste_detalle[$i]["id_talla"]);
				$this->db->delete('ajuste_fisico');

				if ($this->db->trans_status() === FALSE){
				    $respuesta = array('mensaje' => 'No se pudo borrar el id_producto ' . $ajuste_detalle[$i]["id_producto"] . ' y id_talla ' . $ajuste_detalle[$i]["id_talla"] . ' de los ajustes pendientes, inténtelo de nuevo y en caso de persistir el problema comuniquese con el administrador del sistema.', 'resp' => 'f');
				    $this->db->trans_rollback();
				    return $respuesta;
				}

			}

		}

		$this->acciones_m->set_user_action($_SESSION["id_usuario"], "Se realizó ajuste de inventario en esta fecha: " . $fecha_ajuste);
		$this->db->trans_commit();
		return $respuesta;

	}

}