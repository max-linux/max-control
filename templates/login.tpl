<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Panel de control</title> 
    <meta http-equiv=Content-Type content="text/html; charset=utf-8"> 
    <link href="{$baseurl}/css/login.css" type="text/css" rel="stylesheet" /> 

{if isset($pruebas)}
    <link href="{$baseurl}/css/debug.css" type="text/css" rel="stylesheet" /> 
    <script type="text/javascript">
    var debug_enabled=true;
    </script>
{/if}


</head> 
<body> 
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
                <dd><input  class='inputTextLogin' type='text' name='username' id='username' size='25' /></dd>
                <dt>Contraseña:</dt> 
                <dd><input  class='inputTextLogin' type='password' name='password' id='password' size='25' /></dd> 
                <dd><input class='inputButton' type='submit' id='loginButton' value="Entrar"/></dd> 
            </dl> 
        </form> 
        </div> 
    </div> 
</center> 

{if isset($debug) }
<!-- debug -->
<div id="aviso">
   <div class="center bold">CONSOLA DE DEPURACIÓN</div>
   {$debug}
</div>
<!-- fin debug -->
{/if}

<div id="footer">
  <div id='site-bottom'>
    <div class='main-projects'>
        <a rel='external' title='Comunidad de Madrid (ventana nueva)' href='http://www.madrid.org/' class='external-link'>
            <img width='132' height='43' alt='Comunidad de Madrid, Consejeria de Educacion' src='{$baseurl}/img/consejeria.png'>
        </a>
        <a rel='external' title='EducaMadrid (ventana nueva)' href='http://www.educa.madrid.org' class='external-link'>
            <img width='132' height='43' alt='EducaMadrid' src='{$baseurl}/img/educamadrid.png'>
        </a>
    </div>
    <p class='copyright-notice'><strong>EducaMadrid</strong> - 2010  - Consejeria de Educacion, Comunidad de Madrid</p>
  </div>
</div>
</body> 
</html>
