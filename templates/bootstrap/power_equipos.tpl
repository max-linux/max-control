
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Apagado o reinicio de equipos ({$pager->getMAX()})</h1>
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
                                <th>IP {$pager->getSortIcons('ipHostNumber')} / MAC {$pager->getSortIcons('macAddress')}</th> 
                                <th>Aula {$pager->getSortIcons('aula')}</th> 
                                <th>Acciones</th> 
                                <th>Encender/Reiniciar en</th> 
                                <th>Estado</th> 
                                <th>Múltiple
                                 <input title='Seleccionar todos los visibles' class="nomargin" type='checkbox' onchange="javascript:enableAll(this);"/>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                    
                    {foreach from=$equipos item=u}
                    {if $u->teacher_in_computer()}
                    <tr id="computer-{$u->hostname()}"> 
                      <td class='text-center'>{$u->cn}</td> 
                      <td class='text-center'>{$u->ipHostNumber} / {$u->macAddress}</td> 
                      <td class='text-center'>{$u->get_aula()}</td>
                      <td class='text-center'> 
                          <a href="{$urlpoweroff}/{$u->hostname()}" title="Apagar equipo {$u->hostname()}"><img src="{$baseurl}/img/poweroff.png" alt="apagar" /></a>
                          <a href="{$urlreboot}/{$u->hostname()}" title="Reiniciar equipo {$u->hostname()}"><img src="{$baseurl}/img/reboot.png" alt="reiniciar" /></a>
                          <a href="{$urlwakeonlan}/{$u->hostname()}" title="Encender {$u->hostname()}"><img src="{$baseurl}/img/poweron.png" alt="encendido de equipos" /></a>
                      </td>
                      <td class='text-center'>
                          <a href="{$urlrebootwindows}/{$u->hostname()}" title="Reiniciar equipo '{$u->hostname()}' en Windows"><img src="{$baseurl}/img/windows.png" alt="windows" /></a>
                          <a href="{$urlrebootmax}/{$u->hostname()}" title="Reiniciar equipo '{$u->hostname()}' en MAX"><img src="{$baseurl}/img/linux-logo.jpg" alt="MAX" /></a>
                      </td>
                      <td class='text-center'> 
                          <img src="{$baseurl}/status.php?hostname={$u->hostname()}&amp;rnd={$u->rnd()}" alt="calculando..." />
                      </td>
                      <td class='text-center'> 
                          <input type='checkbox' class="computeraction" name="{$u->hostname()}" id="{$u->hostname()}" onchange="javascript:oncheckboxChange();"/>
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


<form id="formactionmultiplecomputer" name="formactionmultiplecomputer" action="{$urlformmultiple}" method="post">
    <input type='hidden' name='computers' id="computers" value='' />
    <input type='hidden' name='faction' id="faction" value='' />
</form>



{literal}
<script type="text/javascript">
<!--

function oncheckboxChange() {
    var multiple=false;
    var toDelete=new Array();
    $.each($('.computeraction'), function(i) { 
        if ($('.computeraction')[i].checked) {
            toDelete.push($('.computeraction')[i].id);
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
    $.each($('.computeraction'), function(i) { 
        if ($('.computeraction')[i].checked) {
            toDelete.push($('.computeraction')[i].id);
            multiple=true;
        }
    });
    $('#computers').val(toDelete);
    $('#faction').val($('#selAction :selected').val());
    $('#formactionmultiplecomputer')[0].submit();
}

function enableAll(obj){
    $.each($('.computeraction'), function(i) { 
        $('.computeraction')[i].checked=obj.checked;
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
