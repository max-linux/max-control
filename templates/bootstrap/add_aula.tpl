<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Añadir aula</h1>
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
                                <label>Nombre del aula:</label>
                                <input type='text' class='form-control'  name='cn' id='cn' autocomplete="off" maxlength='20' onblur='javascript:usedcn(this.value);' autofocus/>
                                <div class="alert alert-danger" style="display:none;" id='groupnotvalid'>El identificador está ocupado</div>
                                <div class="alert alert-success" style="display:none;" id='groupvalid'>El identificador está libre</div>
                                <div class="alert alert-danger" style="display:none;" id='groupempty'>El identificador no puede estar vacío</div>
                                <div class="alert alert-danger" style="display:none;" id='groupinvalid'>Identificador no válido (letras ASCII, números o .-_)</div>
                            </div>
                            
                            <div class="form-group">
                                <label>Comentario</label>
                                <input type='text' class='form-control' name='description' id='description' size='70'/> 
                            </div>
                            
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
    $.ajax({
      type: "POST",
      url: ajaxurl,
      data: "accion=usedaula&cn="+cn,
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
        }
      }
    });
}
function checkform() {
    if ( $('#cn').val() == '' ) {
        $('#groupempty').show();
        return false;
    }
    return valid;
}
-->
</script>
{/literal}
