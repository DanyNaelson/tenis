<?php
class Salidas_m extends CI_Model{

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

		if (!is_null($modelo) && ($modelo != "") && ($modelo != '0')){
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

		$this->db->select('pt.id_talla,mv.id_tipo_movimiento,dm.cantidad,mv.confirmacion');
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

	function registrar_salida($salida, $salida_detalle){

		$fecha_salida = date("Y-m-d H:m:s");

		$this->db->trans_begin();

		$this->db->select('id_movimiento');
		$this->db->from('movimientos');
		$this->db->where('id_tipo_movimiento', 2);

		//echo $this->db->get_compiled_select();die;
		$query = $this->db->get();

		if ($this->db->trans_status() === FALSE){
			$mensaje = "Error al consultar los movimientos de salida, intentelo nuevamente.";
			$mensaje .= "|f";
		    $this->db->trans_rollback();
		    return $mensaje;
		}

		$row = count($query->result());

		$folio = 'S' . ($row + 1);

		$data = array(
			'folio' => $folio,
	        'id_tipo_movimiento' => 2,
	        'cantidad' => $salida["cantidad"],
	        'precio' => 0,
	        'fecha' => $fecha_salida,
	        'id_almacen' => $salida["id_almacen"],
	        'confirmacion' => 1
		);

		$this->db->insert('movimientos', $data);

		if ($this->db->trans_status() === FALSE){
			$mensaje = "Error al insertar el movimiento, inténtelo de nuevo y si persiste el problema consulte al administrador del sistema.";
			$mensaje .= "|f";
		    $this->db->trans_rollback();
		    return $mensaje;
		}

		$id_ultimo_e = $this->db->insert_id();

		for($i = 0; $i < count($salida_detalle) ; $i++){

			$this->db->select('cantidad');
			$this->db->from('producto_talla');
			$this->db->where('id_producto', $salida_detalle[$i]["id_producto"]);
			$this->db->where('id_talla', $salida_detalle[$i]["id_talla"]);

			//echo $this->db->get_compiled_select();die;
			$query = $this->db->get();

			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			}

			$row = $query->result();

			if (empty($row)) {
				$data_pt = array(
			        'id_producto' => $salida_detalle[$i]["id_producto"],
			        'id_talla' => $salida_detalle[$i]["id_talla"],
			        'codigo_barras' => '',
			        'id_almacen' => NULL,
			        'cantidad' => $salida_detalle[$i]["cantidad"]
				);

				$this->db->insert('producto_talla', $data_pt);

			}else{
				$cantidad = (int)$row[0]->cantidad - (int)$salida_detalle[$i]["cantidad"];

				$this->db->set('cantidad', $cantidad);
				$this->db->where('id_producto', $salida_detalle[$i]["id_producto"]);
				$this->db->where('id_talla', $salida_detalle[$i]["id_talla"]);
				$this->db->update('producto_talla');
			}

			if ($this->db->trans_status() === FALSE){
				$mensaje = "Error al actualizar la cantidad del producto, inténtelo de nuevo y si persiste el problema consulte al administrador del sistema.";
				$mensaje .= "|f";
			    $this->db->trans_rollback();
			    return $mensaje;
			}

			$data_det = array(
		        'id_movimiento' => $id_ultimo_e,
		        'id_producto' => $salida_detalle[$i]["id_producto"],
		        'id_talla' => $salida_detalle[$i]["id_talla"],
		        'cantidad' => $salida_detalle[$i]["cantidad"],
		        'precio' => 0
			);

			$this->db->insert('detalle_movimiento', $data_det);

			if ($this->db->trans_status() === FALSE){
				$mensaje = "Error al insertar el detalle_movimiento, inténtelo de nuevo y si persiste el problema consulte al administrador del sistema.";
				$mensaje .= "|f";
			    $this->db->trans_rollback();
			    return $mensaje;
			}
		}

		$mensaje = "Se ingresaron los detalles de salida correctamente.";
		$mensaje .= "|t";
		$this->acciones_m->set_user_action($_SESSION["id_usuario"], "Se registro la salida con id: " . $id_ultimo_e);
		$this->db->trans_commit();

		return $mensaje;

	}

}