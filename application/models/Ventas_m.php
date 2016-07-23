<?php
class Ventas_m extends CI_Model{

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

		$this->db->select('pt.id_producto_talla,p.id_producto,m.marca,p.modelo,p.descripcion,t.id_talla,t.talla,mv.id_tipo_movimiento,dm.cantidad');
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

	function registrar_venta($venta, $venta_detalle){

		$fecha_venta = date("Y-m-d H:m:s");
		$insert = "";
		$mensaje = "";
		$str = 1;

		$this->db->trans_begin();

		$this->db->select('id_movimiento');
		$this->db->from('movimientos');
		$this->db->where('id_tipo_movimiento', 4);

		//echo $this->db->get_compiled_select();die;
		$query = $this->db->get();

		if ($this->db->trans_status() === FALSE){
		    $this->db->trans_rollback();
		}

		$row = count($query->result());

		if (is_int($row)) {

			$folio = 'V' . ($row + 1);

			$data = array(
				'folio' => $folio,
		        'id_tipo_movimiento' => 4,
		        'cantidad' => $venta["cantidad"],
		        'fecha' => $fecha_venta,
		        'id_almacen' => $venta["id_almacen"],
		        'precio' => $venta["precio"],
		        'confirmacion' => 0
			);

			$str = $this->db->insert('movimientos', $data);

			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			}

			$id_ultimo_e = $this->db->insert_id();

			if ($str == 1)
			{
				
				for($i = 0; $i < count($venta_detalle) ; $i++){

					$this->db->select('cantidad');
					$this->db->from('producto_talla');
					$this->db->where('id_producto', $venta_detalle[$i]["id_producto"]);
					$this->db->where('id_talla', $venta_detalle[$i]["id_talla"]);

					//echo $this->db->get_compiled_select();die;
					$query = $this->db->get();

					if ($this->db->trans_status() === FALSE){
					    $this->db->trans_rollback();
					}

					$row = $query->result();

					if (empty($row)) {
						$data_pt = array(
					        'id_producto' => $venta_detalle[$i]["id_producto"],
					        'id_talla' => $venta_detalle[$i]["id_talla"],
					        'codigo_barras' => '',
					        'id_almacen' => NULL,
					        'cantidad' => $venta_detalle[$i]["cantidad"]
						);

						$str = $this->db->insert('producto_talla', $data_pt);

						if ($this->db->trans_status() === FALSE){
						    $this->db->trans_rollback();
						}
					}else{
						$cantidad = (int)$row[0]->cantidad - (int)$venta_detalle[$i]["cantidad"];

						if($cantidad < 0){
							$mensaje = "Error al actualizar la cantidad del producto ya que es menor que 0.";
							$mensaje .= "|f";

							if ($this->db->trans_status() === FALSE){
							    $this->db->trans_rollback();
							}
						}

						$this->db->set('cantidad', $cantidad);
						$this->db->where('id_producto', $venta_detalle[$i]["id_producto"]);
						$this->db->where('id_talla', $venta_detalle[$i]["id_talla"]);
						$str = $this->db->update('producto_talla');
					}

					if($str == 1){

						$data_det = array(
					        'id_movimiento' => $id_ultimo_e,
					        'id_producto' => $venta_detalle[$i]["id_producto"],
					        'id_talla' => $venta_detalle[$i]["id_talla"],
					        'cantidad' => $venta_detalle[$i]["cantidad"],
					        'precio' => $venta_detalle[$i]["precio"]
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
							$mensaje = "Se ingresaron los detalles de venta correctamente.";
							$mensaje .= "|t";
						} else {
							$mensaje = "Error al insertar el detalle_movimiento, inténtelo de nuevo y si persiste el problema consulte al administrador del sistema.";
							$mensaje .= "|f";
						}

						if ($this->db->trans_status() === FALSE){
						    $this->db->trans_rollback();
						}else{
							$this->acciones_m->set_user_action($_SESSION["id_usuario"], "Se registro la venta con id: " . $id_ultimo_e);
						    $this->db->trans_commit();
						}

					}else{
						$mensaje = "Error al actualizar la cantidad del producto, inténtelo de nuevo y si persiste el problema consulte al administrador del sistema.";
						$mensaje .= "|f";
					}
				}
			}
			else
			{
				$mensaje = "Error al insertar el movimiento, inténtelo de nuevo y si persiste el problema consulte al administrador del sistema.";
				$mensaje .= "|f";
			}

		}else{
			$mensaje = "Error al consultar los movimientos de venta, intentelo nuevamente.";
			$mensaje .= "|f";
		}

		return $mensaje;

	}

	function obtener_ventas($id_almacen){

		$this->db->select('mv.id_movimiento,m.marca,p.modelo,p.descripcion,t.talla,dm.cantidad,dm.precio');
		$this->db->from('producto_talla pt');
		$this->db->join('productos p', 'p.id_producto = pt.id_producto');
		$this->db->join('marca m', 'm.id_marca = p.id_marca');
		$this->db->join('talla t', 't.id_talla = pt.id_talla');
		$this->db->join('detalle_movimiento dm', 'dm.id_producto = pt.id_producto AND dm.id_talla = pt.id_talla');
		$this->db->join('movimientos mv', 'mv.id_movimiento = dm.id_movimiento AND mv.id_almacen = ' . $id_almacen . " AND mv.confirmacion = 0 AND mv.id_tipo_movimiento = 4");
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

	function confirmar_ventas($id_movimientos){
		$movimientos = explode(",", $id_movimientos);
		$insert = "";
		$mensaje = "";
		$str = 1;
		$respuesta = array('mensaje' => 'Se han confirmado correctamente los movimientos de venta.', 'resp' => 't');

		$this->db->trans_begin();

		foreach($movimientos as $movimiento){
			$this->db->set('confirmacion', 1);
			$this->db->where('id_movimiento', $movimiento);
			$this->db->update('movimientos');

			if($this->db->trans_status() === FALSE){
				$respuesta = array('mensaje' => 'No se actualizó el movimiento con id: ' . $movimiento . '.', 'resp' => 'f');
			    $this->db->trans_rollback();
			    return $respuesta;
			}else{
				$this->acciones_m->set_user_action($_SESSION["id_usuario"], "Se confirmo el cierre de venta con los id_movimientos: " . $movimientos);
				$this->db->trans_commit();
			}
		}

		return $respuesta;
	}
}