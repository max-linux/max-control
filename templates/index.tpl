<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
<title>Panel de control</title> 
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="{$baseurl}/css/style.css" rel="stylesheet" type="text/css" /> 
<link rel="shortcut icon" href="{$baseurl}/img/favicon.ico" type="image/x-icon" />
<script type="text/javascript" src="{$baseurl}/js/jquery.js"></script>
{if isset($pruebas)}
    <link href="{$baseurl}/css/debug.css" type="text/css" rel="stylesheet" /> 
    <script type="text/javascript">
    var debug_enabled=true;
    </script>
{/if}
</head>
<body>

<div id="top">
</div> 
<div id="header"> 
    <a href="{$baseurl}"> 
    <img src="{$baseurl}/img/title.png" alt="title"/> 
    </a>
</div> 

<div id="hmenu"> 
  <a id="m" href="{$logout_url}">Salir de sesión</a> 
</div> 

<h3>Panel de control de Servidor de Centro</h3>

<!-- div menu -->
<div id='menu'> 
    <ul id='nav'> 
        
        <li id='mainmenu'> 
            <div class='separator'>Menú</div> 
        </li> 
        
        {include file="menuizdo.tpl"}
        
    </ul>
</div>
<!-- FIN div menu -->

<div id="limewrap">
    <div id="content"><div>
</div> 

{if isset($have_alerts) }
 <div id="alerts">
        <noscript>
        Esta página usa Javascript.<br/>
        Por favor, actívelo, añada este sitio a sus sitios de confianza o actualice su navegador.
        </noscript>
   {$alerts}
  </div>
{/if}

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

<div id="main"> 
{$content}
</div>

{if isset($debug) }
<!-- debug -->
<div id="aviso">
   <div class="center bold">CONSOLA DE DEPURACIÓN</div>
   {$debug}
</div>
<!-- fin debug -->
{/if}

</div></div>
<div id="footer"> 
  <div id='site-bottom'>
    <div class='main-projects'>
        <a rel='external' title='Comunidad de Madrid (ventana nueva)' href='http://www.madrid.org/' class='external-link'>
            <img width='132' height='43' alt='Comunidad de Madrid, Consejeria de Educacion' src='{$baseurl}/img/consejeria.png' />
        </a>
        <a rel='external' title='EducaMadrid (ventana nueva)' href='http://www.educa.madrid.org' class='external-link'>
            <img width='132' height='43' alt='EducaMadrid' src='{$baseurl}/img/educamadrid.png' />
        </a>
    </div>
    <p class='copyright-notice'><strong>EducaMadrid</strong> - 2010  - Consejeria de Educacion, Comunidad de Madrid</p>
  </div> 
</div>
</body> 
</html> 
