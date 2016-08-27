<?php
class Inventario_fisico_m extends CI_Model{

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

	function finalizar_fisico($fisico){

		$fecha_fisico = date("Y-m-d H:m:s");
		$respuesta = array("mensaje" => "Se finalizó correctamente el inventario físico, revisa el módulo de ajuste de inventario para realizar las operaciones pertinentes.", "resp" => "t");

		$this->db->trans_begin();

		$this->db->select('id_almacen');
		$this->db->from('ajuste_fisico');
		$this->db->where('id_almacen', $fisico[0]["id_almacen"]);

		//echo $this->db->get_compiled_select();die;
		$query = $this->db->get();

		$row = $query->result();

		if ($this->db->trans_status() === FALSE){
		    $respuesta = array('mensaje' => 'No se pudieron consultar los datos de ajuste previo, inténtelo de nuevo y en caso de persistir el problema comuniquese con el administrador del sistema.', 'resp' => 'f');
		    $this->db->trans_rollback();
		    return $mensaje;
		}

		if(!empty($row)){
			$this->db->where('id_almacen', $fisico[0]["id_almacen"]);
			$this->db->delete('ajuste_fisico');

			if ($this->db->trans_status() === FALSE){
			    $respuesta = array('mensaje' => 'No se pudieron borrar los datos de ajuste previo, inténtelo de nuevo y en caso de persistir el problema comuniquese con el administrador del sistema.', 'resp' => 'f');
			    $this->db->trans_rollback();
			    return $mensaje;
			}
		}

		foreach($fisico as $fis){

			if($fis["diferencia"] != 0){
				$data = array(
			        'id_producto' => $fis["id_producto"],
			        'id_talla' => $fis["id_talla"],
			        'id_almacen' => $fis["id_almacen"],
			        'cantidad_sistema' => $fis["cantidad_sistema"],
			        'cantidad_fisica' => $fis["cantidad"],
			        'diferencia' => $fis["diferencia"]
				);

				$this->db->insert('ajuste_fisico', $data);

				if ($this->db->trans_status() === FALSE){
				    $respuesta = array('mensaje' => 'No se pudo ingresar el id_producto: ' . $fis->id_producto . ', id_talla: ' . $fis->id_talla . ', inténtelo de nuevo y en caso de persistir el problema comuniquese con el administrador del sistema.', 'resp' => 'f');
				    $this->db->trans_rollback();
				    return $mensaje;
				}
			}
		}

		$this->acciones_m->set_user_action($_SESSION["id_usuario"], "Se realizó conteo de inventario físico en esta fecha: " . $fecha_fisico);
		$this->db->trans_commit();
		return $respuesta;

	}

}