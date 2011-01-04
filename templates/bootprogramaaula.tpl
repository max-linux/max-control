

<h3>Configurar el arranque programado del aula <u>{$aula->cn}</u></h3>
<form action='{$urlform}' method='post'> 
    <table class='dashboardTable' border='1'>
        <thead>
        <tr>
            <th class='thAction'>Acción</th>
            {foreach from=$programer->weekDays() key=k item=u}
            <th>{$u}</th>
            {/foreach}
        </tr>
        </thead>
        
        <tr>
            <td>Arrancar</td>
            {foreach from=$programer->getTimers('start') key=k item=u}
            <td>{$u}</td>
            {/foreach}
        </tr>
        <tr>
            <td>Reiniciar</td>
            {foreach from=$programer->getTimers('reboot') key=k item=u}
            <td>{$u}</td>
            {/foreach}
        </tr>
        <tr>
            <td>Parar</td>
            {foreach from=$programer->getTimers('stop') key=k item=u}
            <td>{$u}</td>
            {/foreach}
        </tr>
    </table>

{*    <table class='formTable'> 
    <tr> 
        <td class='tright'><span class="ftitle">Archivo de arranque:</span></td>
        <td> 
            <select name='boot' id='boot' > 
                <option value=''>Menú de arranque</option> 
                {foreach from=$tipos key=k item=o}
                <option value='{$k}' {if $aulaboot == $k}selected{/if}>{$o}</option>
                {/foreach}
            </select> 
            
        </td> 
    </tr> 

    <tr> 
        <td class='tright'><span class="ftitle">Reiniciar aula:</span></td>
        <td> <input type='checkbox' class='inputText' name='reboot' value='1' /></td> 
    </tr> 

    <tr> 
        <td></td> 
        <td> 
        <input class='inputButton' type='submit' name='Guardar' value="Guardar" alt="Guardar" /> 
        <input type='hidden' name='aula' value='{$aula->cn}' />
        </td> 
    </tr>

    </table> *}
    
    <div class="tright margin20">
        <input class='inputButton' type='submit' name='Guardar' value="Guardar" alt="Guardar" /> 
        <input type='hidden' name='aula' value='{$aula->cn}' />
    </div>
    </form> 

{literal}
<script type="text/javascript">
<!--

function programer(classname, ssource) {
    var obj=$('#'+ssource)[0];
    $.each($('.'+classname), function(i) { 
        $('.'+classname)[i].selectedIndex=obj.selectedIndex;
    });
}


-->
</script>
{/literal}

{*
{if $DEBUG}
{debug}
{/if}
*}
