
<h2>Listado de aulas ({$pager->getMAX()})</h2>



<table class="bDataTable"> 
    <tr> 
        <td> 
        <form id="aulas" action="{$urlform}" method="post"> 
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
      <th class=''>Nombre {$pager->getSortIcons('cn')}</th> 
      <th class=''>Arranque por defecto {$pager->getSortIcons('cachedBoot')}</th>
      <th class=''>Cambiar</th>
      <th class=''>Programar</th>
     </tr>
    </thead>
 
 
    <tbody> 
      {foreach from=$aulas item=u}
      <tr class='border' id="{$u->safecn()}"> 
      {if $u->teacher_in_aula()}
        <td class='tcenter'><span>{$u->cn}</span></td> 
        <td class='tcenter'><span>{$u->cachedBoot}</span></td>
        <td class='tcenter'>
            <span>
            <a href="{$urleditar}/{$u->cn}"><img src="{$baseurl}/img/edit-table.gif" alt="editar" /></a>
            </span>
        </td>
        <td class='tcenter'>
            <span>
            {if $u->get_num_computers() > 0 }
                {if $programer->isProgramed($u->safecn()) }
                <a href="{$urlprogramar}/{$u->cn}"><img src="{$baseurl}/img/tempor.png" alt="programar" /></a>
                {else}
                <a href="{$urlprogramar}/{$u->cn}"><img src="{$baseurl}/img/add.gif" alt="programar" /></a>
                {/if}
            {else}
                aula vacía
            {/if}
            </span>
        </td>
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
