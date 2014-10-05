<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Panel de control</title>
    <link rel="shortcut icon" href="{$baseurl}/img/favicon.ico" type="image/x-icon" />
    <link href="{$baseurl}/css/bootstrap.min.css" rel="stylesheet">
    <link href="{$baseurl}/css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">
    <link href="{$baseurl}/css/maxcontrol.css" rel="stylesheet">
    <link href="{$baseurl}/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
{* needed for document.ready *}
    <script src="{$baseurl}/js/jquery-1.11.0.js"></script>
{if isset($DEBUG)}
    <link href="{$baseurl}/css/debug.css" type="text/css" rel="stylesheet" /> 
    <script type="text/javascript">
    var debug_enabled=true;
    </script>
{/if}
</head>
<body>
    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{$baseurl}">
                    <img src="{$baseurl}/img/title.png" alt="Panel de Control" style="height: 35px;"/> 
                    Panel de control<span class="hidden-xs"> de Servidor de Centro</span>
                </a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right pull-right">
                
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        {$role} <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="{$baseurl}/miperfil"><i class="fa fa-user fa-fw"></i> Mi perfil</a>
                        </li>
                        
                        <li class="divider"></li>
                        <li>
                            <a href="{$logout_url}"><i class="fa fa-sign-out fa-fw"></i> Salir</a>
                            {*<a id="m" href="{$logout_url}"><small>{$role}</small><br/>Salir de sesión</a>*}
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        {*
                        <li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                <input type="text" class="form-control" placeholder="Buscar...">
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            </div>
                            <!-- /input-group -->
                        </li>
                        *}

                        {$menuObj->getMenu()}
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <!-- Page Content -->
        <div id="page-wrapper">

            {if isset($session_info) }
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {$session_info}
            </div>
            {/if}

            
            {if isset($session_error) }
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {$session_error}
            </div>
            {/if}

            <div class="row">
                <div class="col-lg-12" id="main">
                    {$content}
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->


            {if isset($debug) }
            <!-- debug -->
            <div id="aviso">
               <div class="center bold">CONSOLA DE DEPURACIÓN</div>
               {$debug}
            </div>
            <!-- fin debug -->
            {/if}
        </div>
        <!-- /#page-wrapper -->



    </div>
    <!-- /#wrapper -->

<div id="footer" class="container">
    <nav class="navbar navbar-default navbar-fixed-bottom">
        <div class="navbar-inner navbar-content-center text-center">

            <a rel='external' title='Comunidad de Madrid (ventana nueva)' href='http://www.madrid.org/' class='external-link'>
                <img width='132' height='43' alt='Comunidad de Madrid, Consejería de Educación y Empleo' src='{$baseurl}/img/consejeria.png' />
            </a>
            <br class="visible-xs">
            <span>
                <strong>EducaMadrid</strong> - 2011-{$smarty.now|date_format:'%Y'}  - Consejería de Educación y Empleo, Comunidad de Madrid, max-control versión: {$max_control_version}
            </span>
            <br class="visible-xs">
            <a rel='external' title='EducaMadrid (ventana nueva)' href='http://www.educa.madrid.org' class='external-link'>
                <img width='132' height='43' alt='EducaMadrid' src='{$baseurl}/img/educamadrid.png' />
            </a>
        </div>
    </nav>
</div>

    
    <script src="{$baseurl}/js/bootstrap.min.js"></script>
    <script src="{$baseurl}/js/plugins/metisMenu/metisMenu.min.js"></script>
    <script src="{$baseurl}/js/maxcontrol.js"></script>

</body>
</html>
