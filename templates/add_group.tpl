<h3>Añadir grupo</h3>


<form action='{$urlform}' method='post'> 
    <table class='formTable'> 
        <tr> 
            <td class='tright'><span class="ftitle">Nombre del grupo:</span></td> 
            <td>
                <input type='text' class='inputText' name='cn' id='cn' autocomplete="off" onblur='javascript:usedcn(this.value);' />
                <span class="error" style="display:none;" id='groupnotvalid'>El identificador está ocupado</span>
                <span class="note" style="display:none;" id='groupvalid'>El identificador está libre</span>
                <span class="error" style="display:none;" id='groupempty'>El identificador no puede estar vacío</span>
                <span class="error" style="display:none;" id='groupinvalid'>Identificador no válido (letras ASCII, números o .-_)</span>
            </td> 
        </tr> 

        <tr> 
            <td class='tright'><span class='ftitle'>Comentario:</span></td> 
            <td><input type='text' class='inputText' name='description' autocomplete="off" /></td> 
        </tr> 

        <tr> 
            <td class='tright'><span class='ftitle'>Crear recurso compartido:</span></td> 
            <td><input type='checkbox' class='inputText' name='createshared' value='1' /> (podrán acceder a él los usuarios añadidos a este grupo)</td> 
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
function usedcn(cn) {
    if ( cn == '' ) {
        $('#groupempty')[0].style.display='';
        return false;
    }
    $('#groupempty')[0].style.display='none';
    $('#groupinvalid')[0].style.display='none';
    // ver si el usuario está ocupado
    $.ajax({
      type: "POST",
      url: ajaxurl,
      data: "accion=usedcn&cn="+cn,
      success: function(data) {
        if (data == 'used') {
            $('#groupnotvalid')[0].style.display='';
            $('#groupvalid')[0].style.display='none';
            return false;
        }
        else if (data == 'invalid') {
            $('#groupinvalid')[0].style.display='';
            $('#groupvalid')[0].style.display='none';
            return false;
        }
        else if (data == 'free') {
            $('#groupnotvalid')[0].style.display='none';
            $('#groupvalid')[0].style.display='';
            //alert('usuario disponible');
        }
        else {
            alert('Error servicio AJAX');
        }
      }
    });
}
-->
</script>
{/literal}
