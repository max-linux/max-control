<?php /* Smarty version 2.6.22, created on 2010-05-18 09:21:11
         compiled from index.tpl */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
<title>Panel de control</title> 
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="<?php echo $this->_tpl_vars['baseurl']; ?>
/css/style.css" rel="stylesheet" type="text/css" /> 
<?php if (isset ( $this->_tpl_vars['pruebas'] )): ?>
    <link href="<?php echo $this->_tpl_vars['baseurl']; ?>
/css/debug.css" type="text/css" rel="stylesheet" /> 
    <script type="text/javascript">
    var debug_enabled=true;
    </script>
<?php endif; ?>
</head>
<body>

<div id="top">
</div> 
<div id="header"> 
    <a href="<?php echo $this->_tpl_vars['baseurl']; ?>
"> 
    <img src="<?php echo $this->_tpl_vars['baseurl']; ?>
/img/title.png" alt="title"/> 
    </a>
</div> 

<div id="hmenu"> 
  <a id="m" href="<?php echo $this->_tpl_vars['logout_url']; ?>
">Salir de sesión</a> 
</div> 

<span><h3>Panel de control de Servidor de Centro</h3></span>

<!-- div menu -->
<div id='menu'> 
    <ul id='nav'> 
        
        <li id=''> 
            <div class='separator'>Menú</div> 
        </li> 
        
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "menuizdo.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        
    </ul>
</div>
<!-- FIN div menu -->

<div id="limewrap">
    <div id="content"><div> 
</div> 

<?php if (isset ( $this->_tpl_vars['have_alerts'] )): ?>
 <div id="alerts">
        <noscript>
        Esta página usa Javascript.<br/>
        Por favor, actívelo, añada este sitio a sus sitios de confianza o actualice su navegador.
        </noscript>
   <?php echo $this->_tpl_vars['alerts']; ?>

  </div>
<?php endif; ?>


<div id="main"> 
<?php echo $this->_tpl_vars['content']; ?>

</div>

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