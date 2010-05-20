

<h3>{$action} equipo <u>{$hostname}</u></h3>

<form action='{$urlform}' method='post'> 
    <table class='formTable'> 
    <tr> 
        <td class='tright'><span class="ftitle">Dirección MAC:</span></td>
        <td><input type='text' class='inputText' name='macAddress' value="{$u->attr('macAddress')}"> (ejemplo 00:00:00:00:00:00)</td>
    </tr>
    
    <tr> 
        <td class='tright'><span class="ftitle">Dirección IP:</span></td>
        <td><input type='text' class='inputText' name='ipHostNumber' value="{$u->attr('ipHostNumber')}"> (ejemplo 192.168.1.23)</td>
    </tr> 
    
    <tr> 
        <td class='tright'><span class="ftitle">Archivo de arranque:</span></td>
        <td><input type='text' class='inputText' name='bootFile' value="{$u->attr('bootFile')}"> (ej: default)</td>
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
                <option value='{$o}' {if $o == $u->attr('sambaProfilePath')}selected{/if}>{$o}</option>
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

{literal}
<script type="text/javascript">
function append_sambaProfilePath() {
        var sel = document.getElementById("sambaProfilePath");
        var text = document.getElementById("new_sambaProfilePath");
        var opt = document.createElement("option");
        var txt =document.createTextNode(text.value);
        opt.appendChild(txt);
        opt.setAttribute("value",text.value);
        sel.appendChild(opt);
        sel.selectedIndex=sel.length-1;
        console.log("select index="+sel.length-1);
        text.value='';
        return false;
}
</script>
{/literal}

{if $pruebas}
{debug}
{/if}

