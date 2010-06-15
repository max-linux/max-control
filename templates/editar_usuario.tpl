

<h3>Editando usuario  <span class='stitle'>{$username}</span></h3> 
 

 <form action='{$urlform}' method='post'> 
    <table class='formTable'> 
    <tr> 
        <td class='tright'><span class="ftitle">Nombre y apellidos:</span></td>
        <td><input type='text' class='inputText' name='cn' id='cn' value="{$u->attr('cn')}" /> 
        </td>
    </tr>

    <tr>
        <td class='tright'><span class='ftitle'>Rol (permisos):</span></td> 
        <td> 
            <select name='role' id='role' > 
                <option value='' {if $u->get_role() == ''}selected{/if}>Alumno</option> 
                <option value='teacher' {if $u->get_role() == 'teacher'}selected{/if}>Profesor</option> 
                <option value='admin' {if $u->get_role() == 'admin'}selected{/if}>Administrador</option> 
            </select> 
        </td> 
    <tr>


    <tr>
        <td class='tright'><span class='ftitle'>Acceso a consola:</span></td> 
        <td> 
            <select name='loginShell' id='loginShell' > 
                <option value='/bin/false' {if $u->attr('loginShell') == '/bin/false'}selected{/if}>Sin acceso a shell</option> 
                <option value='/bin/bash' {if $u->attr('loginShell') == '/bin/bash'}selected{/if}>Con acceso a shell (bash)</option> 
            </select> 
        </td> 
    <tr>

    <tr>
        <td class='tright'><span class='ftitle'>Cambiar contraseña:</span></td> 
        <td>
            <input type='password' class='inputText' name='newpwd' id='newpwd' value="" autocomplete="off" /> (dejar vacío para no cambiar)
        </td>
    <tr>
    <tr>
        <td class='tright'><span class='ftitle'>Confirmar contraseña:</span></td> 
        <td>
            <input type='password' class='inputText' name='newpwd2' id='newpwd2' value="" autocomplete="off" onblur='javascript:checkpass();' /> 
            <span class="error" style="display:none;" id='badpassword'>Las contraseñas no coinciden</span>
        </td>
    <tr>

    </tr> 
    <tr> 
        <td></td> 
        <td> 
        <input class='inputButton' type='submit' name='{$action}' value="Guardar" alt="Guardar" /> 
        <input type='hidden' name='uid' value='{$u->attr('uid')}' />
        </td> 
    </tr> 
    </table> 
    </form> 

<script type="text/javascript">
    var hostname="{$hostname}";
    var ajaxurl="{$baseurl}/index.php?ajax=1";
</script>

{literal}
<script type="text/javascript">
function checkpass() {
    if ( $('#newpwd')[0].value !=  $('#newpwd2')[0].value) {
        $('#badpassword')[0].style.display='';
        return false;
    }
    else {
        $('#badpassword')[0].style.display='none';
        return true;
    }
}
</script>
{/literal}


{if $DEBUG}
{debug}
{/if}

