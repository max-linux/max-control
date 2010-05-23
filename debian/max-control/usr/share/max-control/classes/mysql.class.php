<?php

// MySql class file

class MYSQL{
	
	var $sql_host;
	var $sql_usuario;
	var $sql_pass;
	var $sql_database;
	var $db_conexion;
	var $campos_tabla = array();
	var $nombres_tabla = array();
	var $campo_id;
	var $resource;
	var $tabla;
	
	
	function debug($txt){
	    global $gui;
	    if (isset($gui)){
	        $gui->debug($txt);
	    }
	    elseif(pruebas){
	        echo "$txt\n<br/>";
	    }
	}
	
	function alert($txt){
	    global $gui;
	    if (isset($gui)){
	        $gui->alert($txt);
	    }
	    elseif(pruebas){
	        echo "$txt\n<br/>";
	    }
	}
	
	function MYSQL(){
	    global $gui;
	    global $site;
	    //$this->debug("MYSQL::init()");
	    
	    $this->sql_host=$site["db_host"];
	    $this->sql_usuario=$site["db_user"];
	    $this->sql_pass=$site["db_pass"];
	    $this->sql_database=$site["db_database"];
	    $this->conectar();
	
	}
	
	function debug_sql()
        {
        global $gui;
        $txt=mysql_error();
        if ($txt != ""){
                $this->alert("Error en consulta SQL: $txt ");
                }
        }

	
	function conectar()
	{
	    global $gui;
		//if (pruebas) debug("<br />mysql_connect(\"$this->sql_host\", \"$this->sql_usuario\", \"$this->sql_pass\")<br />");
		
		$this->db_conexion= @mysql_connect("$this->sql_host", "$this->sql_usuario", "$this->sql_pass") or $gui->debug("Error de conexión con la base de datos") ;
		@mysql_select_db("$this->sql_database") or $gui->debug("Error seleccionado base de datos $this->sql_database") ;
		
		// set encoding
		//mysql_query("SET NAMES 'utf8'", $this->db_conexion);

	}
	
	function desconectar() {
	  if ( get_resource_type($this->db_conexion) == "mysql link" ) {
        @mysql_close($this->db_conexion);
      }
   }
	
	function query($sql)
	{
	    global $gui;
	    global $contador_mysql;
	    $contador_mysql++;
		//if (pruebas) $this->debug("<br /><spam class='blue'>DEBUG: sql=". htmlentities($sql) . "</spam>");
		$this->resource = mysql_query($sql,$this->db_conexion);
		$this->debug_sql();
		return $this->resource;
	}
	
	
	
	function get_array($sql) {
	    global $contador_mysql;
	    $contador_mysql++;
	    
	    $this->conectar();
	    $this->resource=$this->query($sql);
        $tmp = array();
        while ($row = @mysql_fetch_assoc($this->resource)){
            $tmp[] = $row;
        }
        $this->desconectar();
        return $tmp;
	}
	
	function clean_html($txt){
	    /*$this->debug("clean_html" . $txt);*/
	    $txt=ereg_replace("<[^>]*>","",$txt );
	    $txt=ereg_replace("<","&lt;",$txt);
	    $txt=ereg_replace(">","&gt;",$txt);
	    return $txt;
	}
	
		
		function validar($nombre, $tipo)
		{
			global $form;
			$valor=$form->leer_datos($nombre);
			debug("VALIDANDO: nombre=$nombre tipo=$tipo valor=$valor");
			switch ($tipo) {
				case "datetime":
						if (($txt= strtotime($valor)) == -1 || $valor === false) {
							$form->error("ERROR: $nombre no es una fecha válida.");
							print_debug();
							die();
						} 
						$valor=date2mysql($valor);
						//debug("<br>FECHA mysql= $valor<br><br><br>");
						/*if(! is_date($valor))
						{
							$form->error("ERROR: $nombre no es una fecha");
							exit;
						}*/
						break;
				case "tinyint":
						if($valor!=1) $valor=0;
						//debug("TINYINT \$valor=$valor",'red');
						break;
				/*case "int":
						if(! is_int($valor))
						{
							$form->error("ERROR: $nombre no es un número entero.");
							print_debug();
							die();
						}
						break;
				*/
				case "float":
						debug("float");
						break;
				case "varchar":
				case "text":
						if (! is_string($valor))
						{
							$form->error("ERROR: $nombre no es un texto.");
							print_debug();
							die();
						}
						break;
				
				}// fin del switch
			return $valor;
		} // fin de validar
		
		
		
		
		function mostrar_bonito($txt)
		{
			// reemplazamos _ por espacios
			$txt=str_replace("_", " ", $txt);
			// ponemos la primera letra de cada palabra en mayúscula
			$txt=ucwords($txt);
			return $txt;
		}// fin de mostrar_bonito
		
		
		
		function get_dbsize($tabla) {
             global $db_database;
             if ($res = $this->query("SHOW TABLE STATUS FROM ". $db_database)) {
               $tables = 0;
               $mysql_size=0;
               while ($row = mysql_fetch_array($res,MYSQL_ASSOC)) {
                        if($tabla==$row["Name"]){
                                echo "Registros=" . $row["Rows"] . " Tamaño= " . $this->human_size($row["Data_length"],1) . "\n";
                           }
                           if($tabla=="todas"){
                                   $mysql_size = $mysql_size + $row["Data_length"];
                                   }
                   $tables++;
               }
               if($tabla=="todas"){
                                   echo $this->human_size($mysql_size,1) . "\n";
               }
           }
		}


	function human_size($size,$dec=1){
		   $size_names= array('Byte','KByte','MByte','GByte', 'TByte','PB','EB','ZB','YB','NB','DB');
		   $name_id=0;
		   while($size>=1024 && ($name_id<count($size_names)-1)){
			   $size/=1024;
			   $name_id++;
		   }
		   return round($size,$dec).' '.$size_names[$name_id];
	}

		
		
		
		
}// fin de la clase
?>
