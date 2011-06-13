<?php
/*
*
*   Import users (and groups) from a CSV file
*
*
*   FORMAT:
*       * Código del Centro
*       * Nombre
*       * Apellidos
*       * Id usuario (uid, login)
*       * Grupo
*
*/

class Importer {
    function Importer($fname=NULL) {
        global $gui;
        $this->fname=$fname;
        $this->defaultPassword='cmadrid';
        $this->maxImport=2;
        return;
    }
    
    function saveImport() {
        global $gui;
        /* open $this->fname */
        $data = file_get_contents($this->fname);
        $lines = explode("\n", $data);
        $users=array();
        $i=0;
        foreach($lines as $line) {
            if ($line == '') continue;
            //$userdata=explode(",", $line);
            $userdata=preg_split('/;|,/', $line);
            
            if(sizeof($userdata) != 5) {
                $gui->session_error("Línea no contiene 5 campos =&gt; '$line' ");
                continue;
            }
            if($userdata[1] == '"Nombre"' || $userdata[1] == 'Nombre') continue;
            
            if( test_string($userdata[3]) ) {
                $gui->session_error("El identificador de usuario '".$userdata[3]."' contiene caracteres no ASCII.");
                continue;
            }
            
            $tmp=array('uid'           => $userdata[3],
                       'plainPassword' => $this->defaultPassword,
                       'cn'            => $userdata[1],
                       'sn'            => $userdata[2],
                       'description'   => $userdata[1] . ' '. $userdata[2],
                       'group'         => parse_valid($userdata[4]),
                       'loginShell'    => '/bin/false',
                       'role'          => '');
            sanitize($tmp, array('uid' => 'charnum',
                                 'cn'=>'charnum',
                                 'sn' => 'charnum',
                                 'description' => 'charnum',
                                 'loginShell' => 'shell',
                                 'role' => 'role',
                                 'plainPassword' => 'str',
                                 'group' => 'charnum'));
            $gui->debuga($tmp);
            $users[]=$tmp;
            $i++;
        }
        
        
        /* save file in IMPORTER_DIR */
        $fh = fopen(IMPORTER_DIR . "/pending.txt", 'w');
        fwrite($fh, serialize($users));
        fclose($fh);
        
        removeFileIfExists(IMPORTER_DIR . "/status.php");
        $this->writeStatus($number=$i, $done=0, $result='');
        //$gui->session_info("En proceso de importación $i cuentas de usuario...");
        
        /* fork process in background */
        $cmd="max-control-importer >> ".FORK_LOGFILE." 2>&1 &";
        //$cmd="max-control-importer >> /tmp/importer.log 2>&1 &";
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

    function writeStatus($number=0, $done=0, $result='') {
        global $gui;
        
        if ( file_exists ( IMPORTER_DIR . "/status.php" ) ) {
            include(IMPORTER_DIR . "/status.php" );
            $now=$importer['date'];
            $number=$importer['number'];
            $done=$importer['done']+$done;
            $info=$importer['info'].$gui->info;
            $error=$importer['error'].$gui->error;
        }
        else {
            $now=date('d-m-Y H:i:s');
            $info=$gui->info;
            $error=$gui->error;
        }
        $gui->debug(" ********* writeStatus()  now=$now number=$number done=$done");
        
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

    function doImport() {
        if ( ! $this->needToRun() ) {
            return;
        }
        
        if ($this->isLocked()) return;
        $this->lock();
        
        global $gui;
        $gui->debug("readImport() init ");
        
        $raw=file_get_contents(IMPORTER_DIR . "/pending.txt");
        $users=unserialize($raw);
        //$gui->debuga($users);
        
        $i=0;
        $ldap=new LDAP();
        foreach($users as $newuser) {
            $user = new USER($newuser);
            if ( $ldap->get_user($newuser['uid']) ) {
                //$i++;
                //$gui->session_error("El usuario '".$newuser['uid']."' ya existe.");
                continue;
            }
            elseif ( $user->newUser() ) {
                $i++;
                $this->_createGroup($newuser['group']);
            } /* end of user->newUser() */
            else {
                $gui->session_error("Error al crear usuario '".$newuser['uid']."'.");
            }
            
            /* añadir usuario a grupo */
            $groups=$ldap->get_groups($newuser['group']);
            if ( $groups[0] && $groups[0]->newMember($newuser['uid']) ) {
                    $gui->session_info("Usuario '".$newuser['uid']."' añadido al grupo '".$newuser['group']."'.</br>");
            }
            else {
                $gui->session_error("No se ha podido añadir al usuario '".$newuser['uid']."' al grupo '".$newuser['group']."'.");
            }
            
            
            /* salir si en el bucle hemos creado mas de X usuarios */
            if ($i >= $this->maxImport) break;
            
        } /* foreach */
        $ldap->disconnect();
        $this->unlock();
        if ($i >0) {
            $this->writeStatus($number=0, $done=$i, $result='');
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

    function _createGroup($groupname) {
        if ( $groupname == '' )
            return;
        global $gui;
        $ldap=new LDAP();
        $groups=$ldap->get_group($groupname);
        if (! $groups ) {
            /* crear el grupo si no existe */
            $groupdata=array('cn' => $groupname,
                             'description' => $groupname,
                             'createshared' => '1',
                             'readonly' => '1');
            $group=new GROUP($groupdata);
            if ( $group->newGroup('1', '0') )
                $gui->session_info("Grupo '".$group->cn."' creado correctamente.");
        }
        $ldap->disconnect();
    }
}
?>
