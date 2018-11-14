<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* controlador para usuarios
* @author Antonio Cruz
* @uses usuarios
* @package principal
* @subpackage controladores
* @copyright Copyright (c) 2018,Examen Ingenia
*/
class Usuario extends CI_Controller{
    /**
    * @var any almacena datos varios que se pasan a la vista (manejado por twig)
    */
    private $data;

    public function __construct(){
        parent::__construct();
        /**
        * agregamos valores a variable $data
        */
        $this->load->library('util');

        //validaciones de sesion
        $this->util->chk_sess();
        $this->util->chk_pass($this->router->fetch_module(), get_class($this), $this->session->userdata('id_perfil'));

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
        $this->data['titulo'] = 'Examen Ingenia - Usuarios';
        $this->data['descripcion'] = 'Examen Ingenia - Usuarios';
        /**
        * Carga modelos necesarios
        */
        $this->load->model('usuarios');
    }
    /**
    * Es la funcion inicial del controlador
    * @uses usuarios::selectAll
    * @see view/usuarios/index.html
    */
    public function index(){
        $urlImagen = FCPATH."/img/logo.png";
        $im = file_get_contents($urlImagen);
        $imdata = base64_encode($im);
        $this->data['logo64'] = $imdata;
        $this->data['usuarios'] = $this->usuarios->selectAll();
        $this->data['pagina'] = 'Usuarios';
        $this->twig->display('usuarios/index.html', $this->data);
    }
     /**
    * Es la funcion listar
    * @uses usuarios::selectAll
    * @see view/usuarios/listar.html
    */
    public function listar(){
        $urlImagen = FCPATH."/img/logo.png";
        $im = file_get_contents($urlImagen);
        $imdata = base64_encode($im);
        $this->data['logo64'] = $imdata;
        $this->data['usuarios'] = $this->usuarios->selectAll();
        $this->data['pagina'] = 'Usuarios';
        $this->twig->display('usuarios/listar.html', $this->data);
    }
    /**
    * guarda los datos del usuario
    * @global mixed POST password
    * @global mixed POST passwordconf
    * @global mixed POST id_usuario
    * @global mixed POST apellidos
    * @global mixed POST usuario
    * @global mixed POST status
    * @uses usuarios::update
    * @uses usuarios::add
    * @see view/usuarios/usuario.html
    */
    public function guardar(){

            $id = $this->input->post('id_usuario');
            $usuario = $this->input->post('usuario');
            $nombre = $this->input->post('nombre');
            $apellidos = $this->input->post('apellidos');
            $email = $this->input->post('email');
            $telof = $this->input->post('telefono');
            $telcel = $this->input->post('celular');
            $status = $this->input->post('status');
            $password = $this->input->post('password');

            $nombre_temporal = $_FILES['archivo']['tmp_name'];
            $nombre_archivo = $_FILES['archivo']['name'];
            move_uploaded_file($nombre_temporal, FCPATH."/img/avatars/".$nombre_archivo);

            if($password != '0'){
                $password = hash('sha512', $password);
            }else{
                $password=0;
            }

            if($id > 0)
            {
                if($password == '0')
                {
                    if ($nombre_archivo!='') {
                        $datos = array('id_usuario'=>$id,
                                       'usuario'=>$usuario,
                                        'nombre'=>$nombre,
                                        'apellidos'=>$apellidos,
                                        'puesto'=>$puesto,
                                        'email'=>$email,
                                        'telefono'=>$telefono,
                                        'celular'=>$celular,
                                        'imagen'=>$nombre_archivo,
                                        'status'=>$status);
                        $this->usuarios->update($id,$datos);
                        die('update');
                    }else{
                        $datos = array(('id_usuario'=>$id,
                                       'usuario'=>$usuario,
                                        'nombre'=>$nombre,
                                        'apellidos'=>$apellidos,
                                        'puesto'=>$puesto,
                                        'email'=>$email,
                                        'telefono'=>$telefono,
                                        'celular'=>$celular,
                                        'imagen'=>$nombre_archivo,
                                        'status'=>$status);
                        $this->usuarios->update($id,$datos);
                        die('update');
                    }
                }else{
                    if ($nombre_archivo!='') {
                        $datos = array(('id_usuario'=>$id,
                                       'usuario'=>$usuario,
                                        'nombre'=>$nombre,
                                        'apellidos'=>$apellidos,
                                        'puesto'=>$puesto,
                                        'email'=>$email,
                                        'telefono'=>$telefono,
                                        'celular'=>$celular,
                                        'imagen'=>$nombre_archivo,
                                        'status'=>$status,
                                        'password'=>$password);

                        $this->usuarios->update($id,$datos);
                        die('update');
                    }else{
                        $datos = array('id_usuario'=>$id,
                                       'usuario'=>$usuario,
                                        'nombre'=>$nombre,
                                        'apellidos'=>$apellidos,
                                        'puesto'=>$puesto,
                                        'email'=>$email,
                                        'telefono'=>$telefono,
                                        'celular'=>$celular,
                                        'imagen'=>$nombre_archivo,
                                        'status'=>$status,
                                        'password'=>$password);

                        $this->usuarios->update($id,$datos);
                        die('update');
                    }
                }
            }else{
                $datos = array('id_usuario'=>$id,
                               'usuario'=>$usuario,
                                'nombre'=>$nombre,
                                'apellidos'=>$apellidos,
                                'puesto'=>$puesto,
                                'email'=>$email,
                                'telefono'=>$telefono,
                                'celular'=>$celular,
                                'imagen'=>$nombre_archivo,
                                'status'=>$status,
                                'password'=>$password,
                                'fecha_registro' => date('Y-m-d')
                                );
                $this->usuarios->add($datos);
                die('ok');
            }

    }
    /**
     * elimina un usuario, pone en sesion mensajes de resultado, redirecciona usuarios/index
     * @param int $id el id del usuario
     *
     * @uses usuarios::delete
     */
    public function eliminar(){
        if ($this->input->is_ajax_request()) 
        {
            $id = $this->input->post('id_usuario');
            $id_usuario = $this->usuarios->delete($id);
            if ($id_usuario>0) {
                die('delete');
            }else{
                die('dbe');
            }
        }die('paramerror');
    }
}