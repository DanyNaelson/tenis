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

	function obtener_productos(){
		$sql = "SELECT p.id_producto, m.marca, p.modelo, p.descripcion, p.precio
				FROM productos p 
				INNER JOIN marca m ON(m.id_marca = p.id_marca)";

		$query = $this->db->query($sql);
        $rows = $query->result();

		if (isset($rows))
		{
		    return $rows;
		}else{
      		return null;
      	}
	}

	function obtener_producto_talla(){
		$sql = "SELECT *
				FROM producto_talla pt
				ORDER BY id_producto, id_talla";

		$query = $this->db->query($sql);
        $rows = $query->result();

		if (isset($rows))
		{
		    return $rows;
		}else{
      		return null;
      	}
	}

	function obtener_marcas(){
		$sql = "SELECT *
				FROM marca";

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
		$d_producto = explode("-", $datos_producto);
		$id_producto = str_replace("producto_", "", trim($d_producto[0]));
		$posicion = strpos($d_producto[1], "select");
		$insert = "";
		$mensaje = "No se ingresaron datos para actualizar el producto.";

		if($posicion === false){
			$data = array(
		        'marca' => $d_producto[1]
			);

			$str = $this->db->insert('marca', $data);
			$id_ultimo_m = $this->db->insert_id();
		}
		else
		{
			$producto_marca = str_replace("select", "", $d_producto[1]);
			$id_ultimo_m = $producto_marca;
			$str = 1;
		}

		$this->db->set('id_marca', $id_ultimo_m);
		$this->db->set('modelo', trim($d_producto[2]));
		$this->db->set('descripcion', $d_producto[3]);
		$this->db->set('precio', trim($d_producto[count($d_producto) - 1]));
		$this->db->where('id_producto', $id_producto);
		$str = $this->db->update('productos');

		if ($str == 1)
		{
			$this->db->where('id_producto', $id_producto);
			$str = $this->db->delete('producto_talla');

			if ($str == 1)
			{
				for($i = 4; $i < count($d_producto) - 1 ; $i++){
					if(trim($d_producto[$i]) != "")
					{
						$data = array(
					        'id_producto' => trim($id_producto),
					        'id_talla' => $i - 3,
					        'codigo_barras' => trim($d_producto[$i]),
					        'id_almacen' => NULL,
					        'cantidad' => NULL
						);

						$str = $this->db->insert('producto_talla', $data);

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
							$mensaje = "Se ingresaron los codigos de barra correctamente.";
						} else {
							$mensaje = "Error al insertar codigos de barra.";
						}
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
			$mensaje = "Error al actualizar los datos del producto.";
		}

		return $mensaje;
	}

	function borrar_producto($datos_producto){
		$id_producto = $datos_producto;

		$this->db->where('id_producto', $id_producto);
		$str = $this->db->delete('productos');

		if ($str == 1)
		{
			$this->db->where('id_producto', $id_producto);
			$str = $this->db->delete('producto_talla');

			if ($str == 1)
			{
				$mensaje = "Se borró el producto correctamente.";
			}
			else
			{
				$mensaje = "Error al borrar productos-talla.";
			}
		}
		else
		{
			$mensaje = "Error al borrar los datos de producto.";
		}

		return $mensaje;
	}

	function insertar_producto($datos_producto){
		$d_producto = explode("-", $datos_producto);
		$posicion = strpos($d_producto[0], "select");
		$insert = "";

		if($posicion === false){
			$data = array(
		        'marca' => trim($d_producto[0])
			);

			$str = $this->db->insert('marca', $data);
			$id_ultimo_m = $this->db->insert_id();
		}
		else
		{
			$producto_marca = str_replace("select", "", $d_producto[0]);
			$id_ultimo_m = $producto_marca;
			$str = 1;
		}

		if ($str == 1)
		{
			$data = array(
		        'modelo' => trim($d_producto[1]),
		        'descripcion' => trim($d_producto[2]),
		        'precio' => trim($d_producto[count($d_producto) - 1]),
		        'id_marca' => trim($id_ultimo_m)
			);

			$str = $this->db->insert('productos', $data);
			$id_ultimo_p = $this->db->insert_id();

			if ($str == 1)
			{
				for($i = 3; $i < count($d_producto) - 1 ; $i++){
					if(trim($d_producto[$i]) != "")
					{
						$data = array(
					        'id_producto' => $id_ultimo_p,
					        'id_talla' => $i - 2,
					        'codigo_barras' => trim($d_producto[$i]),
					        'id_almacen' => NULL,
					        'cantidad' => NULL
						);

						$str = $this->db->insert('producto_talla', $data);

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
							$mensaje = "Se ingresaron los codigos de barra correctamente.";
						} else {
							$mensaje = "Error al insertar codigos de barra.";
						}
					}
				}
			}
			else
			{
				$mensaje = "Error al insertar el producto.";
			}
		}
		else
		{
			$mensaje = "Error al insertar la marca del producto.";
		}

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

}