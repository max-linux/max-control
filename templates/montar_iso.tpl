

<h3>Montar imagen ISO: <u>{$iso}</u></h3>

<form action='{$urlform}' method='post'> 
    <table class='formTable'> 
    
    <tr> 
        <td class='tright'><span class="ftitle">En el aula:</span></td>
        <td> 
            <select name='aula' id='aula' > 
                <option value=''></option> 
                {foreach from=$aulas item=a}
                <option value='{$a->attr('cn')}'>{$a->attr('cn')} ({$a->get_num_computers()} equipos)</option>
                {/foreach}
            </select> 
        <input class='inputButton' type='submit' name="Montar en el aula" value="Montar en el aula" alt="Montar en el aula" /> 
        <input type='hidden' name='iso' value='{$iso}' />
        </td> 
    </tr> 
    </table> 
</form> 

<hr>

<form action='{$urlform}' method='post'> 
    <table class='formTable'> 
    
    <tr> 
        <td class='tright'><span class="ftitle">En el equipo:</span></td>
        <td> 
            <select name='equipo' id='equipo' > 
                <option value=''></option> 
                {foreach from=$computers item=a}
                <option value='{$a->hostname()}'>{$a->hostname()}</option>
                {/foreach}
            </select> 
            <input class='inputButton' type='submit' name="Montar en el equipo" value="Montar en el equipo" alt="Montar en el equipo" /> 
            <input type='hidden' name='iso' value='{$iso}' />
        </td> 
    </tr> 


    </table> 
</form> 

{*
{if $pruebas}
{debug}
{/if}
*}
