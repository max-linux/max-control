
<h2>Importador de usuarios</h2>

<div class='help'>
Para importar una lista grande de usuarios se necesita esa lista en formato CSV
que se puede generar desde una hoja de cálculo (Excel u OpenOffice/LibreOffice Calc).
<br/>

El Coordinador TIC desde la página de gestión de EducaMadrid, puede generar este archivo, 
no hay que crearlo de cero.
<br/>

Puede descargar un archivo plantilla desde aquí:
<br/><br/>

<a href="{$baseurl}/files/plantilla_usuarios.csv">Plantilla usuarios (formato CSV)</a>
<br/><br/>

Como separadores use la coma "," y como delimitador de texto las comillas dobles ".
<br/><br/>
El órden de los campos es el siguiente:
<ul>
    <li>Nombre</li>
    <li>Apellidos</li>
    <li>Id. de usuario (login)</li>
    <li>Centro (no se tiene en cuenta)</li>
    <li>Clase (grupo)</li>
    <li>Tipo (alumno, profesor...)</li>
</ul>


La contraseña por defecto para todos los usuarios es <b>cmadrid</b>
<br/><br/>


El identificador de usuario y el grupo no puede tener espacios, caracteres raros y debe empezar por una letra (si el grupo empieza por número se añadirá <b>g_</b> al principio.
<br/><br/>

La importación necesita entre uno y tres segundos por usuario y se ejecuta en segundo plano, se podrá ver el progreso y los usuarios creados durante el tiempo que dure la importación o una vez que haya terminado un resumen.
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
