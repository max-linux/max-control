
<h2>Listado de grupos</h2>



<table class="bDataTable"> 
    <tr> 
        <td> 
        <form id="group" action="{$urlform}" method="post"> 
          <input type='text' name='Filter' id='Filter' value="{$filter}" /> 
          <input type='submit' name='button' value="Buscar" title="Buscar" /> 
          <input type='submit' name='button' value="Añadir grupo" title="Añadir grupo" onclick="javascript:add();" />
          <input type='hidden' id="faction" name='faction' value='search' />
        </form>
        
        </td> 
    </tr> 
</table> 


<table class='dataTable'> 
    <thead> 
      <tr>
      <th class=''>Nombre</th> 
      <th class=''>Miembros</th>
      {*<th class=''>Editar</th> *}
      <th class=''>Borrar</th> 
      </tr>
    </thead>
 
 
    <tbody> 
      {foreach from=$groups key=k item=u}
      <tr class='border' id="{$u->attr('cn')}"> 
        <td class='tcenter'><span>{$u->attr('cn')}</span></td> 
        <td class='tcenter'><span>
                        {$u->get_num_users()} 
                        <a href="{$urlmiembros}/{$u->cn}"><img src="{$baseurl}/img/edit-table.gif" alt="editar" /></a>
                        </span>
        </td> 
        {*<td class='tcenter'> 
            <a href="{$urleditar}/{$u->cn}"><img src="{$baseurl}/img/edit-table.gif" alt="editar" /></a>
        </td>*}
        <td class='tcenter'> 
            <a href="{$urlborrar}/{$u->cn}"><img src="{$baseurl}/img/delete.gif" alt="borrar" /></a>
        </td>
      </tr>
      {/foreach}

    </tbody> 
</table> 

{literal}
<script type="text/javascript">
function add() {
    $('#faction')[0].value='add';
    $('#group')[0].submit();
}
</script>
{/literal}

{*
{if $pruebas}
{debug}
{/if}
*}
