

<h3>Administración de los equipos del aula: <span class='stitle'>{$aula}</span></h3> 
 

 
<table class='dashboardTable'> 
<thead> 
	<tr> 
		<th class="tleft">Equipos del aula</th> 
		<th></th> 
		<th class="tleft">Resto de equipos (sin aula asignada)</th> 
	</tr> 
</thead> 
<tbody> 
<tr> 
    <td rowspan="2"> 
    <form action='{$urlform}' method='post'> 
        <!-- FIXME soportar añadir y borrado multiple -->
        <select name='delcomputer[]' size='15' multiple> 
            {foreach from=$equipos.ingroup item=o}
                <option value="{$o}">{$o}</option> 
            {/foreach}
        </select> 
    </td> 
 
    <td> 

    <input class='inputButton' type='image' name='delfromgroup'
            value="Quitar"
            src='{$baseurl}/img/right.gif'
            title="Quitar equipo del aula"
            alt="Quitar equipo del aula" /> 
    <input type="hidden" name="aula" value="{$aula}"/> 
    </form>



    <br /> 
    <br /> 




    <form action='{$urlform}' method='post'> 
    <input class='inputButton' type='image' name='addtogroup'
            value="Añadir usuarios al grupo"
            src='{$baseurl}/img/left.gif'
            title="Añadir equipo al aula"
            alt="Añadir equipo al aula" /> 
    </td> 
 
	<td> 
        <select name='addcomputer[]' size='15' multiple> 
            {foreach from=$equipos.outgroup item=o}
                <option value="{$o}">{$o}</option> 
            {/foreach}
        </select> 
		<input type="hidden" name="aula" value="{$aula}"> 
    </form> 
	</td> 
</tr> 
</tbody> 
</table>


{*
{if $DEBUG}
{debug}
{/if}
*}
