<?php
if(DEBUG)
    error_reporting(E_ALL);
/*
*
*   Import users (and groups) from a CSV file
*
*
*   FORMAT:
*        Nombre
*        Apellidos
*        Id. de usuario (login)
*        Centro (no se tiene en cuenta)
*        Clase (grupo) distinto de "-"
*        Tipo (alumno, profesor...) "emStudent" "emTeacher"
*
*
*/
/* "Nombre","Apellidos","uid","centro","clase","tipo" */
define('IMPORT_NAME', 0);
define('IMPORT_SURNAME', 1);
define('IMPORT_UID', 2);
define('IMPORT_CENTER', 3); /* not needed */
define('IMPORT_GROUP', 4); /* empty group if == "-" */
define('IMPORT_ROLE', 5); /* "emStudent" "emTeacher" */
define('MAX_UID_LENGTH', 20);

class Importer {
    function __construct($fname=NULL) {
        global $gui;
        $this->fname=$fname;
        $this->defaultPassword='cmadrid';
        $this->maxImport=5; // crear de 5 en 5
        return;
    }
    
    function saveImport() {
        global $gui;
        
        /* Create empty long.php */
        $fh = fopen(IMPORTER_DIR . "/long.php", 'w');
        $txt="<?php\n/* long UIDs */\n\$longUsernames=array();\n\n";
        fwrite($fh, $txt);
        fclose($fh);
        
        /* Create empty users_imported.php */
        $fh = fopen(IMPORTER_DIR . "/users_imported.php", 'w');
        $txt="<?php\n/* imported UIDS */\n\$usersImported=array();\n\n";
        fwrite($fh, $txt);
        fclose($fh);
        
        /* open $this->fname */
        $data = file_get_contents($this->fname);
        $lines = explode("\n", $data);
        $users=array();
        $i=0;
        $alumnos=0;
        $profesores=0;
        foreach($lines as $line) {
            if ($line == '') continue;
            //$userdata=explode(",", $line);
            $userdata=preg_split('/;|,/', $line);
            
            if(sizeof($userdata) != 6) {
                //$gui->session_error("Línea no contiene 6 campos =&gt; '$line' ");
                continue;
            }
            if($userdata[IMPORT_NAME] == '"Nombre"' || $userdata[IMPORT_SURNAME] == 'Apellidos') continue;
            
            /* forzar quitar espacios */
            $userdata[IMPORT_UID]=preg_replace('/\s+/','',$userdata[IMPORT_UID]);
            $userdata[IMPORT_UID]=preg_replace('/"/','',$userdata[IMPORT_UID]);
            if( sanitizeOne($userdata[IMPORT_UID], 'uid') != $userdata[IMPORT_UID] ) {
                $gui->session_error("El identificador de usuario '".$userdata[IMPORT_UID]."' contiene caracteres no válidos (ASCII).");
                continue;
            }
            
            $usergroup=parse_valid($userdata[IMPORT_GROUP]);
            if( $usergroup == "-" || $usergroup == "_" ) {
                $usergroup='';
            }
            if ( preg_match('/^[0-9]/', $usergroup) ) {
                /* empieza por numero */
                $usergroup="g_$usergroup";
            }
            /***************************************************/
            $userrole=$text = preg_replace( "{\s+}", '', parse_valid($userdata[IMPORT_ROLE]));
            if($userrole == 'emTeacher' || $userrole == 'emteacher') {
                $userrole='teacher';
                $profesores++;
            }
            else {
                $userrole=''; /* alumno vacío */
                $alumnos++;
            }
            /*********** 20 chars UID limit ***************/
            $extradata='';
            if ( strlen($userdata[IMPORT_UID]) > MAX_UID_LENGTH ) {
                $olduid=sanitizeOne($userdata[IMPORT_UID], 'uid');
                $extradata=", usuario sin recortar: $olduid";
            }
            /**********************************************/
            $tmp=array('cn'            => $userdata[IMPORT_UID],
                       'password'      => $this->defaultPassword,
                       'givenname'     => $userdata[IMPORT_NAME],
                       'sn'            => $userdata[IMPORT_SURNAME],
                       'description'   => $userdata[IMPORT_NAME] . ' '. $userdata[IMPORT_SURNAME].$extradata,
                       'group'         => $usergroup,
                       'loginshell'    => '/bin/false',
                       'role'          => $userrole);
            sanitize($tmp, array('cn' => 'uid',
                                 'givenname'=>'cnsn',
                                 'sn' => 'cnsn',
                                 'description' => 'cnsn',
                                 'loginshell' => 'shell',
                                 'role' => 'role',
                                 'password' => 'str',
                                 'group' => 'uid'));
            $gui->debuga($tmp);
            $users[]=$tmp;
            $i++;
        }
        
        if($i < 1 ) {
            $gui->session_error("No se encontraron datos en el archivo para importar.");
            return;
        }
        
        /* save file in IMPORTER_DIR */
        $fh = fopen(IMPORTER_DIR . "/pending.txt", 'w');
        fwrite($fh, serialize($users));
        fclose($fh);
        
        removeFileIfExists(IMPORTER_DIR . "/status.php");
        $this->writeStatus($number=$i, $done=0, $result='');
        $gui->session_info("En proceso de importación de $i cuentas de usuario ($alumnos alumnos, $profesores profesores)...");
        
        /* fork process in background */
        $cmd="max-control-importer >> ".FORK_LOGFILE." 2>&1 &";
        if(DEBUG)
            $cmd="max-control-importer DEBUG >> /tmp/importer.log 2>&1 &";
        pclose(popen($cmd, "r"));
    }

    function needToRun() {
        /* return True/False if this pending.txt exists */
        return file_exists ( IMPORTER_DIR . "/pending.txt" );
    }

    function isRunning() {
        /* return True/False if this pending.txt exists */
        return file_exists ( IMPORTER_DIR . "/status.php" );
    }

    function status() {
        if ( file_exists ( IMPORTER_DIR . "/status.php" ) ) {
            include_once(IMPORTER_DIR . "/status.php" );
            return $importer;
        }
        return;
    }
    
    function getLongUsernames() {
        if ( file_exists ( IMPORTER_DIR . "/long.php" ) ) {
            include_once(IMPORTER_DIR . "/long.php" );
            return $longUsernames;
        }
        return array();
    }

    function delete() {
        removeFileIfExists(IMPORTER_DIR . "/status.php");
        removeFileIfExists(IMPORTER_DIR . "/pending.txt");
        removeFileIfExists(IMPORTER_DIR . "/importer.lock");
        removeFileIfExists(IMPORTER_DIR . "/long.php");
        removeFileIfExists(IMPORTER_DIR . "/users_imported.php");
    }

    function stop() {
        removeFileIfExists(IMPORTER_DIR . "/pending.txt");
        removeFileIfExists(IMPORTER_DIR . "/importer.lock");
    }

    function checkMax() {
        if ( file_exists ( IMPORTER_DIR . "/status.php" ) ) {
            include(IMPORTER_DIR . "/status.php" );
            return $importer['done'] >= $importer['number'];
        }
    }

    function writeStatus($number=0, $done=0, $result='', $failed=0) {
        global $gui;
        
        if ( file_exists ( IMPORTER_DIR . "/status.php" ) ) {
            include(IMPORTER_DIR . "/status.php" );
            $now=$importer['date'];
            $number=$importer['number'];
            $ok=$importer['ok']+$done;
            $done=$importer['done']+$done;
            /*$info=$importer['info'].$gui->info;
            $error=$importer['error'].$gui->error;*/
            $info=$gui->info;
            $error=$gui->error;
            $failed=$importer['failed']+$failed;
        }
        else {
            /* new file default values */
            $now=date('d-m-Y H:i:s');
            $info=$gui->info;
            $error=$gui->error;
            $ok=0;
            $failed=0;
        }
        $gui->debug(" ********* writeStatus()  now=$now number=$number done=$done failed=$failed");
        
        $fh = fopen(IMPORTER_DIR . "/status.php", 'w');
        $status="<?php
/* status of importer */
\$importer=array();
\$importer['date']='$now';
\$importer['number']=\"$number\";
\$importer['done']=\"$done\";
\$importer['result']=\"$result\";
\$importer['info']=\"$info\";
\$importer['error']=\"$error\";
\$importer['failed']=\"$failed\";
\$importer['ok']=\"$ok\";

?>
";
        fwrite($fh, $status);
        fclose($fh);
    }

    function lock() {
        $fh = fopen(IMPORTER_DIR . "/importer.lock", 'w');
        fwrite($fh, "");
        fclose($fh);
    }

    function unlock() {
        removeFileIfExists(IMPORTER_DIR . "/importer.lock");
    }

    function isLocked() {
        if ( file_exists ( IMPORTER_DIR . "/importer.lock" ) ) {
            return true;
        }
        return false;
    }

    function finishedDate() {
        if (file_exists(IMPORTER_DIR . "/status.php")) {
            return date ('d-m-Y H:i:s', filemtime(IMPORTER_DIR . "/status.php"));
        }
        return 0;
    }

    function timeNeeded() {
        global $gui;
        if ( ! file_exists ( IMPORTER_DIR . "/status.php" ) ) {
            return 0;
        }
        include(IMPORTER_DIR . "/status.php" );
        $gui->debug("inicio='".$this->finishedDate()."' fin='".$importer['date']."'");
        return date_diff2($importer['date'], $this->finishedDate());
    }

    function doImport() {
        if ( ! $this->needToRun() ) {
            return;
        }
        
        if ($this->isLocked()) return;
        $this->lock();
        
        include(IMPORTER_DIR . "/status.php" );
        if ( $importer['number'] < 20 ) {
            $this->maxImport=1;
        }
        
        global $gui;
        $gui->debug("readImport() init ");
        
        $raw=file_get_contents(IMPORTER_DIR . "/pending.txt");
        $users=unserialize($raw);
        //$gui->debuga($users);
        
        if ( file_exists ( IMPORTER_DIR . "/status.php" ) ) {
            include(IMPORTER_DIR . "/status.php" );
            $gui->error=$importer['error'];
            $gui->info=$importer['info'];
        }
        
        
        $i=0;
        $failed=0;
        global $ldap;
        foreach($users as $newuser) {
            $origuid=$newuser['cn'];
            
            if ( $this->isUserImported($origuid) ) {
                //$gui->session_error("$i Usuario '".$origuid."' importado:continue...");
                continue;
            }
            
            if (strlen($newuser['cn']) == MAX_UID_LENGTH) {
                if ( $this->isUserImported($origuid) ) {
                    //$gui->session_error("$i Usuario '".$origuid."' de 20 ya importado:continue2...");
                    $gui->session_error("Usuario '".$origuid."' existe, no se crearán duplicados.");
                    continue;
                }
                /*if ( $ldap->get_user($origuid) ) {
                    //$gui->session_error("$i Usuario '".$origuid."' de 20 existe, añadimos 1 letra...");
                    $newuser['uid']=$origuid."x";
                }*/
            }
            
            /* user UID can't have more than 20 chars */
            if (strlen($newuser['cn']) > MAX_UID_LENGTH) {
                $create=false;
                $newuid=substr($newuser['cn'], 0, MAX_UID_LENGTH);
                //$gui->session_error("$i Usuario '".$origuid."' de más de 20...");
                if ( $this->isUserImported($origuid) ) {
                    //$gui->session_error("$i Usuario '".$newuser['uid']."' importado:continue3...");
                    continue;
                }
                
                if ( $ldap->get_user($newuid) ) {
                    /* user exists */
                    if( ! preg_match( '/'.$origuid.'/', $gui->error) ) {
                        $gui->session_error("Usuario acortado '$origuid' => '$newuid' ya existe.");
                    }
                    
                    /*
                    //$gui->session_error("$i Usuario '".$newuid."' existe, probando con números...");
                    $newuid=substr($origuid, 0, MAX_UID_LENGTH-1);
                    //try 9 times to get an no exist username
                    for($j=1; $j<10; $j++) {
                        //$gui->session_error("$i Probando usuario '".$newuid.$j."'...");
                        if ( $this->isUserImported($origuid) ) {
                            //$gui->session_error("Usuario '".$newuid.$j."' importado, saliendo del bucle...");
                            $create=false;
                            break;
                        }
                        if ( !$ldap->get_user($newuid . "$j") ) {
                            //$gui->session_error("$i Usuario '".$newuid.$j."' NO existe, creando...");
                            $create=true;
                            $this->LongUID($newuser['uid'], $newuid."$j");
                            $newuser['uid']=$newuid."$j";
                            break;
                        }
                        //else {
                        //    $gui->session_error("Usuario '".$newuid.$j."' existe, probando otro...");
                        //}
                    }*/

                    //$gui->session_error("$i Fin del for(create=$create) '$newuid'...=> ".$newuser['uid']);
                }
                else {
                    //$gui->session_error("$i ELSE Usuario '$newuid' no existe");
                    $create=true;
                    $this->LongUID($newuser['cn'], $newuid);
                    $newuser['cn']=$newuid;
                }
                if( ! $create ) {
                    continue;
                }
                else {
                    $gui->session_info("Nombre largo (".strlen($origuid)."caracteres) acortado: $origuid =&gt; ".$newuser['cn']);
                }
            }
            /*******************************************/
            //$gui->session_error("MAIN Usuario '".$origuid."' ".strlen($origuid)."...");
            $user = new USER($newuser);
            $user->background=false;
            if ( $ldap->get_user($newuser['cn']) ) {
                $gui->session_error("El usuario '".$newuser['cn']."' ya existe.");
                continue;
            }
            elseif ( $user->newUser() ) {
                $this->userImported($origuid);
                $i++;
                $this->_createGroup($newuser['group'], $ldap);
            } // end of user->newUser()
            else {
                $gui->debug(preg_match( '/'.$newuser['cn'].'/', $gui->error));
                if( ! preg_match( '/'.$newuser['cn'].'/', $gui->error) ) {
                    $gui->session_error("Error al crear usuario '".$newuser['cn']."' =&gt; " . $user->errortxt);
                    $failed++;
                }
                continue;
            }
            
            
            if($newuser['group'] != '') {
                // añadir usuario a grupo
                $groups=$ldap->get_groups($newuser['group']);
                $gui->debuga($groups);
                if ( isset($groups[0]) && $groups[0]->newMember($newuser['cn']) ) {
                        $gui->session_info("Usuario '".$newuser['cn']."' añadido al grupo '".$newuser['group']."'");
                }
                else {
                    $gui->session_error("No se ha podido añadir al usuario '".$newuser['cn']."' al grupo '".$newuser['group']."'.");
                }
            }
            
            
            // salir si en el bucle hemos creado mas de X usuarios
            if ($i >= $this->maxImport) {
                //$gui->session_error("i($i) > maxImport(".$this->maxImport.")");
                break;
            }
            
        } /* foreach */
        $this->unlock();
        if ($i >0) {
            $this->writeStatus($number=0, $done=$i, $result='', $failed=$failed);
        }
        else {
            /* delete pending.txt */
            $gui->debug("i=0 exiting...");
            removeFileIfExists(IMPORTER_DIR . "/pending.txt");
            /* hacer recache */
            $cmd='sudo '.MAXCONTROL.' recache > /dev/null 2>&1 &';
            $gui->debug($cmd);
            pclose(popen($cmd, "r"));
            $this->writeStatus($number=0, $done=$i, $result='', $failed=$failed);
        }
        if ($this->checkMax()) {
            /* done > number */
            $gui->debug("done >= max exiting...");
            removeFileIfExists(IMPORTER_DIR . "/pending.txt");
            /* hacer recache */
            $cmd='sudo '.MAXCONTROL.' recache > /dev/null 2>&1 &';
            $gui->debug($cmd);
            pclose(popen($cmd, "r"));
        }
    }

    function _createGroup($groupname, $ldap=NULL) {
        if ( $groupname == '' )
            return;
        global $gui;
        if (!$ldap)
            $ldap=new LDAP();
        $groups=$ldap->get_group($groupname);
        if (! $groups ) {
            /* crear el grupo si no existe */
            $groupdata=array('cn' => $groupname,
                             'description' => $groupname,
                             'createshared' => '1',
                             'readonly' => '1');
            $group=new GROUP($groupdata);
            /* newGroup($createshared, $readonly, $grouptype=2) */
            if ( $group->newGroup('1', '0') )
                $gui->session_info("Grupo '".$group->cn."' creado correctamente.");
        }
    }

    function LongUID($cn, $newuid) {
        file_put_contents(IMPORTER_DIR . "/long.php", "\$longUsernames['$cn']=array('$cn','$newuid');\n", FILE_APPEND | LOCK_EX);
    }


    function userImported($cn) {
        file_put_contents(IMPORTER_DIR . "/users_imported.php", "\$usersImported[]='$cn';\n", FILE_APPEND | LOCK_EX);
    }
    
    
    function isUserImported($cn) {
        global $gui;
        if ( file_exists ( IMPORTER_DIR . "/users_imported.php" ) ) {
            include(IMPORTER_DIR . "/users_imported.php" );
            if( in_array($cn, $usersImported) ) {
                //$gui->session_error("isUserImported($uid)=true");
                return true;
            }
        }
        //$gui->session_error("isUserImported($uid)=false");
        return false;
    }
    
    function getNumUsersImported() {
        if ( file_exists ( IMPORTER_DIR . "/users_imported.php" ) ) {
            include(IMPORTER_DIR . "/users_imported.php" );
            return sizeof($usersImported);
        }
        return 0;
    }
}

