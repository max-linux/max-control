
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Editando usuario  <span class='stitle'>{$username}</span></h1>
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
                                <label>Identificador: {$u->attr('cn')}</label>
                            </div>
                            
                            <div class="form-group">
                                <label>Nombre</label>
                                <input type='text' class='form-control' name='givenname' id='givenname' value="{$u->attr('givenname')}" /> 
                            </div>

                            <div class="form-group">
                                <label>Apellidos</label>
                                <input type='text' class='form-control' name='sn' id='sn' value="{$u->attr('sn')}" /> 
                            </div>

                            <div class="form-group">
                                <label>Comentario</label>
                                <input type='text' class='form-control' name='description' id='description' size='70' value="{$u->attr('description')}" /> 
                            </div>
                            
                            
                            <div class="form-group form-inline">
                                <label>Permisos (rol)</label>
                                <select class="form-control pull-right" name='role' id='role' > 
                                    <option value='' {if $u->get_role() == ''}selected='selected'{/if}>Alumno</option> 
                                    <option value='teacher' {if $u->get_role() == 'teacher'}selected='selected'{/if}>Profesor</option> 
                                    <option value='tic' {if $u->get_role() == 'tic'}selected='selected'{/if}>Coordinador TIC</option> 
                                    {if $permisos->is_admin() }
                                    <option value='admin' {if $u->get_role() == 'admin'}selected='selected'{/if}>Administrador</option> 
                                    {/if}
                                </select> 
                            </div>


                            <div class="form-group form-inline">
                                <label>Acceso a consola</label>
                                <select class="form-control pull-right" name='loginshell' id='loginshell' > 
                                    <option value='/bin/false' {if $u->attr('loginshell') == '/bin/false'}selected='selected'{/if}>Sin acceso a shell</option> 
                                    <option value='/bin/bash' {if $u->attr('loginshell') == '/bin/bash'}selected='selected'{/if}>Con acceso a shell (bash)</option> 
                                </select>  
                            </div>


                            <div class="form-group">
                                <label>Contraseña</label>
                                <input type='password' class='form-control' name='newpwd' id='newpwd' value="" autocomplete="off" /> (dejar vacío para no cambiar)
                            </div>

                            <div class="form-group">
                                <label>Confirmar contraseña</label>
                                <input type='password' class='form-control' name='newpwd2' id='newpwd2' value="" autocomplete="off" onblur='javascript:checkpass();' /> 
                                <div class="alert alert-danger" style="display:none;" id='badpassword'>Las contraseñas no coinciden</div>
                            </div>
                            
                            <input type='hidden' name='cn' value='{$u->attr('cn')}' />
                            <button class='btn btn-danger' type='button' name='reset' value="Resetear perfil" title="Borra todos los archivos personales de este usuario" onclick="javascript:resetProfile('{$username}');"/>Resetear perfil</button>

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
    var baseurl="{$baseurl}/usuarios/";
</script>

{literal}
<script type="text/javascript">
function checkpass() {
    if ( $('#newpwd').val() !=  $('#newpwd2').val()) {
        $('#badpassword').show().closest('.form-group').addClass('has-error');
        return false;
    }
    else {
        $('#badpassword').hide().closest('.form-group').removeClass('has-error');
        return true;
    }
}

function resetProfile(username) {
    document.location.href=baseurl+"resetprofile/"+username;
}
</script>
{/literal}

