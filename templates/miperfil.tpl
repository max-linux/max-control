

<h3>Editando mi perfil  <span class='stitle'>{$username}</span></h3> 
 

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
            {if $u->get_role() == ''}Alumno{/if}
            {if $u->get_role() == 'teacher'}Profesor{/if}
            {if $u->get_role() == 'admin'}Administrador{/if}
        </td> 
    </tr>


    <tr>
        <td class='tright'><span class='ftitle'>Cambiar contraseña:</span></td> 
        <td>
            <input type='password' class='inputText' name='newpwd' id='newpwd' value="" autocomplete="off" /> (dejar vacío para no cambiar)
        </td>
    </tr>

    <tr>
        <td class='tright'><span class='ftitle'>Confirmar contraseña:</span></td> 
        <td>
            <input type='password' class='inputText' name='newpwd2' id='newpwd2' value="" autocomplete="off" onblur='javascript:checkpass();' /> 
            <span class="error" style="display:none;" id='badpassword'>Las contraseñas no coinciden</span>
        </td>
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



{if $pruebas}
{debug}
{/if}

