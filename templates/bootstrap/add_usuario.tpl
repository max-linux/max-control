
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Añadir usuario</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <form role="form" action='{$urlform}' method='post' onsubmit="return checkpass();">

                            <div class="form-group">
                                <label>Identificador:</label>
                                <input type='text' class='form-control' name='cn' autocomplete="off" maxlength='20' onblur='javascript:usedcn(this.value);' />
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
                            
                            
                            <div class="form-group form-inline">
                                <label>Permisos (rol)</label>
                                <select class="form-control pull-right" name='role' id='role' > 
                                    <option value=''>Alumno</option> 
                                    <option value='teacher'>Profesor</option> 
                                    <option value='tic'>Coordinador TIC</option> 
                                    {if $permisos->is_admin() }
                                    <option value='admin'>Administrador</option> 
                                    {/if}
                                </select> 
                            </div>


                            <div class="form-group form-inline">
                                <label>Acceso a consola</label>
                                <select class="form-control pull-right" name='loginshell' id='loginshell' > 
                                    <option value='/bin/false' selected='selected'>Sin acceso a shell</option> 
                                    <option value='/bin/bash'>Con acceso a shell (bash)</option> 
                                </select>  
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
<form action='{$urlform}' method='post' onsubmit="return checkpass();"> 
    <table class='formTable'> 
        <tr> 
            <td class='tright'><span class="ftitle">Nombre de usuario:</span></td> 
            <td>
                <input type='text' class='inputText' name='cn' autocomplete="off" maxlength='20' onblur='javascript:usedcn(this.value);' />
                <span class="error" style="display:none;" id='usernotvalid'>El identificador está ocupado</span>
                <span class="note" style="display:none;" id='uservalid'>El identificador está libre</span>
                <span class="error" style="display:none;" id='userempty'>El identificador no puede estar vacío</span>
                <span class="error" style="display:none;" id='userinvalid'>Identificador no válido (letras ASCII, números o .-_)</span>
            </td> 
        </tr> 

        <tr> 
            <td class='tright'><span class='ftitle'>Nombre:</span></td> 
            <td><input type='text' class='inputText' name='givenname' autocomplete="off" /></td> 
        </tr>

        <tr>
            <td class='tright'><span class='ftitle'>Apellidos:</span></td> 
            <td><input type='text' class='inputText' name='sn' autocomplete="off" /></td> 
        </tr>

        <tr> 
            <td class='tright'><span class='ftitle'>Contraseña:</span></td> 
            <td>
                <input type='password' class='inputText' name='password' id='password' autocomplete="off"/> 
                <span class="error" style="display:none;" id='nullpassword'>Las contraseñas no pueden estar vacías</span>
            </td>
        </tr>

        <tr>
            <td class='tright'><span class='ftitle'>Confirme contraseña:</span></td> 
            <td>
                <input type='password' class='inputText' name='repassword' id='repassword' autocomplete="off" onblur='javascript:checkpass();' />
                <span class="error" style="display:none;" id='badpassword'>Las contraseñas no coinciden</span>
            </td> 
        </tr> 

        <tr> 
            <td class='tright'><span class='ftitle'>Comentario:</span></td> 
            <td><input type='text' class='inputText' name='description' autocomplete="off" /></td> 
        </tr>

        <tr>
            <td class='tright'><span class='ftitle'>Rol (permisos):</span></td> 
            <td> 
                <select name='role' id='role' > 
                    <option value=''>Alumno</option> 
                    <option value='teacher'>Profesor</option> 
                    <option value='tic'>Coordinador TIC</option> 
                    {if $permisos->is_admin() }
                    <option value='admin'>Administrador</option> 
                    {/if}
                </select> 
            </td> 
        </tr>

        <tr>
            <td class='tright'><span class='ftitle'>Acceso a consola:</span></td> 
            <td> 
                <select name='loginshell' id='loginshell' > 
                    <option value='/bin/false' selected='selected'>Sin acceso a shell</option> 
                    <option value='/bin/bash'>Con acceso a shell (bash)</option> 
                </select> 
            </td> 
        </tr>

        <tr> 
            <td></td> 
            <td> 
            <input class='inputButton' type='submit' name='add' value="Añadir" alt="Añadir" /> 
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
        $('#userempty')[0].style.display='';
        return false;
    }
    $('#userempty')[0].style.display='none';
    $('#userinvalid')[0].style.display='none';
    // ver si el usuario está ocupado
    $.ajax({
      type: "POST",
      url: ajaxurl,
      data: "accion=usedcn&cn="+cn,
      success: function(data) {
        if (data == 'used') {
            $('#usernotvalid')[0].style.display='';
            $('#uservalid')[0].style.display='none';
            valid=false;
        }
        else if (data == 'invalid') {
            $('#userinvalid')[0].style.display='';
            $('#uservalid')[0].style.display='none';
            valid=false;
        }
        else if (data == 'free') {
            $('#usernotvalid')[0].style.display='none';
            $('#uservalid')[0].style.display='';
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
