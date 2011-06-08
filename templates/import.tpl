
<h2>Importador de usuarios</h2>

<div class='help'>
Para importar una lista grande de usuarios se necesita esa lista en formato CSV
que se puede generar desde una hoja de cálculo (Excel, OpenOffice Calc).
<br/>

Puede descargar un archivo plantilla desde aquí:
<br/><br/>

<a href="{$baseurl}/files/plantilla_usuarios.csv">Plantilla usuarios (formato CSV)</a>
<br/><br/>

Como separadores use la coma "," y como delimitador de texto las comillas dobles ".
<br/><br/>
El órden de los campos es el siguiente:
<ul>
    <li>usuario</li>
    <li>contraseña</li>
    <li>Nombre</li>
    <li>Apellidos</li>
    <li>Comentario</li>
    <li>Grupo</li>
</ul>
<br/><br/>

El rol por defecto para todos los usuarios importados es <b>alumno</b>.

El script se ejecutará durante un máximo de 2 minutos, el alta de cada usuario 
necesita entre 2 y 3 segundos, por lo que se recomienda importar en lotes de 60 en 60 como máximo.
<br/><br/>

El identificador de usuario no puede tener espacios, caracteres raros y debe empezar por una letra.
</div>


<table class="bDataTable"> 
    <tr> 
        <td> 
        <form id="importer" method="post" enctype="multipart/form-data" action="{$urlform}" onsubmit="return disable_submit();"> 
          <input name="importfile" type="file">
          <input type="hidden" name="MAX_FILE_SIZE" value="50000">
          <input type='submit' name='import' id='import' value="Importar" title="Importar"/>
        </form>
        </td> 
    </tr> 
</table> 

{literal}
<script type="text/javascript">
<!--
function disable_submit() {
    $('#import')[0].disabled=true;
    $('#import')[0].value='Importando nuevas cuentas...';
    $('#importer')[0].submit();
}
-->
</script>
{/literal}


{*
{if $DEBUG}
{debug}
{/if}
*}
