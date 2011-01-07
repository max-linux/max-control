
<h2>Listado de imágenes ISO ({$numisos})</h2>

<div class='help'>
El listado que aparece a continuación son las ISOS disponibles para montar 
en los equipos de los alumnos, como si estuviéramos introduciendo un CD o DVD 
realmente en las unidades.<br/>

Pulse sobre el icono montar y automáticamente los alumnos comenzarán a ver 
dicho material en sus estaciones.<br/><br/>

Para ampliar las imágenes ISO disponibles, solo tiene que generar una imagen 
de un DVD o CD con programas como CloneCD, y copiar el archivo ISO a la unidad <b>Y:</b>
o desde MaX desde <b>/mnt/isos</b><br/><br/>

La próxima vez que entre en este panel, aparecerá como una imagen 
más disponible para montar en los equipos de los alumnos.

Por favor no use espacios o caracteres extraños en el nombre del archivo.
</div>


<table class="bDataTable"> 
    <tr> 
        <td> 
        <form id="hosts" action="{$urlform}" method="post"> 
          <input type='text' name='Filter' id='Filter' value="{$filter}" /> 
          <input type='submit' name='button' value="Buscar" title="Buscar" /> 
          <input type='submit' name='button' value="Desmontar ISO" title="Desmontar ISO" />
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

    {if $numisos > 0}
    <tbody> 
      {foreach from=$isos key=k item=u}
      <tr class='border' id="{$u->attr('filename')}"> 
        <td class='tcenter'><span>{$u->attr('filename')}</span></td> 
        <td class='tcenter'><span>{$u->attr('size')}</span></td> 
        <td class='tcenter'><span>{$u->attr('volumeid')}</span></td>
        <td class='tcenter'> 
            <a href="{$urlmontar}/{$u->attr('filename')}"><img src="{$baseurl}/img/edit-table.gif" alt="montar" title='Montar ISO' /></a>
        </td>
      </tr>
      {/foreach}

    </tbody> 
    {/if}
</table> 

{*
{if $DEBUG}
{debug}
{/if}
*}
