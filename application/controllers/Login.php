<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * controlador de Login
 * @author Antonio Cruz
 * @uses users
 * @package clientes
 * @copyright Copyright (c) 2018, Examen Ingenia
*/
class Login extends CI_Controller {
   /**
   * Inicializo variable data
   **/
   private $data;
   /**
   * Constructor de la clase
   **/
   public function __construct() {
      parent::__construct();

      $this->load->library('util'); 

      $this->data['titulo'] = 'Login';
      $this->data['ctoken'] = $this->security->get_csrf_hash();
      $this->data['ctokename'] = $this->security->get_csrf_token_name();
      $this->data['cookiename'] = $this->config->item('csrf_cookie_name');
      $this->data['csrf_cname'] = $this->config->item("csrf_cookie_name");
      $this->data['mensaje'] = $this->session->flashdata('mensaje');
      $this->data['mensaje2'] = $this->session->flashdata('mensaje2');
      $this->data['modulo'] = $this->router->fetch_module();

      $this->load->model('usuarios');
   }


   /**
   * Es la funcion inicial del controlador
   * @see login/login.html
   */
   public function index() {
      $this->data['pagina'] = "Inicia sesion";
      $this->twig->display('login/login.html', $this->data);
   }


   /**
   * Es la funcion inicial que valida el acceso
   */
   public function validalog() {
      $strinUsuario = (string)$this->input->post('usuario');
      $stringPassword  = (string)$this->input->post('password');
      if ($strinUsuario != '' && $stringPassword != '') { 
         //checamos credenciales
         $res = $this->usuarios->obtus($strinUsuario, $stringPassword);
         if (!empty($res) && count($res)>0) {
            if ($res->imagen == "") {
              $imagen = 'defaultavatar.png';
            }else{
              $imagen = $res->imagen;
            }
            $avatar = (file_exists(FCPATH."/img/avatars/".$imagen))? $imagen: 'defaultavatar.png';
            
            $session = array('id_usuario'       => $res->id_usuario,
                           'usuario'            => $res->usuario,
                           'nombre'             => $res->nombres,
                           'apellidos'          => $res->apellidos,
                           'status'             => $res->status,
                           'email'              => $res->email,
                           'avatar'             => $avatar
                           );
            $this->session->set_userdata($session);
            $this->session->set_userdata('logged', true);

            $mensaje2Datos = array('msg' => 'Bienvenido',
                                 'msgIco'=>'fa fa-check alert-icon ',
                                 'msgClass_name'=> 'success-notice');
            $this->session->set_flashdata('mensaje2', $mensaje2Datos);
            if ($res->status == '1') {
               redirect(base_url().'main/index');
            }else{
                $mensaje2Datos = array('msg' => 'Credenciales invalidas',
                                   'msgIco'=>'glyphicon glyphicon-warning-sign alert-icon',
                                   'msgClass_name'=> 'error-notice');
                $this->session->set_flashdata('mensaje2', $mensaje2Datos);
            }
         }else {
            $mensaje2Datos = array('msg' => 'Credenciales invalidas',
                                   'msgIco'=>'glyphicon glyphicon-warning-sign alert-icon',
                                   'msgClass_name'=> 'error-notice');
            $this->session->set_flashdata('mensaje2', $mensaje2Datos);
         }
      }else {
         $mensaje2Datos = array('msg' => 'Ingrese Datos válidos',
                                   'msgIco'=>'glyphicon glyphicon-warning-sign alert-icon',
                                   'msgClass_name'=> 'error-notice');
         $this->session->set_flashdata('mensaje2', $mensaje2Datos);
      }
      redirect($this->data['modulo'].'/login/index');
   }


   /**
   * Es la funcion para salir de la sesion
   */
   public function salir() {
      $this->session->sess_destroy();
      $mensaje2Datos = array('msg' => 'Tu sesion se cerro con éxito',
                              'msgIco'=>'glyphicon glyphicon-warning-sign alert-icon',
                              'msgClass_name'=> 'error-notice');
      $this->session->set_flashdata('mensaje2', $mensaje2Datos);
      redirect($this->data['modulo'].'/login/index');
   }

  public function recupera_password(){
    $stringUsuario = (string)$this->input->post('usuario');
    $usuario = $this->usuarios->selectBy(array('usuario' => $stringUsuario))[0];
    $dat = $usuario;
    if (!empty($usuario)) {
      $id_usuario = $usuario->id_usuario;
      $usuario = $usuario->usuario;
      $email = $dat->email;
      $nombreUsuario = $dat->nombres." ".$dat->apellidos;
      $uno = substr($stringUsuario,0,3);
      $dos = rand(1,99);
      $pass = $uno.$dos.'cen';
      $password=hash('sha512', $pass);
      $update = $this->usuarios->update($id_usuario,array('password' => $password));
      if ($update >0) {
        $bit = $this->util->enviaemail($email,
                                   'Tu password a sido restablecido correctamente',
                                   'Estimado '.$nombreUsuario.', Enviamos datos de acceso al sistema.
                                    Usuario: '.$usuario.' Password: '.$pass,
                                   '');
        if ($bit>0) {
           
          die('restablecido');
        }else{
          
          die('error-email');
        }
      }else{
        
        die('error-update');
      }
    }else{
    
      die('error-user');  
    }
  }

  /*FUNCION QUE CARGA EL LOGIN*/
  function lost_password(){
      $this->data['pagina'] = "Perdiste tu Password";
      $this->data['mensaje'] = $this->session->flashdata('mensaje');
      $this->twig->display('login/lost_password.html',$this->data);
  }

}
