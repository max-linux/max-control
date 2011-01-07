

<h3>Configurar el arranque programado del aula <u>{$aula->cn}</u></h3>
<form id='programerform' action='{$urlform}' method='post'> 
    <table class='dashboardTable' border='1'>
        <thead>
        <tr>
            <th class='thAction'>Acción</th>
            <th>Sistema Operativo</th>
            {foreach from=$programer->weekDays() key=k item=u}
            <th>{$u}</th>
            {/foreach}
        </tr>
        </thead>
        
        <tr>
            <td>Arrancar</td>
            <td>{$programer->getSO('wakeonlan', $tipos)}</td>
            {foreach from=$programer->getTimers('wakeonlan') key=k item=u}
            <td>{$u}</td>
            {/foreach}
        </tr>
        <tr>
            <td>Reiniciar</td>
            <td>{$programer->getSO('reboot', $tipos)}</td>
            {foreach from=$programer->getTimers('reboot') key=k item=u}
            <td>{$u}</td>
            {/foreach}
        </tr>
        <tr>
            <td>Parar</td>
            <td>{* poweroff no tiene select *}</td>
            {foreach from=$programer->getTimers('poweroff') key=k item=u}
            <td>{$u}</td>
            {/foreach}
        </tr>
    </table>
    
    <div class="tright margin20">
        <input class='inputButton' type='button' name='Resetear' value="Borrar" alt="Borrar" onclick="javascript:reset_form();" /> 
        <input type='hidden' name='cn' value='{$aula->cn}' />
        <input type='hidden' name='safecn' value='{$aula->safecn()}' />
        <input type='hidden' name='faction' id='faction' value='save' />
        <input class='inputButton' type='submit' name='Guardar' value="Guardar" alt="Guardar" /> 
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

function reset_form() {
    var answer = confirm("¿Está seguro de borrar la programación para este aula?")
    if (answer ==0 ) {
        return false;
    }
    $('#faction')[0].value='delete';
    var form = $("#programerform");
    form.find(':input').each(function() {
        if ( $(this).attr('type') == 'select-one' ) {
            $(this)[0].selectedIndex=0;
        }
    });
    form.submit();
}
-->
</script>
{/literal}

{*
{if $DEBUG}
{debug}
{/if}
*}
