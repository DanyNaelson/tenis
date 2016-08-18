<?php
class Reportes_m extends CI_Model{

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

	function obtener_movimientos($id_almacen, $limit = null, $offset = null, $folio = null, $tipo_movimiento = null, $fecha_inicio = null, $fecha_fin = null){

		$this->db->select('m.id_movimiento,tm.id_tipo_movimiento,tm.tipo_movimiento,m.folio,a.almacen,m.fecha,m.cantidad,m.precio,m.confirmacion');
		$this->db->from('movimientos m');
		$this->db->join('tipo_movimiento tm', 'tm.id_tipo_movimiento = m.id_tipo_movimiento');
		$this->db->join('almacenes a', 'a.id_almacen = m.id_almacen');
		$this->db->where('a.id_almacen', $id_almacen);

		if (!is_null($folio)){
			$this->db->where('m.folio', $folio);
		}

		if (!is_null($tipo_movimiento) && $tipo_movimiento != "0"){
			$this->db->where('tm.id_tipo_movimiento', $tipo_movimiento);
		}

		if (!is_null($fecha_inicio)){
			if (is_null($fecha_fin)){
				$fecha_fin = date("Y-m-d");
			}

			$this->db->where('m.fecha >= CAST(\'' . $fecha_inicio . ' 00:00:00\' AS DATETIME)');
			$this->db->where('m.fecha <= CAST(\'' . $fecha_fin . ' 23:59:59\' AS DATETIME)');
		}

		if(!is_null($limit)){
			$this->db->limit($limit, $offset);
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

	function obtener_detalles_movimiento($id_movimiento){

		$this->db->select('pt.id_producto,dm.cantidad,mc.marca,p.modelo,p.descripcion,t.talla,dm.precio');
		$this->db->from('movimientos m');
		$this->db->join('detalle_movimiento dm', 'dm.id_movimiento = m.id_movimiento');
		$this->db->join('producto_talla pt', 'pt.id_producto = dm.id_producto AND pt.id_talla = dm.id_talla');
		$this->db->join('talla t', 't.id_talla = pt.id_talla');
		$this->db->join('productos p', 'p.id_producto = pt.id_producto');
		$this->db->join('marca mc', 'mc.id_marca = p.id_marca');
		$this->db->where('m.id_movimiento', $id_movimiento);

		$this->db->order_by('pt.id_producto,pt.id_talla', 'ASC');

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

	function cancelar_movimiento($id_movimiento){

		$fecha_cancelacion = date("Y-m-d H:m:s");
		$insert = "";
		$mensaje = "";
		$str = 1;
		$respuesta = array("mensaje" => "Se canceló el movimiento correctamente.", "resp" => "t");

		$this->db->trans_begin();

		$this->db->set('confirmacion', -1);
		$this->db->where('id_movimiento', $id_movimiento);
		$str = $this->db->update('movimientos');

		if ($this->db->trans_status() === FALSE){
		    $this->db->trans_rollback();
		    $respuesta["mensaje"] = "Error al cancelar el movimiento, inténtelo de nuevo y si persiste el problema consulte al administrador del sistema.";
			$respuesta["resp"] = "f";
		    return $respuesta;
		}else{

			$this->db->select('dm.id_producto,dm.id_talla,dm.cantidad,m.id_tipo_movimiento');
			$this->db->from('movimientos m');
			$this->db->join('detalle_movimiento dm', 'dm.id_movimiento = m.id_movimiento');
			$this->db->where('m.id_movimiento', $id_movimiento);
			$this->db->order_by('dm.id_producto,dm.id_talla', 'ASC');

			//echo $this->db->get_compiled_select();die;
			$query_m = $this->db->get();

			$row_m = $query_m->result();
			
			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			    $respuesta["mensaje"] = "Error al consultar los detalles del movimiento, inténtelo de nuevo y si persiste el problema consulte al administrador del sistema.";
				$respuesta["resp"] = "f";
			    return $respuesta;
			}else{
				foreach ($row_m as $detalle_m) {
					$this->db->select('cantidad');
					$this->db->from('producto_talla');
					$this->db->where('id_producto', $detalle_m->id_producto);
					$this->db->where('id_talla', $detalle_m->id_talla);

					//echo $this->db->get_compiled_select();die;
					$query_p = $this->db->get();

					$row_p = $query_p->result();

					if ($this->db->trans_status() === FALSE){
					    $this->db->trans_rollback();
					    $respuesta["mensaje"] = "Error al consultar la cantidad del producto, inténtelo de nuevo y si persiste el problema consulte al administrador del sistema.";
						$respuesta["resp"] = "f";
					    return $respuesta;
					}else{
						if($detalle_m->id_tipo_movimiento == 1 || $detalle_m->id_tipo_movimiento == 8 || $detalle_m->id_tipo_movimiento == 9){
							$cantidad_new = $row_p[0]->cantidad - $detalle_m->cantidad;
						}else{
							$cantidad_new = $row_p[0]->cantidad + $detalle_m->cantidad;
						}

						$this->db->set('cantidad', $cantidad_new);
						$this->db->where('id_producto', $detalle_m->id_producto);
						$this->db->where('id_talla', $detalle_m->id_talla);
						$str = $this->db->update('producto_talla');

						if ($this->db->trans_status() === FALSE){
						    $this->db->trans_rollback();
						    $respuesta["mensaje"] = "Error al consultar la cantidad del producto, inténtelo de nuevo y si persiste el problema consulte al administrador del sistema.";
							$respuesta["resp"] = "f";
						    return $respuesta;
						}
					}
				}
			}
		}

		$this->acciones_m->set_user_action($_SESSION["id_usuario"], "Se canceló el movimiento con id: " . $id_movimiento . " en la fecha: " . $fecha_cancelacion);
		$this->db->trans_commit();
		return $respuesta;

	}

}