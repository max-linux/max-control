
<h2>Apagado o reinicio de aulas</h2>



<table class="bDataTable"> 
    <tr> 
        <td> 
        <form action="{$urlform}" method="post"> 
          <input type='text' name='Filter' id='Filter' value="{$filter}" /> 
          <input type='submit' name='button' value="Buscar" title="Buscar" /> 
        </form>
        </td> 
    </tr> 
</table> 


<table class='dataTable'> 
    <thead> 
    <tr>
      <th class=''>Nombre</th> 
      <th class=''>Equipos en este aula</th>
      <th class=''>Acciones</th> 
      <th class=''>Encender/Reiniciar en</th> 
    </tr>
    </thead>
 
 
    <tbody> 
      {foreach from=$aulas item=u}
      <tr class='border' id="{$u->safecn()}"> 
      {if $u->teacher_in_aula()}
        <td class='tcenter'><span>{$u->cn}</span></td> 
        <td class='tcenter'><span>{$u->get_num_computers()}</span></td>
        <td class='tcenter'> 
            {if $u->get_num_computers() > 0 }
            <a href="{$urlpoweroff}/{$u->cn}" title="Apagar aula '{$u->cn}'"><img src="{$baseurl}/img/poweroff.png" alt="apagar" /></a>
            <a href="{$urlreboot}/{$u->cn}" title="Reiniciar aula '{$u->cn}'"><img src="{$baseurl}/img/reboot.png" alt="reiniciar" /></a>
            <a href="{$urlwakeonlan}/{$u->cn}" title="Enecender '{$u->cn}'"><img src="{$baseurl}/img/poweron.png" alt="encendido de equipos" /></a>
            {/if}
        </td>
        <td class='tcenter'>
            {if $u->get_num_computers() > 0 }
            <a href="{$urlrebootwindows}/{$u->cn}" title="Reiniciar aula '{$u->cn}' en Windows"><img src="{$baseurl}/img/windows-logo.jpg" alt="windows" /></a>
            <a href="{$urlrebootmax}/{$u->cn}" title="Reiniciar aula '{$u->cn}' en MAX"><img src="{$baseurl}/img/linux-logo.jpg" alt="MAX" /></a>
            <a href="{$urlbackharddi}/{$u->cn}" title="Reiniciar equipo '{$u->cn}' en Backharddi-NG"><img src="{$baseurl}/img/backharddi-logo.jpg" alt="Backharddi-NG" /></a>
            {/if}
        </td>
      </tr>
      {/if}
      {/foreach}

    </tbody> 
</table> 

{*
{if $DEBUG}
{debug}
{/if}
*}
