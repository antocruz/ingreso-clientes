<?php
class MysqlConnect{

	private $db_host = 'cpamysql.c6lywwcjkgvq.us-east-1.rds.amazonaws.com';
	private $db_user = 'cpamaster';
	private $db_pass = 'CPALores072';
	private $db_name = 'cpa_cef';
	protected $mysqli = null;

	public function __construct($params=[]){
		$this->db_host = isset($params['db_host']) ? $params['db_host'] : $this->db_host;
		$this->db_user = isset($params['db_user']) ? $params['db_user'] : $this->db_user;
		$this->db_pass = isset($params['db_pass']) ? $params['db_pass'] : $this->db_pass;
		$this->db_name = isset($params['db_name']) ? $params['db_name'] : $this->db_name;
		$this->mysqli = new mysqli($this->db_host,$this->db_user,$this->db_pass,$this->db_name);
		if ($this->mysqli->connect_errno){
			exit("Falló la conexion: ".$mysqli->connect_error);
		}
	}
}
?>