<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller{
    private $data;

    public function __construct(){
        parent::__construct();

        $this->load->library('util');
        //validaciones de sesion
        $this->util->chk_sess();

        $this->data['titulo'] = 'Panel Principal';
        $this->data['descripcion'] = 'Examen Ingenia - Panel Principal';
        $this->data['pagina'] = 'Panel Principal';
        $this->data['ctoken'] = $this->security->get_csrf_hash();
        $this->data['ctokename'] = $this->security->get_csrf_token_name();
        $this->data['cookiename'] = $this->config->item('csrf_cookie_name');
        $this->data['mensaje'] = $this->session->flashdata('mensaje');
        $this->data['mensaje2'] = $this->session->flashdata('mensaje2');
        $this->data['modulo'] = $this->router->fetch_module();
        $this->data['controlador'] = get_class();
        $this->data['usuario'] = $this->session->all_userdata(); 
        
    }
    
    public function index(){
        $this->twig->display('main/index.html', $this->data);
    }
}