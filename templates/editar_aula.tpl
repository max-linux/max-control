

<h3>Administración de los profesores del aula: <span class='stitle'>{$aula}</span></h3> 
 

 
<table class='dashboardTable'> 
<thead> 
	<tr> 
		<th class="tleft">Profesores del aula</th> 
		<th></th> 
		<th class="tleft">Resto de profesores</th> 
	</tr> 
</thead> 
<tbody> 
<tr> 
    <td rowspan="2"> 
    <form action='{$urlform}' method='post'>
        <!-- FIXME soportar añadir y borrado multiple -->
        <select name='deluser[]' size='15' multiple> 
            {foreach from=$miembros.ingroup item=o}
                <option value="{$o}">{$o}</option> 
            {/foreach}
        </select> 
    </td> 
 
    <td> 

    <input class='inputButton' type='image' name='delfromgroup'
            value="Quitar"
            src='{$baseurl}/img/right.gif'
            title="Quitar profesor del aula"
            alt="Quitar profesor del aula" /> 
    <input type="hidden" name="aula" value="{$aula}"/> 
    </form>



    <br /> 
    <br /> 




    <form action='{$urlform}' method='post'> 
    <input class='inputButton' type='image' name='addtogroup'
            value="Añadir usuarios al grupo"
            src='{$baseurl}/img/left.gif'
            title="Añadir profesor al aula"
            alt="Añadir profesor al aula" /> 
    </td> 
 
	<td> 
        <select name='adduser[]' size='15' multiple> 
            {foreach from=$miembros.outgroup item=o}
                <option value="{$o}">{$o}</option> 
            {/foreach}
        </select> 
		<input type="hidden" name="aula" value="{$aula}"> 
    </form> 
	</td> 
</tr> 
</tbody> 
</table>


{if $DEBUG}
{debug}
{/if}
