{*
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Panel de control</title> 
    <meta http-equiv=Content-Type content="text/html; charset=utf-8"> 
    <link href="{$baseurl}/css/login.css" type="text/css" rel="stylesheet" /> 
    <link rel="shortcut icon" href="{$baseurl}/img/favicon.ico" type="image/x-icon" />
{if isset($DEBUG)}
    <link href="{$baseurl}/css/debug.css" type="text/css" rel="stylesheet" /> 
    <script type="text/javascript">
    var debug_enabled=true;
    </script>
{/if}


</head> 
<body onload="document.login.username.focus();"> 
<center> 
    <div id='login'> 
        <div id='loginin'> 
        <a href="{$baseurl}"> 
            <img src="{$baseurl}/img/title.png" alt="Panel de Control"/> 
        </a>

        <form name='login' action='{$login_url}' method='post'> 
            <h3>Panel de control para el servidor de centro</h3>
            <dl> 
                <dt>Usuario:</dt> 
                <dd><input  class='inputTextLogin' type='text' name='username' id='username' size='25' autocomplete="off"/></dd>
                <dt>Contraseña:</dt> 
                <dd><input  class='inputTextLogin' type='password' name='password' id='password' size='25' autocomplete="off"/></dd> 
                <dd><input class='inputButton' type='submit' id='loginButton' value="Entrar"/></dd> 
            </dl> 
        </form>
        
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
</center> 


<div id="footer">
  <div id='site-bottom'>
    <div class='main-projects'>
        <a rel='external' title='Comunidad de Madrid (ventana nueva)' href='http://www.madrid.org/' class='external-link'>
            <img width='132' height='43' alt='Comunidad de Madrid, Consejería de Educación y Empleo' src='{$baseurl}/img/consejeria.png'>
        </a>
        <a rel='external' title='EducaMadrid (ventana nueva)' href='http://www.educa.madrid.org' class='external-link'>
            <img width='132' height='43' alt='EducaMadrid' src='{$baseurl}/img/educamadrid.png'>
        </a>
    </div>
    <p class='copyright-notice'><strong>EducaMadrid</strong> - 2011  - Consejería de Educación y Empleo, Comunidad de Madrid, max-control versión: {$max_control_version}</p>
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
</body> 
</html>
*}

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
