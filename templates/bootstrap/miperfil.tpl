
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Editando mi perfil</h1>
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
                                <label>Nombre</label>
                                <input type='text' class='form-control' name='givenname' id='givenname' value="{$u->attr('givenname')}" /> 
                            </div>

                            <div class="form-group">
                                <label>Apellidos</label>
                                <input type='text' class='form-control' name='sn' id='sn' value="{$u->attr('sn')}" /> 
                            </div>

                            <div class="form-group">
                                <label>Comentario</label>
                                <input type='text' class='form-control' name='description' id='description' value="{$u->attr('description')}" /> 
                            </div>
                            
                            
                            <div class="form-group form-inline">
                                <label>Permisos (rol)</label>
                                {if $u->get_role() == ''}Alumno{/if}
                                {if $u->get_role() == 'teacher'}Profesor{/if}
                                {if $u->get_role() == 'tic'}Coordinador TIC{/if}
                                {if $u->get_role() == 'admin'}Administrador{/if}
                            </div>


                            
                            <div class="form-group">
                                <label>Contraseña</label>
                                <input type='password' class='form-control' name='newpwd' id='newpwd' value="" autocomplete="off" /> (dejar vacío para no cambiar)
                            </div>

                            <div class="form-group">
                                <label>Confirmar contraseña</label>
                                <input type='password' class='form-control' name='newpwd2' id='newpwd2' value="" autocomplete="off" onblur='javascript:checkpass();' /> 
                                <p class="alert-danger" style="display:none;" id='badpassword'>Las contraseñas no coinciden</p>
                            </div>
                            
                            <input type='hidden' name='cn' value='{$u->attr('cn')}' />

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
 


{literal}
<script type="text/javascript">
function checkpass() {
    if ( $('#newpwd').val() !=  $('#newpwd2').val()) {
        $('#badpassword').show();
        return false;
    }
    else {
        $('#badpassword').hide();
        return true;
    }
}
</script>
{/literal}


{*
{if $DEBUG}
{debug}
{/if}
*}
