
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Bienvenido a MAX Control</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <h3>Antes de seguir vamos a añadir un usuario administrador</h3>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <form role="form" action='{$urlform}' method='post' onsubmit="return checkpass();">

                            <div class="form-group">
                                <label>Identificador:</label>
                                <input type='text' class='form-control' name='cn' autocomplete="off" maxlength='20' onblur='javascript:usedcn(this.value);' autofocus />
                                <div class="alert alert-danger" style="display:none;" id='usernotvalid'>El identificador está ocupado</div>
                                <div class="alert alert-success" style="display:none;" id='uservalid'>El identificador está libre</div>
                                <div class="alert alert-danger" style="display:none;" id='userempty'>El identificador no puede estar vacío</div>
                                <div class="alert alert-danger" style="display:none;" id='userinvalid'>Identificador no válido (letras ASCII, números o .-_)</div>
                            </div>
                            
                            <div class="form-group">
                                <label>Nombre</label>
                                <input type='text' class='form-control' name='givenname' id='givenname' /> 
                            </div>

                            <div class="form-group">
                                <label>Apellidos</label>
                                <input type='text' class='form-control' name='sn' id='sn'/> 
                            </div>

                            <div class="form-group">
                                <label>Comentario</label>
                                <input type='text' class='form-control' name='description' id='description' size='70'/> 
                            </div>


                            <div class="form-group">
                                <label>Contraseña</label>
                                <input type='password' class='form-control' name='password' id='password' value="" autocomplete="off" />
                                <div class="alert alert-danger" style="display:none;" id='nullpassword'>Las contraseñas no pueden estar vacías</div>
                            </div>

                            <div class="form-group">
                                <label>Confirmar contraseña</label>
                                <input type='password' class='form-control' name='repassword' id='repassword' value="" autocomplete="off" onblur='javascript:checkpass();' /> 
                                <div class="alert alert-danger" style="display:none;" id='badpassword'>Las contraseñas no coinciden</div>
                            </div>
                            

                            <input type='hidden' name='role' id="role" value='admin' />
                            <input type='hidden' name='loginshell' id="loginshell" value='/bin/bash' />
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
        $('#userempty').show();
        return false;
    }
    $('#userempty').hide();
    $('#userinvalid').hide();
    // ver si el usuario está ocupado
    $.ajax({
      type: "POST",
      url: ajaxurl,
      data: "accion=usedcn&cn="+cn,
      success: function(data) {
        if (data == 'used') {
            $('#usernotvalid').show();
            $('#uservalid').hide();
            valid=false;
        }
        else if (data == 'invalid') {
            $('#userinvalid').show();
            $('#uservalid').hide();
            valid=false;
        }
        else if (data == 'free') {
            $('#usernotvalid').hide();
            $('#uservalid').show();
            valid=true;
        }
        else {
            alert("Respuesta desconocida: \n'" + data + "'");
            valid=false;
        }
      }
    });
}
function checkpass() {
    if ( ! valid ) {
        return false;
    }
    $('#badpassword').hide();
    $('#nullpassword').hide();

    if ($('#password').val() == '' ) {
        $('#nullpassword').show();
        return false;
    }
    if ( $('#password').val() !=  $('#repassword').val()) {
        $('#badpassword').show();
        return false;
    }
    else {
        return true;
    }
}
-->
</script>
{/literal}
