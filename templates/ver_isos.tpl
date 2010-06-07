
<h2>Listado de imágenes ISO</h2>



<table class="bDataTable"> 
    <tr> 
        <td> 
        <form id="hosts" action="{$urlform}" method="post"> 
          <input type='text' name='Filter' id='Filter' value="{$filter}" /> 
          <input type='submit' name='button' value="Buscar" title="Buscar" /> 
          
        </form>
        </td> 
    </tr> 
</table> 


<table class='dataTable'> 
    <thead> 
    <tr>
      <th class=''>Archivo</th> 
      <th class=''>Tamaño</th> 
      <th class=''>Volume ID</th> 
      <th class=''>Montar</th> 
    </tr>
    </thead>

    <tbody> 
      {foreach from=$isos key=k item=u}
      <tr class='border' id="{$u->attr('filename')}"> 
        <td class='tcenter'><span>{$u->attr('filename')}</span></td> 
        <td class='tcenter'><span>{$u->attr('size')}</span></td> 
        <td class='tcenter'><span>{$u->attr('volumeid')}</span></td>
        <td class='tcenter'> 
            <a href="{$urlmontar}/{$u->attr('filename')}"><img src="{$baseurl}/img/edit-table.gif" alt="montar" /></a>
        </td>
      </tr>
      {/foreach}

    </tbody> 
</table> 

<note>Es necesario que los archivos no tengan espacios ni caracteres extraños.<br/>
Puede copiarlos a través de su unidad I: (en Windows) o en MAX desde /mnt/isos</note>

{*
{if $pruebas}
{debug}
{/if}
*}
