
<h2>Listado de aulas</h2>



<table class="bDataTable"> 
    <tr> 
        <td> 
        <form id="aulas" action="{$urlform}" method="post"> 
          <input type='text' name='Filter' id='Filter' value="{$filter}" /> 
          <input type='submit' name='button' value="Buscar" title="Buscar" /> 
          <input type='submit' name='button' value="Nueva aula" title="Nueva aula" onclick="javascript:newaula();" />
          <input type='hidden' id="faction" name='faction' value='search' />
        </form>
        </td> 
    </tr> 
</table> 


<table class='dataTable'> 
    <thead> 
     <tr>
      <th class=''>Nombre</th> 
      <th class=''>Profesores en este aula</th>
      <th class=''>Equipos en este aula</th>
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
      </tr>
      {/foreach}

    </tbody> 
</table> 

{literal}
<script type="text/javascript">
function newaula() {
    $('#faction')[0].value='nueva';
    $('#aulas')[0].submit();
}
</script>
{/literal}


{*
{if $pruebas}
{debug}
{/if}
*}
