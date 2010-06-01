

<h3>Administration del grupo  <span class='stitle'>{$group}</span></h3> 
 

 
<table> 
<thead> 
	<tr> 
		<th class="tleft">Usuarios que pertenecen al grupo</th> 
		<th></th> 
		<th class="tleft">Resto de usuarios</th> 
	</tr> 
</thead> 
<tbody> 
<tr> 
    <td rowspan="2"> 
    <form action='{$urlform}' method='post'> 
        <!-- FIXME soportar añadir y borrado multiple -->
        <select name='deluser' size='15'> 
            {foreach from=$members.ingroup item=o}
                <option value="{$o}">{$o}</option> 
            {/foreach}
        </select> 
    </td> 
 
    <td> 

    <input class='inputButton' type='image' name='delfromgroup'
            value="Quitar"
            src='{$baseurl}/img/right.gif'
            title="Quitar usuario del grupo"
            alt="Quitar usuario del grupo" /> 
    <input type="hidden" name="group" value="{$group}"/> 
    </form>



    <br /> 
    <br /> 




    <form action='{$urlform}' method='post'> 
    <input class='inputButton' type='image' name='addtogroup'
            value="Añadir usuarios al grupo"
            src='{$baseurl}/img/left.gif'
            title="Añadir usuarios al grupo"
            alt="Añadir usuario al grupo" /> 
    </td> 
 
	<td> 
        <select name='adduser' size='15'> 
            {foreach from=$members.outgroup item=o}
                <option value="{$o}">{$o}</option> 
            {/foreach}
        </select> 
		<input type="hidden" name="group" value="{$group}"> 
    </form> 
	</td> 
</tr> 
</tbody> 
</table>


{if $pruebas}
{debug}
{/if}
