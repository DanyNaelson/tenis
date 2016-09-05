<?php
class Productos_m extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	function obtener_tallas(){
		$sql = "SELECT *
				FROM talla
				ORDER BY talla";

		$query = $this->db->query($sql);
        $rows = $query->result();

		if (isset($rows))
		{
		    return $rows;
		}else{
      		return null;
      	}
	}

	function obtener_modelos($id_marca = null, $modelo = null){
		$sql = "SELECT count(id_producto) as num_p
				FROM productos ";

		if (!is_null($id_marca)){
			$sql .= "WHERE id_marca = " . $id_marca . " ";
			if (!is_null($modelo)) {
				$sql .= "AND modelo = '" . $modelo . "'";
			}
		}else{
			if (!is_null($modelo)) {
				$sql .= "WHERE modelo = '" . $modelo . "'";
			}
		}

		$query = $this->db->query($sql);
        $row = $query->row();

		if (isset($row))
		{
		    return $row->num_p;;
		}else{
      		return 0;
      	}
	}

	function obtener_productos($id_marca = null, $modelo = null, $limit = 10, $offset = 0){
		$sql = "SELECT p.id_producto, m.id_marca, m.marca, p.modelo, p.descripcion, p.precio
				FROM productos p 
				INNER JOIN marca m ON(m.id_marca = p.id_marca) ";
		
		if ($id_marca != null) {
			$sql .= "WHERE m.id_marca = " . $id_marca . " ";

			if ($modelo != null) {
				$sql .= "AND p.modelo = '" . $modelo . "' ";
			}
		}else{
			if ($modelo != null) {
				$sql .= "WHERE p.modelo = '" . $modelo . "' ";
			}
		}

		$sql .= "ORDER BY m.marca, p.modelo
				LIMIT " . $limit . " OFFSET " . $offset;

		$query = $this->db->query($sql);
        $rows = $query->result();

		if (isset($rows))
		{
		    return $rows;
		}else{
      		return null;
      	}
	}

	function obtener_producto_talla($codigo_barras = null, $id_producto = null){
		$sql = "SELECT *
				FROM producto_talla pt
				INNER JOIN productos p ON (p.id_producto = pt.id_producto)";

		if($id_producto != null){
			$sql .= "WHERE pt.id_producto IN (" . $id_producto . ") ";
			if ($codigo_barras != null) {
				$sql .= "AND pt.codigo_barras = '" . $codigo_barras . "' ";
			}
		}else{
			if ($codigo_barras != null) {
				$sql .= "WHERE pt.codigo_barras = '" . $codigo_barras . "' ";
			}
		}

		$sql .= "ORDER BY p.id_producto, id_talla";

		$query = $this->db->query($sql);
        $rows = $query->result();

		if (isset($rows))
		{
		    return $rows;
		}else{
      		return null;
      	}
	}

	function obtener_marcas($id_marca = null){
		$sql = "SELECT *
				FROM marca ";

		if ($id_marca != null) {
			$sql .= "WHERE id_marca = " . $id_marca . " ";
		}

		$sql .= "ORDER BY id_marca";

		$query = $this->db->query($sql);
        $rows = $query->result();

		if (isset($rows))
		{
		    return $rows;
		}else{
      		return null;
      	}
	}

	function actualizar_producto($datos_producto){
		$d_producto = explode("|", $datos_producto);
		$id_producto = str_replace("producto_", "", trim($d_producto[0]));
		$posicion = strpos($d_producto[1], "select");
		$update = "";
		$mensaje = "Se actualizaron los datos correctamente.";
		$mensaje .= "|t";

		$this->db->trans_begin();
		
		if($posicion === false){
			$data = array(
		        'marca' => $d_producto[1]
			);

			$str = $this->db->insert('marca', $data);
			$id_ultimo_m = $this->db->insert_id();

			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			}
		}else{
			$producto_marca = str_replace("select", "", $d_producto[1]);
			$id_ultimo_m = $producto_marca;
		}

		$this->db->set('id_marca', $id_ultimo_m);
		$this->db->set('modelo', trim($d_producto[2]));
		$this->db->set('descripcion', $d_producto[3]);
		$this->db->set('precio', trim($d_producto[count($d_producto) - 1]));
		$this->db->where('id_producto', $id_producto);
		$this->db->update('productos');

		if ($this->db->trans_status() === FALSE){
			$mensaje = "Hubo un problema al actualizar los datos.";
			$mensaje .= "|f";
		    $this->db->trans_rollback();
		    return $mensaje;
		}
			
		for($i = 4; $i < count($d_producto) - 1 ; $i++){
			if(trim($d_producto[$i]) != ""){

				$this->db->select('id_producto_talla');
				$this->db->from('producto_talla');
				$this->db->where('id_producto', $id_producto);
				$this->db->where('id_talla', $i - 3);
				$query = $this->db->get();
				$rows = $query->result();

				if(!empty($rows)){

					$this->db->set('codigo_barras', trim($d_producto[$i]));
					$this->db->where('id_producto_talla', $rows[0]->id_producto_talla);
					$this->db->update('producto_talla');

				}else{

					$data = array(
					        'id_producto' => $id_producto,
					        'id_talla' => $i - 3,
					        'codigo_barras' => trim($d_producto[$i]),
					        'id_almacen' => NULL,
					        'cantidad' => 0
						);var_dump($data);die;
					
					$this->db->insert('producto_talla', $data);

				}

				if ($this->db->trans_status() === FALSE){
				    $mensaje = "Hubo un problema al actualizar los datos.";
					$mensaje .= "|f";
				    $this->db->trans_rollback();
				    return $mensaje;
				}
			}
		}

		$this->acciones_m->set_user_action($_SESSION["id_usuario"], "Se actualizo el modelo: " . trim($d_producto[2]));
		$this->db->trans_commit();

		return $mensaje;
	}

	function borrar_producto($datos_producto){
		$id_producto = $datos_producto;
		$mensaje = "Se borró el producto correctamente.";

		$this->db->trans_begin();

		$this->db->where('id_producto', $id_producto);
		$str = $this->db->delete('productos');

		if ($this->db->trans_status() === FALSE){
			$mensaje = "Error al borrar los datos de producto.";
		    $this->db->trans_rollback();
		    return $mensaje;
		}
			
		$this->db->where('id_producto', $id_producto);
		$str = $this->db->delete('producto_talla');
		
		if ($this->db->trans_status() === FALSE){
			$mensaje = "Error al borrar productos-talla.";
		    $this->db->trans_rollback();
		    return $mensaje;
		}

		$this->acciones_m->set_user_action($_SESSION["id_usuario"], "Se eliminó el id_producto: " . $id_producto);
		$this->db->trans_commit();

		return $mensaje;
	}

	function insertar_producto($datos_producto){
		$d_producto = explode("|", $datos_producto);
		$posicion = strpos($d_producto[0], "select");
		$mensaje = "Necesita ingresar por lo menos un código de barras.";

		$this->db->trans_begin();

		if($posicion === false){
			$data = array(
		        'marca' => trim($d_producto[0])
			);

			$this->db->insert('marca', $data);

			if ($this->db->trans_status() === FALSE){
				$mensaje = "Error al insertar marca.";
				$mensaje .= "|f";
			    $this->db->trans_rollback();
			    return $mensaje;
			}

			$id_ultimo_m = $this->db->insert_id();
		}
		else
		{
			$producto_marca = str_replace("select", "", $d_producto[0]);
			$id_ultimo_m = $producto_marca;
		}

		$data = array(
	        'modelo' => trim($d_producto[1]),
	        'descripcion' => trim($d_producto[2]),
	        'precio' => trim($d_producto[count($d_producto) - 1]),
	        'id_marca' => trim($id_ultimo_m)
		);

		$this->db->insert('productos', $data);

		if ($this->db->trans_status() === FALSE){
		    $mensaje = "Error al insertar el producto.";
			$mensaje .= "|f";
		    $this->db->trans_rollback();
		    return $mensaje;
		}

		$id_ultimo_p = $this->db->insert_id();

		for($i = 3; $i < count($d_producto) - 1 ; $i++){
			if(trim($d_producto[$i]) != "")
			{
				$data = array(
			        'id_producto' => $id_ultimo_p,
			        'id_talla' => $i - 2,
			        'codigo_barras' => trim($d_producto[$i]),
			        'id_almacen' => NULL,
			        'cantidad' => 0
				);

				$this->db->insert('producto_talla', $data);

				if ($this->db->trans_status() === FALSE){
					$mensaje = "Error al insertar codigos de barra.";
					$mensaje .= "|f";
				    $this->db->trans_rollback();
				    return $mensaje;
				}
			}
		}

		$mensaje = "Se agregó correctamente el producto.";
		$mensaje .= "|t";
		$this->acciones_m->set_user_action($_SESSION["id_usuario"], "Se ingreso el producto con modelo: " . trim($d_producto[1]) . ", id_talla: " . ($i - 2) . " y con codigo de barras: " . trim($d_producto[$i]));
		$this->db->trans_commit();

		return $mensaje;
	}

	function obtener_codigo($codigo){
		$sql = "SELECT m.marca, p.modelo, p.descripcion, pt.codigo_barras
				FROM marca m
				INNER JOIN productos p ON (p.id_marca = m.id_marca)
				INNER JOIN producto_talla pt ON (pt.id_producto = p.id_producto)
				WHERE codigo_barras = '" . trim($codigo) . "'";

		$query = $this->db->query($sql);
        $rows = $query->result();

		if (!empty($rows))
		{
		    return $rows;
		}else
		{
      		return null;
      	}
	}

	function validar_marca($marca){
		$sql = "SELECT id_marca, marca
				FROM marca
				WHERE UPPER(marca) = UPPER('" . trim($marca) . "')";

		$query = $this->db->query($sql);
        $rows = $query->result();

		if (!empty($rows))
		{
		    return $rows;
		}else
		{
      		return null;
      	}
	}

	function validar_modelo($modelo){
		$sql = "SELECT p.id_producto, m.marca, p.modelo, p.descripcion
				FROM marca m
				INNER JOIN productos p ON (p.id_marca = m.id_marca)
				WHERE UPPER(p.modelo) = UPPER('" . trim($modelo) . "')";

		$query = $this->db->query($sql);
        $rows = $query->result();

		if (!empty($rows))
		{
		    return $rows;
		}else
		{
      		return null;
      	}
	}

}