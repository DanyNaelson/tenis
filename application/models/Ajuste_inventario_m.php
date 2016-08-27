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

	function registrar_salida($salida, $salida_detalle){

		$fecha_salida = date("Y-m-d H:m:s");
		$insert = "";
		$mensaje = "";
		$str = 1;

		$this->db->trans_begin();

		$this->db->select('id_movimiento');
		$this->db->from('movimientos');
		$this->db->where('id_tipo_movimiento', 2);

		//echo $this->db->get_compiled_select();die;
		$query = $this->db->get();

		if ($this->db->trans_status() === FALSE){
		    $this->db->trans_rollback();
		}

		$row = count($query->result());

		if (is_int($row)) {

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

			$str = $this->db->insert('movimientos', $data);

			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			}

			$id_ultimo_e = $this->db->insert_id();

			if ($str == 1)
			{
				
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

						$str = $this->db->insert('producto_talla', $data_pt);

						if ($this->db->trans_status() === FALSE){
						    $this->db->trans_rollback();
						}
					}else{
						$cantidad = (int)$row[0]->cantidad - (int)$salida_detalle[$i]["cantidad"];

						if($cantidad < 0){
							$mensaje = "Error al actualizar la cantidad del producto ya que es menor que 0.";
							$mensaje .= "|f";

							if ($this->db->trans_status() === FALSE){
							    $this->db->trans_rollback();
							}
						}

						$this->db->set('cantidad', $cantidad);
						$this->db->where('id_producto', $salida_detalle[$i]["id_producto"]);
						$this->db->where('id_talla', $salida_detalle[$i]["id_talla"]);
						$str = $this->db->update('producto_talla');
					}

					if($str == 1){

						$data_det = array(
					        'id_movimiento' => $id_ultimo_e,
					        'id_producto' => $salida_detalle[$i]["id_producto"],
					        'id_talla' => $salida_detalle[$i]["id_talla"],
					        'cantidad' => $salida_detalle[$i]["cantidad"],
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
							$mensaje = "Se ingresaron los detalles de salida correctamente.";
							$mensaje .= "|t";
						} else {
							$mensaje = "Error al insertar el detalle_movimiento, inténtelo de nuevo y si persiste el problema consulte al administrador del sistema.";
							$mensaje .= "|f";
						}

						if ($this->db->trans_status() === FALSE){
						    $this->db->trans_rollback();
						}else{
							$this->acciones_m->set_user_action($_SESSION["id_usuario"], "Se registro la salida con id: " . $id_ultimo_e);
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
			$mensaje = "Error al consultar los movimientos de salida, intentelo nuevamente.";
			$mensaje .= "|f";
		}

		return $mensaje;

	}

}