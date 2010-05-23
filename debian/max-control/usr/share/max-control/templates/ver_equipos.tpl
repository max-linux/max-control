
<h2>Listado de equipos</h2>



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
      <th class=''>Nombre</th> 
      <th class=''>IP / MAC</th> 
      <th class=''>Aula</th> 
      <th class=''>Editar</th> 
    </thead>
 
 
    <tbody> 
      {foreach from=$equipos key=k item=u}
      <tr class='border' id="{$u->attr('uid')}"> 
        <td class='tcenter'><span>{$u->attr('uid')}</span></td> 
        <td class='tcenter'><span>{$u->attr('ipHostNumber')} / {$u->attr('macAddress')}</span></td> 
        <td class='tcenter'><span>{$u->attr('sambaProfilePath')}</span></td>
        <td class='tcenter'> 
            <a href="{$urleditar}/{$u->hostname()}"><img src="{$baseurl}/img/edit-table.gif"></a>
        </td>
      </tr>
      {/foreach}

    </tbody> 
</table> 

{*
{if $pruebas}
{debug}
{/if}
*}
