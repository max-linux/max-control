
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Vista general</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>


<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-user fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">{$num_users}</div>
                        <div>Usuarios</div>
                    </div>
                </div>
            </div>
            <a href="{$baseurl}/usuarios/ver">
                <div class="panel-footer">
                    <span class="pull-left">Ver</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-green">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-users fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">{$num_groups}</div>
                        <div>Grupos</div>
                    </div>
                </div>
            </div>
            <a href="{$baseurl}/usuarios/grupos">
                <div class="panel-footer">
                    <span class="pull-left">Ver</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-yellow">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-desktop fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">{$num_equipos}</div>
                        <div>Equipos</div>
                    </div>
                </div>
            </div>
            <a href="{$baseurl}/equipos/ver">
                <div class="panel-footer">
                    <span class="pull-left">Ver</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-red">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-sitemap fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">{$num_aulas}</div>
                        <div>Aulas</div>
                    </div>
                </div>
            </div>
            <a href="{$baseurl}/equipos/aulas">
                <div class="panel-footer">
                    <span class="pull-left">Ver</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
</div>
<!-- /.row -->