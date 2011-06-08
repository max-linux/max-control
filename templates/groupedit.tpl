

<h3>Cambiar nombre del grupo: <span class='stitle'>{$group}</span></h3> 
 
 <form action='{$urlform}' method='post' onsubmit="return checkform();"> 
    <table class='formTable'> 
        <tr> 
            <td class='tright'><span class="ftitle">Nombre del grupo:</span></td> 
            <td>
                <input type='text' class='inputText' name='cn' id='cn' autocomplete="off" maxlength='20' value='{$group}' onblur='javascript:usedcn(this.value);' />
                <span class="error" style="display:none;" id='groupnotvalid'>El identificador está ocupado</span>
                <span class="note" style="display:none;" id='groupvalid'>El identificador está libre</span>
                <span class="error" style="display:none;" id='groupempty'>El identificador no puede estar vacío</span>
                <span class="error" style="display:none;" id='groupinvalid'>Identificador no válido (letras ASCII, números o .-_)</span>
            </td> 
        </tr>
    
    <tr> 
        <td></td> 
        <td> 
        <input class='inputButton' type='submit' name='Guardar' value="Renombrar grupo" alt="Guardar" /> 
        <input type='hidden' name='group' value='{$group}' />
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
    // ver si el grupo está ocupado
    $.ajax({
      type: "POST",
      url: ajaxurl,
      data: "accion=usedcn&cn="+cn,
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
            valid=false;
        }
      }
    });
}

function checkform() {
    usedcn($('#cn')[0].value);
    return valid;
}
-->
</script>
{/literal}
