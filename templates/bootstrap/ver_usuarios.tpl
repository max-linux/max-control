
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Listado de usuarios ({$pager->getMAX()})</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>


{if $overQuotaEnabled}
<div id="overQuotaDiv" class="alert alert-danger alert-dismissable" >
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    Los siguientes usuarios han superado el {$overQuotaLimit}% de la cuota máxima:
    <ul>
    {foreach from=$overQuota key=k item=u}
        <li>
            <a href="{$urleditar}/{$k}">{$k}</a>
            Usado {$u.size} MB de {$u.maxsize} MB ({$u.percent})
            <a href="{$resetprofilebase}/{$k}"><img src="{$baseurl}/img/delete.gif" alt="borrar" /></a>
        </li>
    {/foreach}
    </ul>
</div>
{/if}



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
                          <a href="{$urladd}" class="btn btn-warning" >Añadir usuario</a>
                        </form>


                    </div>
                    <div class="col-lg-6 text-right pull-right" >
                        <select class="form-group form-control" style="display:none;" name='selAction' id='selAction' onchange="javascript:actionSelected();">
                            <option value=''>Seleccionar acción...</option>
                            <option value='delete'>&nbsp;&nbsp;&nbsp;&nbsp;Borrar seleccionados</option>
                            <option value='clean'>&nbsp;&nbsp;&nbsp;&nbsp;Limpiar perfil</option>
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
                                <th>Identificador {$pager->getSortIcons('cn')}</th>
                                <th class="hidden-xs">Nombre {$pager->getSortIcons('givenname')} Apellidos {$pager->getSortIcons('sn')}</th>
                                <th class="form-group form-inline">Rol
                                    <select class="form-control" name='selectrole' id='selectrole' onchange="javascript:rolFilter(this);">
                                        <option value='' {if $role == ''}selected="selected"{/if}>----------</option>
                                        <option value='alumno' {if $role == 'alumno'}selected="selected"{/if}>Alumno</option> 
                                        <option value='teacher' {if $role == 'teacher'}selected="selected"{/if}>Profesor</option> 
                                        <option value='tic' {if $role == 'tic'}selected="selected"{/if}>Coordinador TIC</option> 
                                        <option value='admin' {if $role == 'admin'}selected="selected"{/if}>Administrador</option> 
                                    </select>
                                </th>
                                <th class="hidden-xs">{if $quotaTime != ''}
                                    <acronym title="Cache de cuota generado el: '{$quotaTime}'">Cuota</acronym>
                                    {else}
                                    Cuota
                                     {/if}
                                    {$pager->getSortIcons('usedSize')}
                                </th>
                                
                                <th>
                                    Acciones <input title='Seleccionar todos los visibles' class="nomargin" type='checkbox' onchange="javascript:enableAll(this);"/>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            
                    {foreach from=$usuarios key=k item=u}
                        <tr>
                            <td>
                                {if $u->attr('description') != ''}
                                    <acronym title='{$u->attr('description')}'><span>{$u->attr('cn')} {if !$u->is_romaing()}<img src="{$baseurl}/img/msg.png" title="Perfil sin roaming" />{/if}</span></acronym>
                                {else}
                                    <span>{$u->attr('cn')} {if !$u->is_romaing()}<img src="{$baseurl}/img/msg.png" title="Perfil sin roaming" />{/if} </span>
                                {/if}

                                <div class="btn-group pull-right">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle btn btn-info" data-toggle="dropdown">
                                        <i class="fa fa-chevron-down"></i>
                                    </button>
                                    <ul class="dropdown-menu slidedown" data-data='{$u|@json_encode}'>
                                        <li><a href="{$urleditar}/{$u->attr('cn')}"><i class="fa fa-edit fa-fw"></i> Editar</a></li>
                                        <li><a href="{$resetprofilebase}/{$u->attr('cn')}"><i class="fa fa-refresh fa-fw"></i>Limpiar perfil</a></li>
                                        <li><a href="{$urlformmultiple}?faction=delete&amp;usernames={$u->attr('cn')}"><i class="fa fa-trash-o fa-fw"></i> Borrar</a></li>
                                    </ul>
                                </div>
                            </td>
                            <td class="hidden-xs">{$u->attr('givenname')} {$u->attr('sn')}</td>
                            <td>
                                {if $u->get_role() == 'teacher'}Profesor{/if}
                                {if $u->get_role() == 'tic'}Coordinador TIC{/if}
                                {if $u->get_role() == 'admin'}Administrador{/if}
                                {if $u->get_role() == ''}Alumno{/if}
                            </td>
                            <td class="hidden-xs">{$u->getquota()}</td>
                            
                            <td>
                                <input type='checkbox' class="userdel" name="{$u->attr('cn')}" id="{$u->attr('cn')}" onchange="javascript:oncheckboxChange();"/>
                            </td>

                        </tr>
                    {/foreach}

                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->

                {if $pager->needPager() }
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


<form id="formdeletemultipleuser" name="formdeletemultipleuser" action="{$urlformmultiple}" method="post">
    <input type='hidden' name='usernames' id="usernames" value='' />
    <input type='hidden' name='faction' id="faction" value='' />
</form>



{literal}
<script type="text/javascript">
<!--

function oncheckboxChange() {
    var multiple=false;
    var toDelete=new Array();
    $.each($('.userdel'), function(i) { 
        if ($('.userdel')[i].checked) {
            toDelete.push($('.userdel')[i].id);
            multiple=true;
        }
    });
    if(multiple)
        $('#selAction').show();
    else
        $('#selAction').hide();
}

function enableAll(obj){
    $.each($('.userdel'), function(i) { 
        $('.userdel')[i].checked=obj.checked;
    });
    if(obj.checked)
        $('#selAction').show();
    else
        $('#selAction').hide();
}

function rolFilter(obj) {
    $('#role')[0].value=obj.value;
    document.forms.formuser.submit();
}

function actionSelected(){
    
    var toDelete=new Array();
    $.each($('.userdel'), function(i) { 
        if ($('.userdel')[i].checked) {
            toDelete.push($('.userdel')[i].id);
            multiple=true;
        }
    });
    $('#usernames')[0].value=toDelete;
    
    var faction = $('#selAction').val();
    $('#faction')[0].value=faction;
    $('#formdeletemultipleuser')[0].submit();
}
-->
</script>
{/literal}

