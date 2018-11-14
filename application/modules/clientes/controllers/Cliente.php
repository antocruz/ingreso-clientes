<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cliente extends CI_Controller{
    private $data;

    public function __construct(){
        parent::__construct();

        $this->load->library('util');

        //validaciones de sesion
        $this->util->chk_sess();
       
        $this->data['modulo'] = $this->router->fetch_module();
        $this->data['controlador'] = get_class();
        $this->data['metodo'] = $this->router->fetch_method();
        $this->data['ctoken'] = $this->security->get_csrf_hash();
        $this->data['ctokename'] = $this->security->get_csrf_token_name();
        $this->data['cookiename'] = $this->config->item('csrf_cookie_name');
        $this->data['csrf_cname'] = $this->config->item("csrf_cookie_name");
        $this->data['mensaje'] = $this->session->flashdata('mensaje');
        $this->data['mensaje2'] = $this->session->flashdata('mensaje2');
        $this->data['usuario'] = $this->session->all_userdata(); 
        $this->data['titulo'] = 'Examen Ingenia - Clientes';
        $this->data['descripcion'] = 'Examen Ingenia - Clientes';
        //modulos
        $this->load->model('clientes');
    }

    public function index(){
        $urlImagen = FCPATH."/img/logo_ingenia.png";
        $im = file_get_contents($urlImagen);
        $imdata = base64_encode($im);
        $this->data['logo64'] = $imdata;
        $this->data['clientes'] = $this->clientes->select();
        $this->data['pagina'] = 'Clientes';
        $this->twig->display('clientes/index.html', $this->data);
    }

    public function listar(){
        $urlImagen = FCPATH."/img/logo_ingenia.png";
        $im = file_get_contents($urlImagen);
        $imdata = base64_encode($im);
        $this->data['logo64'] = $imdata;
        $this->data['clientes'] = $this->clientes->select();
        $this->data['pagina'] = 'Clientes';
        $this->twig->display('clientes/listar.html', $this->data);
    }


    public function guardar(){
        if ($this->input->is_ajax_request()) 
        {
            $id_cliente = $this->input->post('id_cliente');
            $nombres = $this->input->post('nombres');
            $apellidos = $this->input->post('apellidos');
            $telefono = $this->input->post('telefono');
            $email = $this->input->post('email');
            $tarjeta = $this->input->post('tarjeta');
            $numTarjeta = $this->input->post('numTarjeta');
            $status = $this->input->post('status');
            $numTarjeta = "XXXX-XXXX-XXXX-".$numTarjeta;

            if($id_cliente != 0)
            {
                
                $datos = array('nombres'=>$nombres,
                                'apellidos'=>$apellidos,
                                'telefono'=>$telefono,
                                'email'=>$email,
                                'tarjeta'=>$tarjeta,
                                'num_tarjeta'=>$numTarjeta,
                                'status'=>$status,
                                'id_usuario'=>$this->data['usuario']['id_usuario']);
                $this->clientes->update($id_cliente,$datos);
                die('update');
            }else{
                $datos = array('nombres'=>$nombres,
                                'apellidos'=>$apellidos,
                                'telefono'=>$telefono,
                                'email'=>$email,
                                'tarjeta'=>$tarjeta,
                                'num_tarjeta'=>$numTarjeta,
                                'status'=>$status,
                                'fecha_registro'=>date("Y-m-d"),
                                'id_usuario'=>$this->data['usuario']['id_usuario']);
                $idCliente = (int) $this->clientes->add($datos);
                if ($idCliente>0) {
                    die('ok');
                }else {
                    die('nok_db');
                }
            }
        }die('paramerror');
    }

    public function eliminar(){
        if ($this->input->is_ajax_request()) 
        {
            $id = $this->input->post('id_cliente');
            $id_cliente = $this->clientes->delete($id);
            if ($id_cliente>0) {
                die('delete');
            }else{
                die('dbe');
            }
        }die('paramerror');
    }
}