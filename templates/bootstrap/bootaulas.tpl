
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Listado de aulas ({$pager->getMAX()})</h1>
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
                          
                          <button type="submit" class="btn btn-primary">Buscar</button>
                          <a href="{$urlrefresh}" class="btn btn-warning" >Actualizar archivos PXE</a>
                          <a href="{$urlclean}" class="btn btn-warning" >Limpiar archivos PXE</a>

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
                                <th>Nombre {$pager->getSortIcons('cn')}</th> 
                                <th>Arranque por defecto {$pager->getSortIcons('cachedBoot')}</th>
                            </tr>
                        </thead>
                        <tbody>
                    
                    {foreach from=$aulas item=u}
                    <tr id="{$u->safecn()}"> 
                    {if $u->teacher_in_aula()}
                      <td class='text-center'>{$u->cn}

                            <div class="btn-group pull-right">
                                <button type="button" class="btn btn-default btn-xs dropdown-toggle btn btn-info" data-toggle="dropdown">
                                    <i class="fa fa-chevron-down"></i>
                                </button>
                                <ul class="dropdown-menu slidedown" data-data='{$u|@json_encode}'>
                                    <li><a href="{$urleditar}/{$u->cn}"><i class="fa fa-edit fa-fw"></i> Editar</a></li>
                                    {if $u->get_num_computers() > 0 }
                                      <li><a href="{$urlprogramar}/{$u->cn}">
                                        {if $programer->isProgramed($u->safecn()) }
                                        <i class="fa fa-plus-square fa-fw"></i> Programar</a>
                                        {else}
                                        <i class="fa fa-tasks fa-fw"></i> Programar</a>
                                        {/if}
                                      </li>
                                    {else}
                                      <li><a href="{$urledithosts}/{$u->cn}"><i class="fa fa-warning fa-fw"></i> Aula vacía</a></li>
                                    {/if}
                                </ul>
                            </div>

                      </td> 
                      <td class='text-center'><span>{$u->getBoot()}</span></td>
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

