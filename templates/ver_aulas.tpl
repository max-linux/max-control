
<h2>Listado de aulas ({$pager->getMAX()})</h2>



<table class="bDataTable"> 
    <tr> 
        <td> 
        <form id="aulas" action="{$urlform}" method="post"> 
          <input type='text' name='Filter' id='Filter' value="{$filter}" /> 
          <input type='submit' name='button' value="Buscar" title="Buscar" /> 
          {if $mode == 'admin'}
          <input type='submit' name='button' value="Nueva aula" title="Nueva aula" />
          {/if}
        </form>
        </td> 
    </tr> 
</table> 


<table class='dataTable'> 
    <thead> 
     <tr>
      <th class=''>Nombre {$pager->getSortIcons('cn')}</th> 
      <th class=''>Profesores en este aula</th>
      {if $mode == 'admin'}
      <th class=''>Equipos en este aula</th>
      <th class=''>Borrar</th> 
      {/if}
     </tr>
    </thead>
 
 
    <tbody> 
      {foreach from=$aulas item=u}
      <tr class='border' id="{$u->safecn()}"> 
        {if $u->attr('description') != ''}
        <td class='tcenter'><acronym title='{$u->attr('description')}'><span>{$u->cn}</span></acronym></td>
        {else}
        <td class='tcenter'><span>{$u->cn}</span></td>
        {/if}
         
        <td class='tcenter'><span>
                        {$u->get_num_users()} 
                        <a href="{$urlprofesores}/{$u->cn}"><img src="{$baseurl}/img/edit-table.gif" alt="editar" /></a>
                        </span>
        </td> 
        {if $mode == 'admin'}
        <td class='tcenter'><span>
                        {$u->get_num_computers()} 
                        <a href="{$urlequipos}/{$u->cn}"><img src="{$baseurl}/img/edit-table.gif" alt="editar" /></a>
                        </span>
        
        </td>
        <td class='tcenter'> 
            <a href="{$urlborrar}/{$u->cn}"><img src="{$baseurl}/img/delete.gif" alt="borrar" /></a>
        </td>
        {/if}
      </tr>
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
