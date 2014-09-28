

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
                          <input type='hidden' name='role' id="role" value='{$role}' />
                          
                          <button type="submit" class="btn btn-primary">Buscar</button>
                          <a href="{$urladd}" class="btn btn-warning" >Añadir aula</a>

                          
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
                                <th class="hidden-xs">Profesores en este aula</th>
                                {if $mode == 'admin'}
                                <th class="hidden-xs">Equipos en este aula</th>
                                {/if}
                            </tr>
                        </thead>
                        <tbody>
                    
                    {foreach from=$aulas item=u}
                      <tr class='border' id="{$u->safecn()}"> 
                        <td class="text-center">
                            {if $u->attr('description') != ''}
                            <acronym title='{$u->attr('description')}'><span>{$u->cn}</span></acronym>
                            {else}
                            <span>{$u->cn}</span>
                            {/if}

                            <div class="btn-group pull-right">
                                <button type="button" class="btn btn-default btn-xs dropdown-toggle btn btn-info" data-toggle="dropdown">
                                    <i class="fa fa-chevron-down"></i>
                                </button>
                                <ul class="dropdown-menu slidedown" data-data='{$u|@json_encode}'>
                                    <li><a href="{$urlprofesores}/{$u->cn}"><i class="fa fa-users fa-fw"></i> Profesores</a></li>
                                    <li><a href="{$urlequipos}/{$u->cn}"><i class="fa fa-desktop fa-fw"></i> Equipos</a></li>
                                    <li class="divider"></li>
                                    <li><a href="{$urlborrar}/{$u->cn}"><i class="fa fa-trash-o fa-fw"></i> Borrar</a></li>
                                    
                                </ul>
                            </div>
                        </td>
                         
                        <td class='hidden-xs text-center'>{$u->get_num_users()}</td> 
                        {if $mode == 'admin'}
                        <td class='hidden-xs text-center'>{$u->get_num_computers()}</td>
                        
                        {/if}
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

{*
<table class="bDataTable"> 
    <tr> 
        <td> 
        <form id="aulas" action="{$urlform}" method="post"> 
          <input type='text' name='Filter' id='Filter' value="{$filter}" /> 
          <input type='submit' name='button' value="Buscar" title="Buscar" /> 
          {if $mode == 'admin'}
          <input type='submit' name='button' value="Nueva aula" title="Nueva aula" />
          {/if}
        </form>
        </td> 
    </tr> 
</table> 


<table class='dataTable'> 
    <thead> 
     <tr>
      <th class=''>Nombre {$pager->getSortIcons('cn')}</th> 
      <th class=''>Profesores en este aula</th>
      {if $mode == 'admin'}
      <th class=''>Equipos en este aula</th>
      <th class=''>Borrar</th> 
      {/if}
     </tr>
    </thead>
 
 
    <tbody> 
      {foreach from=$aulas item=u}
      <tr class='border' id="{$u->safecn()}"> 
        {if $u->attr('description') != ''}
        <td class='tcenter'><acronym title='{$u->attr('description')}'><span>{$u->cn}</span></acronym></td>
        {else}
        <td class='tcenter'><span>{$u->cn}</span></td>
        {/if}
         
        <td class='tcenter'><span>
                        {$u->get_num_users()} 
                        <a href="{$urlprofesores}/{$u->cn}"><img src="{$baseurl}/img/edit-table.gif" alt="editar" /></a>
                        </span>
        </td> 
        {if $mode == 'admin'}
        <td class='tcenter'><span>
                        {$u->get_num_computers()} 
                        <a href="{$urlequipos}/{$u->cn}"><img src="{$baseurl}/img/edit-table.gif" alt="editar" /></a>
                        </span>
        
        </td>
        <td class='tcenter'> 
            <a href="{$urlborrar}/{$u->cn}"><img src="{$baseurl}/img/delete.gif" alt="borrar" /></a>
        </td>
        {/if}
      </tr>
      {/foreach}

    </tbody> 
</table> 

*}



{*
{if $DEBUG}
{debug}
{/if}
*}
