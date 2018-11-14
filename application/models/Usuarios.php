<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuarios extends CI_Model{
    public $name = 'usuarios';
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

    public function selectBy($condiciones){
        if (is_array($condiciones)){
            $this->db->select('*')
                     ->from($this->name)
                     ->where($condiciones)
                     ->where('status = 1');
            $query = $this->db->get();
            return $query->result();
        }
        else{
            $this->error = 'condiciones debe ser un array';
            return false;
        }
    }
    public function obtus($usuario, $password) { 
        if ($usuario!='' && $password!='') {
            $this->db->select('u.*')
                     ->from($this->name.' as u')
                     ->where('usuario', $usuario)
                     ->where('password', $password)
                     ->where('status = 1');
           $r = $this->db->get();
        return $r->row();
        }
        return "";
    }

    public function delete($id){
        if ($id>0){
            $data = array_merge(array('status' => 0), $this->data);
            $this->db->where('id_usuario', $id);
            return $this->db->update($this->name, $data);
        }
        return false;
    }

    public function update($id, $data){
        if ($id>0 && is_array($data)){
            $data = array_merge($data, $this->data);
            $this->db->where('id_usuario', $id);
            return $this->db->update($this->name, $data);
        }
        return false;
    }
    public function add($data){
        if (is_array($data) && !empty($data)){
            $data = array_merge($data, $this->data);
            $this->db->insert($this->name, $data);
            return $this->db->insert_id();
        }
        return false;
    }
}