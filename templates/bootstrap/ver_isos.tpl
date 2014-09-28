
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Listado de imágenes ISO ({$numisos})</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>



<div class='alert alert-info'>
    El listado que aparece a continuación son las ISOS disponibles para montar 
    en los equipos de los alumnos, como si estuviéramos introduciendo un CD o DVD 
    realmente en las unidades.<br/>

    <br/>
    Seleccione el aula o equipo de la lista desplegable para montar cada imagen
     y automáticamente los alumnos comenzarán a ver dicho material en sus equipos.<br/><br/>

    Para ampliar las imágenes ISO disponibles, solo tiene que generar una imagen 
    de un DVD o CD con programas como CloneCD, y copiar el archivo ISO a la unidad <b>Y:</b>
    o desde MAX a <b>/mnt/isos</b><br/><br/>

    La próxima vez que entre en este panel, aparecerá como una imagen 
    más disponible para montar en los equipos de los alumnos.
    <br/>
    Por favor <b>no use espacios o caracteres extraños en el nombre del archivo</b>.
</div>


<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">

                <div class="row">

                    <div class="col-lg-6">
                        <form class="form-inline" id="formisos" action="{$urlform}" method="post"> 
                          <input class="form-group form-control" placeholder="Buscar" type='text' name='Filter' id='Filter' value="{$filter}" /> 
                          <input type='hidden' name='role' id="role" value='{$role}' />
                          
                          <button type="submit" class="btn btn-primary">Buscar</button>
                          <a href="{$urldesmontar}" class="btn btn-warning" >Desmontar ISO</a>
                        </form>
                    </div>
                </div>

            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class=''>Archivo</th> 
                                <th class=''>Tamaño</th> 
                                <th class=''>Volume ID</th> 
                                <th class='tleft'>Montar</th> 
                            </tr>
                        </thead>
                        <tbody>
                            
                    {foreach from=$isos key=k item=u}
                          <tr id="{$u->attr('filename')}"> 
                            <td class='center'><span>{$u->attr('filename')}</span></td> 
                            <td class='center'><span>{$u->attr('size')}</span></td> 
                            <td class='center'><span>{$u->attr('volumeid')}</span></td>
                            <td class='text-left'> 
                            
                                <div class="form-group form-inline">
                                    <select class="form-control" name='taula' id='taula' onchange="javascript:mount_iso('{$u->filename}', 'aula',this.value);"> 
                                        <option value=''>En aula</option> 
                                        {foreach from=$aulas item=a}
                                            {if $a->teacher_in_aula()}
                                                <option value='{$a->attr("cn")}'>&nbsp;&nbsp;{$a->attr('cn')} ({$a->get_num_computers()} equipos)</option>
                                            {/if}
                                        {/foreach}
                                    </select>
                                    
                                    
                                    
                                    <select class="form-control" name='tequipo' id='tequipo' onchange="javascript:mount_iso('{$u->filename}','equipo',this.value);"> 
                                        <option value=''>En equipo</option> 
                                        {foreach from=$computers item=a}
                                            {if $a->teacher_in_computer()}
                                                <option value='{$a->hostname()}'>&nbsp;&nbsp;{$a->hostname()}</option>
                                            {/if}
                                        {/foreach}
                                    </select>
                                </div>
                            </td>
                          </tr>
                    {/foreach}

                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->



            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->


{*
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
*}

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
