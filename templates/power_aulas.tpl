
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
    </tr>
    </thead>
 
 
    <tbody> 
      {foreach from=$aulas item=u}
      <tr class='border' id="{$u->safecn()}"> 
        <td class='tcenter'><span>{$u->cn}</span></td> 
        <td class='tcenter'><span>{$u->get_num_computers()}</span></td>
        <td class='tcenter'> 
            {if $u->get_num_computers() > 0 }
            <a href="{$urlpoweroff}/{$u->cn}" title="Apagar aula {$u->cn}"><img src="{$baseurl}/img/poweroff.png" alt="apagar" /></a>
            <a href="{$urlreboot}/{$u->cn}" title="Reiniciar aula {$u->cn}"><img src="{$baseurl}/img/reboot.png" alt="reiniciar" /></a>
            <a href="{$urlwakeonlan}/{$u->cn}" title="WakeonLan {$u->cn}"><img src="{$baseurl}/img/poweron.png" alt="wakeonlan" /></a>
            {/if}
        </td>
      </tr>
      {/foreach}

    </tbody> 
</table> 

{*
{if $DEBUG}
{debug}
{/if}
*}
