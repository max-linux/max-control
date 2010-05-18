<?php /* Smarty version 2.6.22, created on 2010-05-18 09:20:14
         compiled from login.tpl */ ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Panel de control</title> 
    <meta http-equiv=Content-Type content="text/html; charset=utf-8"> 
    <link href="<?php echo $this->_tpl_vars['baseurl']; ?>
/css/login.css" type="text/css" rel="stylesheet" /> 

<?php if (isset ( $this->_tpl_vars['pruebas'] )): ?>
    <link href="<?php echo $this->_tpl_vars['baseurl']; ?>
/css/debug.css" type="text/css" rel="stylesheet" /> 
    <script type="text/javascript">
    var debug_enabled=true;
    </script>
<?php endif; ?>


</head> 
<body> 
<center> 
    <div id='login'> 
        <div id='loginin'> 
        <a href="<?php echo $this->_tpl_vars['baseurl']; ?>
"> 
            <img src="<?php echo $this->_tpl_vars['baseurl']; ?>
/img/title.png" alt="Panel de Control"/> 
        </a>

        <form name='login' action='<?php echo $this->_tpl_vars['login_url']; ?>
' method='post'> 
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

<?php if (isset ( $this->_tpl_vars['debug'] )): ?>
<!-- debug -->
<div id="aviso">
   <div class="center bold">CONSOLA DE DEPURACIÓN</div>
   <?php echo $this->_tpl_vars['debug']; ?>

</div>
<!-- fin debug -->
<?php endif; ?>

<div id="footer">
  <div id='site-bottom'>
    <div class='main-projects'>
        <a rel='external' title='Comunidad de Madrid (ventana nueva)' href='http://www.madrid.org/' class='external-link'>
            <img width='132' height='43' alt='Comunidad de Madrid, Consejeria de Educacion' src='<?php echo $this->_tpl_vars['baseurl']; ?>
/img/consejeria.png'>
        </a>
        <a rel='external' title='EducaMadrid (ventana nueva)' href='http://www.educa.madrid.org' class='external-link'>
            <img width='132' height='43' alt='EducaMadrid' src='<?php echo $this->_tpl_vars['baseurl']; ?>
/img/educamadrid.png'>
        </a>
    </div>
    <p class='copyright-notice'><strong>EducaMadrid</strong> - 2010  - Consejeria de Educacion, Comunidad de Madrid</p>
  </div>
</div>
</body> 
</html>