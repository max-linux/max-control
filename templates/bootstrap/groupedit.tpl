
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Cambiar nombre del grupo: <span class='stitle'>{$group}</span></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <form role="form" action='{$urlform}' method='post' onsubmit="return checkform();">

                            <div class="form-group">
                                <label>Nombre del grupo:</label>
                                <input type='text' class='form-control'  name='cn' id='cn' value='{$group}' autocomplete="off" maxlength='20' onblur='javascript:usedcn(this.value);' />
                                <div class="alert alert-danger" style="display:none;" id='groupnotvalid'>El identificador está ocupado</div>
                                <div class="alert alert-success" style="display:none;" id='groupvalid'>El identificador está libre</div>
                                <div class="alert alert-danger" style="display:none;" id='groupempty'>El identificador no puede estar vacío</div>
                                <div class="alert alert-danger" style="display:none;" id='groupinvalid'>Identificador no válido (letras ASCII, números o .-_)</div>
                            </div>
                            
                            {*
                            <div class="form-group">
                                <label>Comentario</label>
                                <input type='text' class='form-control' name='description' id='description' size='70'/> 
                            </div>
                            
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name='createshared' value='1' checked='true'> Crear recurso compartido
                                    </label>
                                    <p class="help-block">(Podrán acceder a él los usuarios añadidos a este grupo)</p>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name='readonly' value='1'> Sólo lectura para alumnos
                                    </label>
                                    <p class="help-block">(Los alumnos sólo podrán leer archivos, no podrán ni escribir ni borrar.)</p>
                                </div>
                            </div>
                            *}

                            <input type='hidden' name='group' value='{$group}' />
                            <button type="submit" class="btn btn-primary pull-right">Guardar</button>
                        </form>
                    </div>
                    <!-- /.col-lg-6 (nested) -->
                    
                </div>
                <!-- /.row (nested) -->
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>

 {*
 <form action='{$urlform}' method='post' onsubmit="return checkform();"> 
    <table class='formTable'> 
        <tr> 
            <td class='tright'><span class="ftitle">Nombre del grupo:</span></td> 
            <td>
                <input type='text' class='inputText' name='cn' id='cn' autocomplete="off" maxlength='20' value='{$group}' onblur='javascript:usedcn(this.value);' />
                <span class="error" style="display:none;" id='groupnotvalid'>El identificador está ocupado</span>
                <span class="note" style="display:none;" id='groupvalid'>El identificador está libre</span>
                <span class="error" style="display:none;" id='groupempty'>El identificador no puede estar vacío</span>
                <span class="error" style="display:none;" id='groupinvalid'>Identificador no válido (letras ASCII, números o .-_)</span>
            </td> 
        </tr>
    
    <tr> 
        <td></td> 
        <td> 
        <input class='inputButton' type='submit' name='Guardar' value="Renombrar grupo" alt="Guardar" /> 
        <input type='hidden' name='group' value='{$group}' />
        </td> 
    </tr> 
    </table> 
    </form> 
*}

<script type="text/javascript">
    var ajaxurl="{$baseurl}/index.php?ajax=1";
    var valid=false;
</script>

{literal}
<script type="text/javascript">
<!--
function usedcn(cn) {
    if ( cn == '' ) {
        $('#groupempty').show();
        return false;
    }
    $('#groupempty').hide();
    $('#groupinvalid').hide();
    // ver si el grupo está ocupado
    $.ajax({
      type: "POST",
      url: ajaxurl,
      data: "accion=usedcn&cn="+cn,
      success: function(data) {
        if (data == 'used') {
            $('#groupnotvalid').show();
            $('#groupvalid').hide();
            valid=false;
        }
        else if (data == 'invalid') {
            $('#groupinvalid').show();
            $('#groupvalid').hide();
            valid=false;
        }
        else if (data == 'free') {
            $('#groupnotvalid').hide();
            $('#groupvalid').show();
            valid=true;
        }
        else {
            alert('Error servicio AJAX');
            valid=false;
        }
      }
    });
}

function checkform() {
    usedcn($('#cn')val());
    return valid;
}
-->
</script>
{/literal}
