
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
      <th class=''>Nombre</th> 
      <th class=''>Profesores en este aula</th>
      <th class=''>Equipos en este aula</th>
      <th class=''>Miembros</th> 
    </thead>
 
 
    <tbody> 
      {foreach from=$aulas item=u}
      <tr class='border' id="{$u->cn}"> 
        <td class='tcenter'><span>{$u->cn}</span></td> 
        <td class='tcenter'><span>{$u->get_num_users()}</span></td> 
        <td class='tcenter'><span>{$u->get_num_computers()}</span></td>
        <td class='tcenter'> 
            <a href="{$urleditar}/{$u->cn}"><img src="{$baseurl}/img/edit-table.gif"></a>
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
