<h3>Añadir usuario</h3>


<form action='{$urlform}' method='post'> 
    <table class='formTable'> 
        <tr> 
            <td class='tright'><span class="ftitle">Nombre de usuario:</span></td> 
            <td>
                <input type='text' class='inputText' name='uid' autocomplete="off" onblur='javascript:useduid(this.value);' />
                <span class="error" style="display:none;" id='usernotvalid'>El identificador está ocupado</span>
                <span class="note" style="display:none;" id='uservalid'>El identificador está libre</span>
                <span class="error" style="display:none;" id='userempty'>El identificador no puede estar vacío</span>
                <span class="error" style="display:none;" id='userinvalid'>Identificador no válido (letras ASCII, números o .-_)</span>
            </td> 
        </tr> 

        <tr> 
            <td class='tright'><span class='ftitle'>Nombre:</span></td> 
            <td><input type='text' class='inputText' name='givenName' autocomplete="off" /></td> 
        </tr>

        <tr>
            <td class='tright'><span class='ftitle'>Apellido:</span></td> 
            <td><input type='text' class='inputText' name='sn' autocomplete="off" /></td> 
        </tr>

        <tr> 
            <td class='tright'><span class='ftitle'>Comentario:</span></td> 
            <td><input type='text' class='inputText' name='description' autocomplete="off" /></td> 
        </tr>

        <tr> 
            <td class='tright'><span class='ftitle'>Contraseña:</span></td> 
            <td><input type='password' class='inputText' name='password' id='password' autocomplete="off"/></td> 
        </tr>

        <tr>
            <td class='tright'><span class='ftitle'>Confirme contraseña:</span></td> 
            <td>
                <input type='password' class='inputText' name='repassword' id='repassword' autocomplete="off" onblur='javascript:checkpass();' />
                <span class="error" style="display:none;" id='badpassword'>Las contraseñas no coinciden</span>
            </td> 
        </tr> 

        <tr>
            <td class='tright'><span class='ftitle'>Rol (permisos):</span></td> 
            <td> 
                <select name='role' id='role' > 
                    <option value=''>Alumno</option> 
                    <option value='teacher'>Profesor</option> 
                    <option value='admin'>Administrador</option> 
                </select> 
            </td> 
        </tr>

        <tr>
            <td class='tright'><span class='ftitle'>Acceso a consola:</span></td> 
            <td> 
                <select name='loginShell' id='loginShell' > 
                    <option value='/bin/false'>Sin acceso a shell</option> 
                    <option value='/bin/bash' selected>Con acceso a shell (bash)</option> 
                </select> 
            </td> 
        </tr>

        <tr> 
            <td></td> 
            <td> 
            <input class='inputButton' type='submit' name='add' value="Añadir" alt="Añadir" /> 
            </td> 
        </tr> 
    </table> 
</form>

<script type="text/javascript">
    var ajaxurl="{$baseurl}/index.php?ajax=1";
</script>

{literal}
<script type="text/javascript">
<!--
function useduid(uid) {
    if ( uid == '' ) {
        $('#userempty')[0].style.display='';
        return false;
    }
    $('#userempty')[0].style.display='none';
    $('#userinvalid')[0].style.display='none';
    // ver si el usuario está ocupado
    $.ajax({
      type: "POST",
      url: ajaxurl,
      data: "accion=useduid&uid="+uid,
      success: function(data) {
        if (data == 'used') {
            $('#usernotvalid')[0].style.display='';
            $('#uservalid')[0].style.display='none';
            return false;
        }
        else if (data == 'invalid') {
            $('#userinvalid')[0].style.display='';
            $('#uservalid')[0].style.display='none';
            return false;
        }
        else if (data == 'free') {
            $('#usernotvalid')[0].style.display='none';
            $('#uservalid')[0].style.display='';
        }
        else {
            alert("Respuesta desconocida: \n'" + data + "'");
        }
      }
    });
}
function checkpass() {
    if ( $('#password')[0].value !=  $('#repassword')[0].value) {
        $('#badpassword')[0].style.display='';
        return false;
    }
    else {
        $('#badpassword')[0].style.display='none';
        return true;
    }
}
-->
</script>
{/literal}
