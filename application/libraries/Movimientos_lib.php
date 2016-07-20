<?php 
if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Movimientos_lib {

	protected $id_producto;
	protected $marca;
	protected $modelo;
	protected $descripcion;
	protected $talla;
	protected $cantidad;
	protected $precio;
	protected $almacen;
	protected $confirmacion;
	protected $movimientos = array();

	public function set_properties($id_producto, $marca, $modelo, $descripcion, $talla, $cantidad = 1, $precio = 0, $confirmacion = 1){
		$this->id_producto = $id_producto;
		$this->marca = $marca;
		$this->modelo = $modelo;
		$this->descripcion = $descripcion;
		$this->talla = $talla;
		$this->cantidad = $cantidad;
		$this->precio = $precio;
		$this->confirmacion = $confirmacion;
	}

	public function get_idProducto(){
		$id_producto = $this->id_producto;
		return $id_producto;
	}

	public function set_idProducto($id_producto){
		$this->id_producto = $id_producto;
	}

	public function get_marca(){
		$marca = $this->marca;
		return $marca;
	}

	public function set_marca($marca){
		$this->marca = $marca;
	}

	public function get_modelo(){
		$modelo = $this->modelo;
		return $modelo;
	}

	public function set_modelo($modelo){
		$this->modelo = $modelo;
	}

	public function get_descripcion(){
		$descripcion = $this->descripcion;
		return $descripcion;
	}

	public function set_descripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	public function get_talla(){
		$talla = $this->talla;
		return $talla;
	}

	public function set_talla($talla){
		$this->talla = $talla;
	}

	public function get_cantidad(){
		$cantidad = $this->cantidad;
		return $cantidad;
	}

	public function set_cantidad($cantidad){
		$this->cantidad = $cantidad;
	}

	public function get_precio(){
		$precio = $this->precio;
		return $precio;
	}

	public function set_precio($precio){
		$this->precio = $precio;
	}

	public function get_almacen(){
		$almacen = $this->almacen;
		return $almacen;
	}

	public function set_almacen($almacen){
		$this->almacen = $almacen;
	}

	public function get_confirmacion(){
		$confirmacion = $this->confirmacion;
		return $confirmacion;
	}

	public function set_confirmacion($confirmacion){
		$this->confirmacion = $confirmacion;
	}

	public function get_movimientos(){
		$movimientos = $this->movimientos;
		return $movimientos;
	}

	public function set_movimientos(){

		$movimiento_tmp = array(
						'id_producto' => $this->id_producto,
						'marca' => $this->marca,
						'modelo' => $this->modelo,
						'descripcion' => $this->descripcion,
						'talla' => $this->talla,
						'cantidad' => $this->cantidad,
						'precio' => $this->precio,
						'confirmacion' => $this->confirmacion
					);

		array_push($this->movimientos, $movimiento_tmp);
	}

	public function find_modelo($modelo){

		foreach($this->movimientos as $movimiento) {
	        $key = array_search($modelo, $movimiento);
	        if($key != false){
	        	return $key;
	        }
	    }
		
	    return 'false';
	}

}