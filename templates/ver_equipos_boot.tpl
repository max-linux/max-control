<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Tipo de arranque de equipos ({$pager->getMAX()})</h1>
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
                          
                          <a href="{$urlrefresh}" class="btn btn-warning" >Actualizar archivos PXE</a>
                          <a href="{$urlclean}" class="btn btn-warning" >Limpiar archivos PXE</a>

                          
                        </form>

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
                                <th>Arranque configurado</th> 
                                <th>Configurar Arranque</th> 
                            </tr>
                        </thead>
                        <tbody>
                    
                    {foreach from=$equipos key=k item=u}
                    <trid="{$u->hostname()}"> 
                    {if $u->teacher_in_computer()}
                      <td class='text-center'>{$u->cn}</td> 
                      <td class='text-center'>{$u->ipHostNumber} / {$u->macAddress}</td> 
                      <td class='text-center'>{$u->getBoot()}</td>
                      {if $u->macAddress != ''}
                        <td class='text-center'> 
                            <a href="{$urleditar}/{$u->hostname()}"><img src="{$baseurl}/img/edit-table.gif" alt="configurar" title="Configurar arranque" /></a>
                        </td>
                      {else}
                        <td class='text-center'>no MAC</td>
                      {/if}
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

