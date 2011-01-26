<h3>Añadir aula</h3>


<form action='{$urlform}' method='post' onsubmit="return checkform();"> 
    <table class='formTable'> 
        <tr> 
            <td class='tright'><span class="ftitle">Nombre del aula:</span></td> 
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
            <td></td> 
            <td> 
            <input class='inputButton' type='submit' name='add' value="Añadir" alt="Añadir" /> 
            </td> 
        </tr> 
    </table> 
</form>

<script type="text/javascript">
    var ajaxurl="{$baseurl}/index.php?ajax=1";
    var valid=false;
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
    $.ajax({
      type: "POST",
      url: ajaxurl,
      data: "accion=usedaula&cn="+cn,
      success: function(data) {
        if (data == 'used') {
            $('#groupnotvalid')[0].style.display='';
            $('#groupvalid')[0].style.display='none';
            valid=false;
        }
        else if (data == 'invalid') {
            $('#groupinvalid')[0].style.display='';
            $('#groupvalid')[0].style.display='none';
            valid=false;
        }
        else if (data == 'free') {
            $('#groupnotvalid')[0].style.display='none';
            $('#groupvalid')[0].style.display='';
            valid=true;
        }
        else {
            alert('Error servicio AJAX');
        }
      }
    });
}
function checkform() {
    return valid;
}
-->
</script>
{/literal}
