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

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

{if isset($DEBUG)}
    <link href="{$baseurl}/css/debug.css" type="text/css" rel="stylesheet" /> 
    <script type="text/javascript">
    var debug_enabled=true;
    </script>
{/if}

</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <a href="{$baseurl}"> 
                            <img src="{$baseurl}/img/title.png" alt="Panel de Control"/> 
                        </a>
                        <h3 class="panel-title">Panel de control para el servidor de centro</h3>
                        {if isset($session_info) }
                            <div class="alert alert-success">
                            {$session_info}
                        </div>
                        {/if}

                        {if isset($session_error) }
                            <div class="alert alert-danger">
                            {$session_error}
                        </div>
                        {/if}
                    </div>
                    <div class="panel-body">
                        <form role="form" name='login' action='{$login_url}' method='post'>
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Usuario" name="username" type="username" autofocus>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Contraseña" name="password" type="password" value="" autocomplete="off">
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                                <button class="btn btn-lg btn-primary btn-block">Entrar</button>
                            </fieldset>
                        </form>
                    </div>

                    {if isset($session_info) }
                    <div class="note">
                    {$session_info}
                    </div>
                    {/if}

                    {if isset($session_error) }
                    <div class="error">
                    {$session_error}
                    </div>
                    {/if}
                    
                </div>
            </div>
        </div>
    </div>

{if isset($debug) }
<!-- debug -->
<div id="aviso">
   <div class="center bold">CONSOLA DE DEPURACIÓN</div>
   {$debug}
</div>
<!-- fin debug -->
{/if}

    <script src="{$baseurl}/js/jquery-1.11.0.js"></script>
    <script src="{$baseurl}/js/bootstrap.min.js"></script>
    <script src="{$baseurl}/js/plugins/metisMenu/metisMenu.min.js"></script>
    <script src="{$baseurl}/js/maxcontrol.js"></script>

</body>

</html>
