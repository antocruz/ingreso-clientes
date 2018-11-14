<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Clientes extends CI_Model{
    public $name = 'clientes';
    public $error;
    private $data;

    public function __construct(){
        parent::__construct();
        $this->data = array('fecha_cambio' => date('Y-m-d'));
    }

    public function select(){
        $this->db->select('*')
                 ->from($this->name)
                 ->where('status = 1');
        $query = $this->db->get();
        return $query->result();
    }

    public function add($data){
        if (is_array($data) && !empty($data)){
            $data = array_merge($data, $this->data);
            $this->db->insert($this->name,$data);
            $lastid = $this->db->insert_id();
            return $lastid;
        }
        return false;
    }

    public function update($id, $data){
        if ($id>0 && is_array($data)){
            $data = array_merge($data, $this->data);
            $this->db->where('id_cliente', $id);
            return $this->db->update($this->name, $data);
        }
        return false;
    }

    public function delete($id){
        if ($id>0){
            $data = array_merge(array('status' => 0), $this->data);
            $this->db->where('id_cliente', $id);
            return $this->db->update($this->name, $data);
        }
        return false;
    }
}