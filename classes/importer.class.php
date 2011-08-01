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


class Importer {
    function Importer($fname=NULL) {
        global $gui;
        $this->fname=$fname;
        $this->defaultPassword='cmadrid';
        $this->maxImport=5;
        return;
    }
    
    function saveImport() {
        global $gui;
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
            
            if( test_string($userdata[IMPORT_UID]) ) {
                $gui->session_error("El identificador de usuario '".$userdata[IMPORT_UID]."' contiene caracteres no ASCII.");
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
            if($userrole == 'emTeacher') {
                $userrole='teacher';
                $profesores++;
            }
            else {
                $userrole=""; /* alumno vacío */
                $alumnos++;
            }
            $tmp=array('uid'           => $userdata[IMPORT_UID],
                       'plainPassword' => $this->defaultPassword,
                       'cn'            => $userdata[IMPORT_NAME],
                       'sn'            => $userdata[IMPORT_SURNAME],
                       'description'   => $userdata[IMPORT_NAME] . ' '. $userdata[IMPORT_SURNAME],
                       'group'         => $usergroup,
                       'loginShell'    => '/bin/false',
                       'role'          => $userrole);
            sanitize($tmp, array('uid' => 'charnum',
                                 'cn'=>'cnsn',
                                 'sn' => 'cnsn',
                                 'description' => 'cnsn',
                                 'loginShell' => 'shell',
                                 'role' => 'role',
                                 'plainPassword' => 'str',
                                 'group' => 'charnum'));
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

    function delete() {
        removeFileIfExists(IMPORTER_DIR . "/status.php");
        removeFileIfExists(IMPORTER_DIR . "/pending.txt");
        removeFileIfExists(IMPORTER_DIR . "/importer.lock");
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
            $user = new USER($newuser);
            if ( $ldap->get_user($newuser['uid']) ) {
                //$gui->session_error("El usuario '".$newuser['uid']."' ya existe.");
                continue;
            }
            elseif ( $user->newUser() ) {
                $i++;
                $this->_createGroup($newuser['group'], $ldap);
            } /* end of user->newUser() */
            else {
                $gui->debug(preg_match( '/'.$newuser['uid'].'/', $gui->error));
                if( ! preg_match( '/'.$newuser['uid'].'/', $gui->error) ) {
                    $gui->session_error("Error al crear usuario '".$newuser['uid']."' =&gt; " . $user->errortxt);
                    $failed++;
                }
                continue;
            }
            
            if($newuser['group'] != '') {
                /* añadir usuario a grupo */
                $groups=$ldap->get_groups($newuser['group']);
                if ( $groups[0] && $groups[0]->newMember($newuser['uid']) ) {
                        $gui->session_info("Usuario '".$newuser['uid']."' añadido al grupo '".$newuser['group']."'.</br>");
                }
                else {
                    $gui->session_error("No se ha podido añadir al usuario '".$newuser['uid']."' al grupo '".$newuser['group']."'.");
                }
            }
            
            /* salir si en el bucle hemos creado mas de X usuarios */
            if ($i >= $this->maxImport) break;
            
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
}
?>
