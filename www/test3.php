<?php




if (! ($cid=ldap_connect("127.0.0.1", 390))) {
    echo "Error: No se pudo conectar con el servidor de autenticación.\n".ldap_error($cid);
}

ldap_set_option($cid, LDAP_OPT_PROTOCOL_VERSION, 3);
if ( ! ($bid=ldap_bind($cid, 'cn=zentyal,dc=madrid,dc=local', '2HFaeqB0=WFEGdVi2kP6')) ) {
    echo "Error: Usuario o contraseña incorrectos.\n".ldap_error($cid);
    die();
}

$filter = '(objectclass=posixAccount)';

if (! ($search=ldap_search($cid, "dc=madrid,dc=local", $filter))) {
    echo "Error: busqueda incorrecta.\n".ldap_error($cid);
}
$number_returned = ldap_count_entries($cid, $search);

$found=ldap_get_attributes($cid, ldap_first_entry($cid, $search));


echo print_r($found,true);


#$count=$found['count'];
#unset($found['count']);
#$found[$count]=array('count'=> 1, 0=>'FF:FF:FF:FF:29:86');
#$found['macAddress']='FF:FF:FF:FF:29:86';
#$count++;

#$found['count']=$count;

#// update objectClass
#$found['objectClass'][]='ieee802Device';
#unset($found['objectClass']['count']);
#$obj=array('objectClass' => $found['objectClass']);
#echo print_r($obj,true);
#$r = ldap_modify($cid, 'uid=wxp$,ou=Computers,dc=max-server', $obj );
#echo print_r($r, true);

#$mac=array('macAddress' => $found['macAddress'] );
#$r = ldap_modify($cid, 'uid=wxp$,ou=Computers,dc=max-server', $mac );
#echo print_r($r, true);
?>
