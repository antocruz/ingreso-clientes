<?php
    //Alberto Ocotitla
    //la magia consiste en vez de hacer un insert por registro, hace un insert por cada N registros
    //disminuyendo considerablemente el tiempo necesario para introducir los datos
    
    class InsertMultiple
    {
        private $host;
        private $user;
        private $pass;
        private $database;
        private $tabla;
        private $num_inserts;
        private $inserts = array();


        function __construct($database)
        {
            
            $this->type = 'MySQL';
            $this->host = 'cpamysql.c6lywwcjkgvq.us-east-1.rds.amazonaws.com';
            $this->user = 'cpamaster';
            $this->pass = 'CPALores072';
            $this->database = $database;

            switch ($this->type)
            {
                case 'MySQL':
                    $db = mysql_connect($this->host,$this->user,$this->pass) or die("Error al crear la conexión");
                    mysql_select_db($this->database, $db) or die("Error al seleccionar la base de datos");
                    mysql_query("set names 'utf8'");
                    break;
            }
        }

        public function trunca_tabla($tabla)
        {
            $this->tabla = $tabla;
            mysql_query('truncate table '.$this->tabla); 
        }

        public function consulta($q)
        {

            $resp = mysql_query($q) or die(mysql_error());
            return $resp;
        }
        public function escapar($str)
        {
            return mysql_real_escape_string($str);
        }

        public function insert_multiple($tabla,$inserts,$num_inserts)
        {  
            $this->tabla = $tabla;
            $this->inserts = $inserts;
            $this->num_inserts = $num_inserts;
            $sqlb = "";
            $querys = 0;
            $pendientes = 0;
            $sqla = "INSERT IGNORE INTO ".$this->tabla." VALUES"; //primer parte de la query
            
            for($a=0;$a<=count($this->inserts)-1;$a++)
            {
                $querys++;
                $sqlb.= $this->inserts[$a]; //voy construyendo la cadena con los elementos del arreglo de valores

                if($querys<=$num_inserts)
                {
                  $pendientes++;
                  $last_sql = $sqlb;
                }
                else
                {
                  if($this->inserts[$a]!="")
                  {
                    mysql_query($sqla.substr($sqlb,1)) or die(mysql_error()); 
                    $pendientes=0;
                  }
                  $sqlb=""; $querys = 0;
                }
            }
 
            if($pendientes>0)
            {
              if(strlen($last_sql)>10)
              {
                mysql_query($sqla.substr($last_sql,1)) or die(mysql_error()); 
              }
            }
            return 1;
        }

        public function debug_insert_multiple($tabla,$inserts,$num_inserts)
        {  
            $this->tabla = $tabla;
            $this->inserts = $inserts;
            $this->num_inserts = $num_inserts;

            $sqla = "<p><b>INSERT INTO</b> ".$this->tabla." <b>VALUES</b>"; //primer parte de la query
            
            for($a=0;$a<=count($this->inserts);$a++)
            {
                $querys++;
                $sqlb.= $this->inserts[$a]; //voy construyendo la cadena con los elementos del arreglo de valores

                if($querys<=$num_inserts)
                {
                  $pendientes++;
                  $last_sql = $sqlb;
                }
                else
                {
                  if($this->inserts[$a]!="")
                  {
                    echo "<p>".substr($sqla.substr($sqlb,1),0,2048)."</p>";
                    $pendientes=0;
                  }
                  $sqlb=""; $querys = 0;
                }
            }
 
            if($pendientes>0)
            {
              if(strlen($last_sql)>10)
              {
                echo "<p>".substr($sqla.substr($last_sql,1),0,2048)."</p>";
              }
            }
            return 1;
        }


    }
?>