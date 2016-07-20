<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Barcode extends CI_Controller {

    public function index($codigo)
    {
        $codigo = trim($codigo);
        $this->set_barcode($codigo);
    }

    private function set_barcode($code)
    {
        //load library
        $this->load->library('zend');
        //load in folder Zend
        $this->zend->load('Zend/Barcode');
        //generate barcode
        Zend_Barcode::factory('code128', 'image', array('text'=>$code), array())->render();
    }

}