
<h2>Tipo de arranque de equipos ({$pager->getMAX()})</h2>



<table class="bDataTable"> 
    <tr> 
        <td> 
        <form id="hosts" action="{$urlform}" method="post"> 
          <input type='text' name='Filter' id='Filter' value="{$filter}" /> 
          <input type='submit' name='button' value="Buscar" title="Buscar" /> 
          <input type='submit' name='button' value="Actualizar archivos PXE" title="Actualizar archivos PXE" /> 
          <input type='submit' name='button' value="Limpiar archivos PXE" title="Limpiar archivos PXE" /> 
        </form>
        </td> 
    </tr> 
</table> 


<table class='dataTable'> 
    <thead> 
    <tr>
      <th class=''>Nombre {$pager->getSortIcons('uid')}</th> 
      <th class=''>IP {$pager->getSortIcons('ipHostNumber')} / MAC {$pager->getSortIcons('macAddress')}</th> 
      <th class=''>Arranque configurado</th> 
      <th class=''>Configurar Arranque</th> 
    </tr>
    </thead>
 
 
    <tbody> 
      {foreach from=$equipos key=k item=u}
      <tr class='border' id="{$u->hostname()}"> 
      {if $u->teacher_in_computer()}
        <td class='tcenter'><span>{$u->attr('uid')}</span></td> 
        <td class='tcenter'><span>{$u->attr('ipHostNumber')} / {$u->attr('macAddress')}</span></td> 
        <td class='tcenter'><span>{$u->getBoot()}</span></td>
        {if $u->attr('macAddress') != ''}
        <td class='tcenter'> 
            <a href="{$urleditar}/{$u->hostname()}"><img src="{$baseurl}/img/reboot.png" title="Configurar arranque" /></a>
        </td>
        {else}
        <td class='tcenter'>no MAC</td>
        {/if}
      </tr>
      {/if}
      {/foreach}

    </tbody> 
</table> 


<!-- paginador -->
{if $pager}
{$pager->getHTML()}
{/if}

{*
{if $DEBUG}
{debug}
{/if}
*}
