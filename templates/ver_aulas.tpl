
<h2>Listado de aulas ({$numaulas})</h2>



<table class="bDataTable"> 
    <tr> 
        <td> 
        <form id="aulas" action="{$urlform}" method="post"> 
          <input type='text' name='Filter' id='Filter' value="{$filter}" /> 
          <input type='submit' name='button' value="Buscar" title="Buscar" /> 
          <input type='submit' name='button' value="Nueva aula" title="Nueva aula" />
        </form>
        </td> 
    </tr> 
</table> 


<table class='dataTable'> 
    <thead> 
     <tr>
      <th class=''>Nombre {$pager->getSortIcons('cn')}</th> 
      <th class=''>Profesores en este aula</th>
      <th class=''>Equipos en este aula</th>
      <th class=''>Borrar</th> 
     </tr>
    </thead>
 
 
    <tbody> 
      {foreach from=$aulas item=u}
      <tr class='border' id="{$u->safecn()}"> 
        <td class='tcenter'><span>{$u->cn}</span></td> 
        <td class='tcenter'><span>
                        {$u->get_num_users()} 
                        <a href="{$urlprofesores}/{$u->cn}"><img src="{$baseurl}/img/edit-table.gif" alt="editar" /></a>
                        </span>
        </td> 
        <td class='tcenter'><span>
                        {$u->get_num_computers()} 
                        <a href="{$urlequipos}/{$u->cn}"><img src="{$baseurl}/img/edit-table.gif" alt="editar" /></a>
                        </span>
        
        </td>
        <td class='tcenter'> 
            <a href="{$urlborrar}/{$u->cn}"><img src="{$baseurl}/img/delete.gif" alt="borrar" /></a>
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
