

<h3>Configurar el arranque del equipo <u>{$aula->cn}</u></h3>
<form action='{$urlform}' method='post'> 
    <table class='formTable'> 

    
    <tr> 
        <td class='tright'><span class="ftitle">Archivo de arranque:</span></td>
        <td> 
            <select name='boot' id='boot' > 
                <option value=''></option> 
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

    </table> 
    </form> 


{*
{if $DEBUG}
{debug}
{/if}
*}
