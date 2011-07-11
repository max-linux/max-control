
<h2>Listado de imágenes ISO ({$numisos})</h2>

<div class='help'>
El listado que aparece a continuación son las ISOS disponibles para montar 
en los equipos de los alumnos, como si estuviéramos introduciendo un CD o DVD 
realmente en las unidades.<br/>

<br/>
Seleccione el aula o equipo de la lista desplegable para montar cada imagen
 y automáticamente los alumnos comenzarán a ver dicho material en sus estaciones.<br/><br/>

Para ampliar las imágenes ISO disponibles, solo tiene que generar una imagen 
de un DVD o CD con programas como CloneCD, y copiar el archivo ISO a la unidad <b>Y:</b>
o desde MaX a <b>/mnt/isos</b><br/><br/>

La próxima vez que entre en este panel, aparecerá como una imagen 
más disponible para montar en los equipos de los alumnos.
<br/>
Por favor <b>no use espacios o caracteres extraños en el nombre del archivo</b>.
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
      <th class='tleft'>Montar</th> 
    </tr>
    </thead>

    {if $numisos > 0}
    <tbody> 
      {foreach from=$isos key=k item=u}
      <tr class='border' id="{$u->attr('filename')}"> 
        <td class='tcenter'><span>{$u->attr('filename')}</span></td> 
        <td class='tcenter'><span>{$u->attr('size')}</span></td> 
        <td class='tcenter'><span>{$u->attr('volumeid')}</span></td>
        <td class='tleft'> 
        
            <select name='taula' id='taula' onchange="javascript:mount_iso('{$u->filename}', 'aula',this.value);"> 
                <option value=''>En aula</option> 
                {foreach from=$aulas item=a}
                    {if $a->teacher_in_aula()}
                        <option value='{$a->attr('cn')}'>{$a->attr('cn')} ({$a->get_num_computers()} equipos)</option>
                    {/if}
                {/foreach}
            </select>
            
            <br/><br/>
            
            <select name='tequipo' id='tequipo' onchange="javascript:mount_iso('{$u->filename}','equipo',this.value);"> 
                <option value=''>En equipo</option> 
                {foreach from=$computers item=a}
                    {if $a->teacher_in_computer()}
                        <option value='{$a->hostname()}'>{$a->hostname()}</option>
                    {/if}
                {/foreach}
            </select>
        </td>
      </tr>
      {/foreach}

    </tbody> 
    {/if}
</table> 

<form id="formiso" name="formiso" action='{$urlmount}' method='post'>
    <input type='hidden' name='iso' id='iso' value='' />
    <input type='hidden' name='equipo' id='equipo' value='' />
    <input type='hidden' name='aula' id='aula' value='' />
</form>

{literal}
<script type="text/javascript">
<!--
function mount_iso(iso, what, where) {
    if( iso == '')
        return;
    
    $('#iso')[0].value=iso;
    if( what == 'equipo')
        $('#equipo')[0].value=where;
    else
        $('#aula')[0].value=where;
    $('#formiso')[0].submit();
}


-->
</script>
{/literal}


{*
{if $DEBUG}
{debug}
{/if}
*}
