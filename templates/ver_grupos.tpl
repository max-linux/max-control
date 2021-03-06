
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Listado de grupos ({$pager->getMAX()})</h1>
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
                          <a href="{$urladd}" class="btn btn-warning" >Añadir grupo</a>

                          
                        </form>


                    </div>
                    <div class="col-lg-6 text-right pull-right" >
                        <select class="form-group form-control" style="display:none;" name='selAction' id='selAction' onchange="javascript:actionSelected();">
                            <option value=''>Seleccionar acción...</option>
                            <option value='delete'>&nbsp;&nbsp;&nbsp;&nbsp;Borrar grupos</option>
                            <option value='deletemembers'>&nbsp;&nbsp;&nbsp;&nbsp;Borrar grupos y sus miembros</option>
                            <option value='clean'>&nbsp;&nbsp;&nbsp;&nbsp;Limpiar perfiles de usuarios de los grupos</option>
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
                                <th>Miembros {$pager->getSortIcons('numUsers')}</th>
                                <th>Acciones <input title='Seleccionar todos los visibles' class="nomargin" type='checkbox' onchange="javascript:enableAll(this);"/></th>
                            </tr>
                        </thead>
                        <tbody>
                    
                    {foreach from=$groups key=k item=u}
                      <tr id="group-{$u->cn}"> 
                        <td>
                            {if $u->description != ''}
                                <acronym title='{$u->description}'>{$u->cn}</acronym>
                            {else}
                                <span>{$u->cn}</span>
                            {/if}


                            <div class="btn-group pull-right">
                                <button type="button" class="btn btn-default btn-xs dropdown-toggle btn btn-info" data-toggle="dropdown">
                                    <i class="fa fa-chevron-down"></i>
                                </button>
                                <ul class="dropdown-menu slidedown" data-data='{$u|@json_encode}'>
                                    <li><a href="{$urlmiembros}/{$u->cn}"><i class="fa fa-users fa-fw"></i> Miembros</a></li>
                                    <li class="divider"></li>
                                    <li><a href="{$urleditar}/{$u->cn}"><i class="fa fa-edit fa-fw"></i> Editar</a></li>
                                    <li><a href="{$urlborrar}?faction=delete&amp;groupnames={$u->cn}"><i class="fa fa-trash-o fa-fw"></i> Borrar</a></li>
                                    
                                </ul>
                            </div>
                        </td> 
                        
                        <td>{$u->numUsers}</td> 
                        <td class='tcenter'> 
                            
                            
                            <input type='checkbox' class="groupdel" name="{$u->cn}" id="{$u->cn}" onchange="javascript:oncheckboxChange();"/>
                        </td>
                      </tr>
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

<form id="formdeletemultiplegroup" name="formdeletemultiplegroup" action="{$urlborrar}" method="post">
    <input type='hidden' name='groupnames' id="groupnames" value='' />
    <input type='hidden' name='faction' id="faction" value='' />
</form>



{literal}
<script type="text/javascript">
<!--

function oncheckboxChange() {
    var multiple=false;
    var toDelete=new Array();
    $.each($('.groupdel'), function(i) { 
        if ($('.groupdel')[i].checked) {
            toDelete.push($('.groupdel')[i].id);
            multiple=true;
        }
    });
    if(multiple)
        $('#selAction').show();
    else
        $('#selAction').hide();
}

function deleteSelected(){
    var toDelete=new Array();
    $.each($('.groupdel'), function(i) { 
        if ($('.groupdel')[i].checked) {
            toDelete.push($('.groupdel')[i].id);
            multiple=true;
        }
    });
    $('#groupnames')[0].value=toDelete;
    $('#formdeletemultiplegroup')[0].submit();
}

function enableAll(obj){
    $.each($('.groupdel'), function(i) { 
        $('.groupdel')[i].checked=obj.checked;
    });
    if(obj.checked)
        $('#selAction').show();
    else
        $('#selAction').hide();
}

function actionSelected(){
    
        var toDelete=new Array();
    $.each($('.groupdel'), function(i) { 
        if ($('.groupdel')[i].checked) {
            toDelete.push($('.groupdel')[i].id);
            multiple=true;
        }
    });
    $('#groupnames')[0].value=toDelete;
    
    
    var faction = $('#selAction').val();
    $('#faction')[0].value=faction;
    $('#formdeletemultiplegroup')[0].submit();
}
-->
</script>
{/literal}



