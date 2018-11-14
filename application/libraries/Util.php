<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Libreria con funciones comunes
 * @author Antonio Cruz Arias
 * @package principal
 * @subpackage librerias
 */
class Util {
    /**
     * @var codeigniter_instance $CI almacena una instancia de codeigniter
     */
	private $CI;

	public function __construct(){
		$this->CI =& get_instance();
	}

    /**
     * sube un archivo al servidor y guarda un registro en rs_files
     * @param array_mixed $post un arreglo con $_POST, checar el codigo
     *
     * @uses rs_files::insert
     *
     * @return string un mensaje de resultado
     */
    public function subearchivo($post, $FILES) {
        if(isset($FILES["archivo"]) && $FILES["archivo"]["error"]== UPLOAD_ERR_OK) {
            $UploadDirectory = FCPATH.'/downloads/cotizaciones/'; //directorio de subidas
            $this->CI->load->model('rs_files');
            //tamaño permitido
            if ($FILES["archivo"]["size"] > 5242880) {
                return "error3";
            }
            //archivos permitidos
            switch(strtolower($FILES['archivo']['type'])) {
                case 'image/png':
                //case 'image/tiff':
                case 'image/jpeg':
                //case 'image/pjpeg':
                //case 'text/plain':
                //case 'text/html':
                //case 'application/x-zip-compressed':
                case 'application/pdf':
                //case 'application/msword':
                //case 'application/vnd.ms-excel':
                //case 'video/mp4':
                    break;
                default:
                    return 'paramerror';
            }
            $File_Name          = strtolower($FILES['archivo']['name']);
            $File_Ext           = substr($File_Name, strrpos($File_Name, '.')); //tipo de archivo
            $Random_Number      = rand(0, 9999999999); //nombre del archivo
            $NewFileName        = $File_Name.'_'.$Random_Number.$File_Ext;

            if(move_uploaded_file($FILES['archivo']['tmp_name'], $UploadDirectory.$NewFileName )) {
                //guarda en base de datos
                $dat = array('id_rs_eval_enc' => $post['terreno'],
                             'id_rs_question_phase' => $post['pregunta'],
                             'file_name' => $NewFileName,
                             'file_path' => $UploadDirectory,
                             'cost' => $post['monto'],
                             'description' => $post['desc'],
                             'phase' => $post['fase'],
                             'stage' => $post['etapa'],
                             'status' => 1);

                if ($this->CI->rs_files->insert($dat)>-1) {
                    return 'ok';
                }
                else {
                    return 'dbe';
                }
            }
            else {
                return 'error2';
            }
        }
        else {
            return 'error1';
        }
    }

    /**
     * descarga un archivo del servidor indicado en rs_files
     * @param int $idres el id del recurso (id_rs_files)
     *
     * @uses rs_files::select
     *
     * @return string|png|jpg|tif|pdf el recurso o un mensaje de error
     */
    public function descarga($idres) {
        if ($idres>0) {
            $this->CI->load->model('rs_files');
            $cot = $this->CI->rs_files->select(array('id_rs_files' => $idres));

            if(!empty($cot)) {
                //nombre y ruta del del documento
                $file = $cot[0]->file_path.$cot[0]->file_name;

                //primero checamos si ya existe el archivo
                if (file_exists($file)) {
                    //que tipo de archivo es
                    if (FALSE != strpos($cot[0]->file_name, "pdf")) {
                        $this->CI->load->library('m_pdf');
                        $pdf = $this->CI->m_pdf->load();

                        header("Content-type:application/pdf");
                        header("Content-Disposition:inline;filename='$filename'");
                        readfile($file);
                    }
                    else if (FALSE != strpos($cot[0]->file_name, "jpg")) {
                        $fres = base64_encode(file_get_contents($file));

                        echo '<img src="data:image/jpeg;base64,'.$fres.'" />';
                        exit;
                    }
                    else if (FALSE != strpos($cot[0]->file_name, "png")) {
                        $fres = base64_encode(file_get_contents($file));

                        echo '<img src="data:image/png;base64,'.$fres.'" />';
                        exit;
                    }
                    else if (FALSE != strpos($cot[0]->file_name, "tif")) {
                        header("Content-type:application/tiff");
                        header("Content-Disposition:attachment;filename='$filename'");
                        readfile($file);
                    }
                }
                else {
                    die('Error: El documento no se encontro');
                }
            }
        }
    }

    /**
     * genera un hash en sha256
     * @param int $len la longitud de la cadena aleatoria
     * @param string $mac cadena extra
     *
     * @return string hash
     */
	public function generaKDB($len, $mac){
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; //!#$%&/()=*+?';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $len; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return hash('sha256', $mac . $randomString);
	}

	/**
	 * busca dentro de un array de objetos
	 * @param string $aguja              = valor que se quiere buscar
	 * @param string $dondePropiedad     = la propiedad del objeto donde se quiere buscar/comparar la aguja
	 * @param string $pajar              = el array de objetos
	 * @param boolean $pararPrimera      = indica si debe parar a la primera ocurrencia, entonces devuelve
	 * 							  un valor booleano, en caso contrario se devuelve el array resultados, con
	 *							  los indices de los objetos en donde fue encontrada la aguja
	 * @param string $retornarValoresde  = la propiedad de cada objeto dentro del array que se devolvera como valor
	 *							  en el array dde resultados, es necesario especificar como false el
	 *							  parametro $pararPrimera
     *
	 * @return boolean|array verdaero o falso si $pararprimera = true, contrario un array con los resultados de la busqueda
	 */
	static function obtenArrayObjetos($aguja, $dondePropiedad, $pajar, $pararPrimera = true, $retornarValoresde=''){
		$retorno = ($pararPrimera)? 'bool': (($retornarValoresde=='')? 'aguja': $retornarValoresde);
		$resultados = [];

		if (is_array($pajar)){
			//por cada elemento del pajar
			foreach ($pajar as $k => $objeto) {
				//comprueba la existencia de la propiedad donde buscar
				if (isset($objeto->$dondePropiedad)){
					if ($objeto->$dondePropiedad == $aguja){
						//retornamos que si lo encontro
						if ($retorno == 'bool'){
							return true;
						}
						//prepara un array con los indices de los objetos donde es encontrada la aguja
						else if ($retorno == 'aguja'){
							$resultados[] = $k;
						}
						else {
							$resultados[] = $objeto->$retornarValoresde;
						}
					}
				}
				//no encontrado
				else{
					//si $pararPrimera == true entonces, como no existe donde buscar no tiene sentido seguir
					if ($retorno == 'bool'){
						return false;
					}
				}
			}
		}
		//finalmente retornamos que no fue encontrado si llego aqui ya que se quiere solo la primera ocurrencia
		if ($retorno == 'bool'){
			return false;
		}
		//o en caso contrario se retorna lo que se encontro
		else {
			return $resultados;
		}
	}

    /**
     * busca dentro de un array de objetos
     * @deprecated
     */
	static function buscarArrayObjs($aguja, $dondePropiedad, $pajar) {
		if (is_array($pajar)) {
			foreach ($pajar as $p) {
				if (strcmp($p->$dondePropiedad, $aguja)) {
					return true;
				}
			}
		}
		return false;
	}

	/**
     * checa que haya iniciado sesion, redirecciona si no ha iniciado sesion
     * @global session['logged']
     *
     * @return boolean true en caso de que si ha inicado sesion
     */
	public function chk_sess(){
		if ($this->CI->session->userdata('logged')){
			return true;
		}
		else{
			$this->CI->session->set_flashdata('mensaje', 'Inicia sesión');
			redirect(base_url());
		}
	}


    /**
     * envia un email, usa la libreria de emal de CI
     * @param string $para correo destino
     * @param string $asunto el asunto del mensaje
     * @param string $mensaje el mensaje
     * @param string $adjunto la ruta del archivo adjunto
     * @param string $correo de copia
     *
     * @uses CI::email
     *
     * @return boolean si tubo exito el envio
     */
	public function enviaemail($para, $asunto, $mensaje, $adjunto='', $copia=''){
		$this->CI->load->library('email');

		$this->CI->email->from('anto.cruz83@gmail.com', 'Examen Ingenia');
		$this->CI->email->to($para);

		if ($copia!='' || is_array($copia)){
			$this->CI->email->cc($copia);
		}

		$this->CI->email->subject($asunto);
		$this->CI->email->message($mensaje);

		if (is_array($adjunto)){
			foreach ($adjunto as $a) {
				if (is_string($a)){
					$this->CI->email->attach($a);
				}
			}
		}
		else if ($adjunto!='' && is_string($adjunto)){
			$this->CI->email->attach($adjunto);
		}

		try{
			return $this->CI->email->send();
		} catch(Exception $e){
			return $this->CI->email->print_debugger();
		}
	}
}
