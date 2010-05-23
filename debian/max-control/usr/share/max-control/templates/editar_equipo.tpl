

<h3>{$action} equipo <u>{$hostname}</u></h3>

<form action='{$urlform}' method='post'> 
    <table class='formTable'> 
    <tr> 
        <td class='tright'><span class="ftitle">Dirección MAC:</span></td>
        <td><input type='text' class='inputText' name='macAddress' id='macAddress' value="{$u->attr('macAddress')}"> 
        <input class='inputButton' type='button' name='getmacbtn' value="Averiguar MAC" alt="Averiguar MAC" onclick="javascript:getmac();"/>
        (ejemplo 00:00:00:00:00:00)
        </td>
    </tr>
    
    <tr> 
        <td class='tright'><span class="ftitle">Dirección IP:</span></td>
        <td>
        <input type='text' class='inputText' name='ipHostNumber' id='ipHostNumber' value="{$u->attr('ipHostNumber')}"> 
        <input class='inputButton' type='button' name='getipbtn' value="Averiguar IP" alt="Averiguar IP" onclick="javascript:getip();"/>
        (ejemplo 192.168.1.23) 
        </td>
    </tr> 
    
    <tr> 
        <td class='tright'><span class="ftitle">Archivo de arranque:</span></td>
        <td><input type='text' class='inputText' name='bootFile' id='bootFile' value="{$u->attr('bootFile')}"> (ej: default)</td>
    </tr> 
    
    <!--<tr> 
        <td class='tright'><span class="ftitle">Parámetros de arranque:</span></td>
        <td><input type='text' class='inputText' name='bootParameter' value="{$u->attr('bootParameter')}"> (variable=valor)</td>
    </tr> -->


    <tr>
        <td class='tright'><span class='ftitle'>Grupo de arranque:</span></td> 
        <td> 
            <select name='sambaProfilePath' id='sambaProfilePath' > 
                <option value=''></option> 
                {foreach from=$aulas key=k item=o}
                <option value='{$o->attr('cn')}' {if $o->attr('cn') == $u->attr('sambaProfilePath')}selected{/if}>{$o->attr('cn')}</option>
                {/foreach}
            </select> 
            
            <!--
            <input type='text' class='inputText' name='new_sambaProfilePath' id='new_sambaProfilePath' value=""/>
            <input class='inputButton' type='button' name='añadir' value="Añadir Grupo" alt="Añadir" onclick="javascript:append_sambaProfilePath(this.value);"/> 
            -->
        </td> 
    <tr>
 
    </tr> 
    <tr> 
        <td></td> 
        <td> 
        <input class='inputButton' type='submit' name='{$action}' value="Guardar" alt="Guardar" /> 
        <input type='hidden' name='hostname' value='{$hostname}' />
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
function getip() {
    // intentar cargar IP con AJAX
    $.ajax({
      type: "POST",
      url: ajaxurl,
      data: "accion=getip&hostname="+hostname,
      success: function(data) {
        if (data != '') {
            $('#ipHostNumber')[0].value=data;
        }
        else {
            $('#ipHostNumber')[0].value='0.0.0.0';
            alert('No se pudo averiguar la dirección IP con su nombre de equipo.\n¿Está el equipo apagado?');
        }
      }
    });
}
function getmac() {
    // intentar cargar IP con AJAX
    $.ajax({
      type: "POST",
      url: ajaxurl,
      data: "accion=getmac&hostname="+hostname,
      success: function(data) {
        if (data != '') {
            $('#macAddress')[0].value=data;
        }
        else {
            // no sobreescribir
            //$('#macAddress')[0].value='00:00:00:00:00:00';
            alert('No se pudo averiguar la dirección MAC del equipo.\n¿Está el equipo apagado?');
        }
      }
    });
}
</script>
{/literal}

{*
{if $pruebas}
{debug}
{/if}
*}
