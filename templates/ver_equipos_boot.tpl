
<h2>Tipo de arranque de equipos</h2>



<table class="bDataTable"> 
    <tr> 
        <td> 
        <form id="hosts" action="{$urlform}" method="post"> 
          <input type='text' name='Filter' id='Filter' value="{$filter}" /> 
          <input type='submit' name='button' value="Buscar" title="Buscar" /> 
          <input type='submit' name='button' value="Actualizar archivos PXE" title="Actualizar archivos PXE" /> 
        </form>
        </td> 
    </tr> 
</table> 


<table class='dataTable'> 
    <thead> 
    <tr>
      <th class=''>Nombre</th> 
      <th class=''>IP / MAC</th> 
      <th class=''>Arranque configurado</th> 
      <th class=''>Configurar Arranque</th> 
    </tr>
    </thead>
 
 
    <tbody> 
      {foreach from=$equipos key=k item=u}
      <tr class='border' id="{$u->hostname()}"> 
        <td class='tcenter'><span>{$u->attr('uid')}</span></td> 
        <td class='tcenter'><span>{$u->attr('ipHostNumber')} / {$u->attr('macAddress')}</span></td> 
        <td class='tcenter'><span>{$u->getBoot()}</span></td>
        {if $u->attr('macAddress') != ''}
        <td class='tcenter'> 
            <a href="{$urleditar}/{$u->hostname()}"><img src="{$baseurl}/img/reboot.png" alt="hacer backup" /></a>
        </td>
        {else}
        <td class='tcenter'>no MAC</td>
        {/if}
      </tr>
      {/foreach}

    </tbody> 
</table> 



{*
{if $DEBUG}
{debug}
{/if}
*}
