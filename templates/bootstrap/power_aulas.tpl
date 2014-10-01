
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Apagado o reinicio de aulas ({$pager->getMAX()})</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>



<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">

                <div class="row">

                    <div class="col-lg-6">
                        <form class="form-inline" id="formuser" action="{$urlform}" method="post"> 
                          <input class="form-group form-control" placeholder="Buscar" type='text' name='Filter' id='Filter' value="{$filter}" /> 
                          <input type='hidden' name='role' id="role" value='{$role}' />
                          
                          <button type="submit" class="btn btn-primary">Buscar</button>
                          
                        </form>


                    </div>
                    <div class="col-lg-6 text-right pull-right" >
                        <select class="form-control" style="display:none;" name='selAction' id='selAction' onchange="javascript:actionSelected();">
                            <option value=''>Seleccionar acción...</option>
                            {foreach from=$multiple_actions key=k item=u}
                            <option value='{$k}'>&nbsp;&nbsp;&nbsp;&nbsp;{$u}</option>
                            {/foreach}
                        </select>
                    </div>

                </div>

            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Nombre {$pager->getSortIcons('cn')}</th> 
                                <th class="hidden-xs">Equipos en este aula {$pager->getSortIcons('cachednumcomputers')}</th>
                                <th>Acciones</th> 
                                <th>Encender/Reiniciar en</th> 
                                <th>Múltiple
                                <input title='Seleccionar todos los visibles' class="nomargin" type='checkbox' onchange="javascript:enableAll(this);"/>
                                  </th>
                            </tr>
                        </thead>
                        <tbody>
                    
                    {foreach from=$aulas item=u}
                      <tr id="{$u->safecn()}"> 
                      {if $u->teacher_in_aula()}
                        <td class='text-center'><span>{$u->cn}</span></td> 
                        <td class='tex-center hidden-xs'><span>{$u->get_num_computers()}</span></td>
                        <td class='text-center'> 
                            {if $u->get_num_computers() > 0 }
                            <a href="{$urlpoweroff}/{$u->cn}" title="Apagar aula '{$u->cn}'"><img src="{$baseurl}/img/poweroff.png" alt="apagar" /></a>
                            <a href="{$urlreboot}/{$u->cn}" title="Reiniciar aula '{$u->cn}'"><img src="{$baseurl}/img/reboot.png" alt="reiniciar" /></a>
                            <a href="{$urlwakeonlan}/{$u->cn}" title="Encender '{$u->cn}'"><img src="{$baseurl}/img/poweron.png" alt="encendido de equipos" /></a>
                            {/if}
                        </td>
                        <td class='text-center'>
                            {if $u->get_num_computers() > 0 }
                            <a href="{$urlrebootwindows}/{$u->cn}" title="Reiniciar aula '{$u->cn}' en Windows"><img src="{$baseurl}/img/windows.png" alt="windows" /></a>
                            <a href="{$urlrebootmax}/{$u->cn}" title="Reiniciar aula '{$u->cn}' en MAX"><img src="{$baseurl}/img/linux-logo.jpg" alt="MAX" /></a>
                            {/if}
                        </td>
                        <td class='text-center'> 
                            {if $u->get_num_computers() > 0 }
                            <input type='checkbox' class="aulaaction" name="{$u->cn}" id="{$u->cn}" onchange="javascript:oncheckboxChange();"/>
                            {else}
                            aula vacía
                            {/if}
                        </td>
                      </tr>
                      {/if}
                      {/foreach}

                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->

                {if $pager->needPager()}
                    <div class="well">
                    {$pager->getHTML()}
                    </div>
                {/if}

            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->


<form id="formactionmultipleaulas" name="formactionmultipleaulas" action="{$urlformmultiple}" method="post">
    <input type='hidden' name='aulas' id="aulas" value='' />
    <input type='hidden' name='faction' id="faction" value='' />
</form>

{*
<table class="bDataTable"> 
    <tr> 
        <td> 
        <form action="{$urlform}" method="post"> 
          <input type='text' name='Filter' id='Filter' value="{$filter}" /> 
          <input type='submit' name='button' value="Buscar" title="Buscar" /> 
          
          <select style="display:none;float:right;" name='selAction' id='selAction' onchange="javascript:actionSelected();">
            <option value=''>Seleccionar acción...</option>
            {foreach from=$multiple_actions key=k item=u}
            <option value='{$k}'>&nbsp;&nbsp;&nbsp;&nbsp;{$u}</option>
            {/foreach}
          </select>
          
        </form>
        </td> 
    </tr> 
</table> 


<table class='dataTable'> 
    <thead> 
    <tr>
      <th class=''>Nombre {$pager->getSortIcons('cn')}</th> 
      <th class=''>Equipos en este aula {$pager->getSortIcons('cachednumcomputers')}</th>
      <th class=''>Acciones</th> 
      <th class=''>Encender/Reiniciar en</th> 
      <th class=''>Múltiple
       <input title='Seleccionar todos los visibles' class="nomargin" type='checkbox' onchange="javascript:enableAll(this);"/>
      </th>
    </tr>
    </thead>
 
 
    <tbody> 
      {foreach from=$aulas item=u}
      <tr class='border' id="{$u->safecn()}"> 
      {if $u->teacher_in_aula()}
        <td class='tcenter'><span>{$u->cn}</span></td> 
        <td class='tcenter'><span>{$u->get_num_computers()}</span></td>
        <td class='tcenter'> 
            {if $u->get_num_computers() > 0 }
            <a href="{$urlpoweroff}/{$u->cn}" title="Apagar aula '{$u->cn}'"><img src="{$baseurl}/img/poweroff.png" alt="apagar" /></a>
            <a href="{$urlreboot}/{$u->cn}" title="Reiniciar aula '{$u->cn}'"><img src="{$baseurl}/img/reboot.png" alt="reiniciar" /></a>
            <a href="{$urlwakeonlan}/{$u->cn}" title="Encender '{$u->cn}'"><img src="{$baseurl}/img/poweron.png" alt="encendido de equipos" /></a>
            {/if}
        </td>
        <td class='tcenter'>
            {if $u->get_num_computers() > 0 }
            <a href="{$urlrebootwindows}/{$u->cn}" title="Reiniciar aula '{$u->cn}' en Windows"><img src="{$baseurl}/img/windows.png" alt="windows" /></a>
            <a href="{$urlrebootmax}/{$u->cn}" title="Reiniciar aula '{$u->cn}' en MAX"><img src="{$baseurl}/img/linux-logo.jpg" alt="MAX" /></a>
            {/if}
        </td>
        <td class='tcenter'> 
            {if $u->get_num_computers() > 0 }
            <input type='checkbox' class="aulaaction" name="{$u->cn}" id="{$u->cn}" onchange="javascript:oncheckboxChange();"/>
            {else}
            aula vacía
            {/if}
        </td>
      </tr>
      {/if}
      {/foreach}

    </tbody> 
</table> 
*}


{literal}
<script type="text/javascript">
<!--

function oncheckboxChange() {
    var multiple=false;
    var toDelete=new Array();
    $.each($('.aulaaction'), function(i) { 
        if ($('.aulaaction')[i].checked) {
            toDelete.push($('.aulaaction')[i].id);
            multiple=true;
        }
    });
    if(multiple)
        $('#selAction').show();
    else
        $('#selAction').hide();
}

function actionSelected(){
    var toDelete=new Array();
    $.each($('.aulaaction'), function(i) { 
        if ($('.aulaaction')[i].checked) {
            toDelete.push($('.aulaaction')[i].id);
            multiple=true;
        }
    });
    $('#aulas').val(toDelete);
    $('#faction').val( $('#selAction :selected').val() );
    $('#formactionmultipleaulas')[0].submit();
}

function enableAll(obj){
    $.each($('.aulaaction'), function(i) { 
        $('.aulaaction')[i].checked=obj.checked;
    });
    if(obj.checked)
        $('#selAction').show();
    else
        $('#selAction').hide();
}

function rolFilter(obj) {
    $('#role').val(obj.value);
    document.forms.formuser.submit();
}
-->
</script>
{/literal}



{*
{if $DEBUG}
{debug}
{/if}
*}
