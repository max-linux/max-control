

<h3>Desmontar imagen ISO</h3>

<form action='{$urlform}' method='post'> 
    <table class='formTable'> 
    
    <tr> 
        <td class='tright'><span class="ftitle">En el aula:</span></td>
        <td> 
            <select name='aula' id='aula' > 
                <option value=''></option> 
                {foreach from=$aulas item=a}
                    {if $a->teacher_in_aula()}
                        <option value='{$a->attr('cn')}'>{$a->attr('cn')} ({$a->get_num_computers()} equipos)</option>
                    {/if}
                {/foreach}
            </select> 
        <input class='inputButton' type='submit' name="Desmontar aula" value="Desmontar aula" alt="Desmontar aula" /> 
        </td> 
    </tr> 
    <tr>
        <td colspan='2'>Sólo se mostrarán las aulas a las que se tenga acceso</td>
    </tr>
    </table> 
</form> 

<br/><br/><br/><br/><br/><br/><br/><br/>
<hr>

<form action='{$urlform}' method='post'> 
    <table class='formTable'> 
    
    <tr> 
        <td class='tright'><span class="ftitle">En el equipo:</span></td>
        <td> 
            <select name='equipo' id='equipo' > 
                <option value=''></option> 
                {foreach from=$computers item=a}
                    {if $a->teacher_in_computer()}
                        <option value='{$a->hostname()}'>{$a->hostname()}</option>
                    {/if}
                {/foreach}
            </select> 
            <input class='inputButton' type='submit' name="Desmontar en equipo" value="Desmontar en equipo" alt="Desmontar en equipo" /> 
        </td> 
    </tr> 
    <tr>
        <td colspan='2'>Sólo se mostrarán los equipos de las aulas a las que se tenga acceso</td>
    </tr>
    </table> 
</form> 


{if $DEBUG}
{debug}
{/if}

