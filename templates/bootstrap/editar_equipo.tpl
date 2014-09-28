<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">{$action} equipo <span class='stitle'>{$hostname}</span></h1>
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

                            
                            
                            <div class="form-group form-inline">
                                <label class="col-lg-4">Dirección MAC</label>
                                <input type='text' class='form-control' name='macAddress' id='macAddress' value="{$u->attr('macAddress')}" />
                                <button class='form-control btn btn-primary' type='button' name='getmacbtn' onclick="javascript:getmac();">Averiguar MAC</button>
                            </div>

                            <div class="form-group form-inline">
                                <label class="col-lg-4">Dirección IP</label>
                                <input type='text' class='form-control' name='ipHostNumber' id='ipHostNumber' value="{$u->attr('ipHostNumber')}" />
                                <button class='form-control btn btn-primary' type='button' name='getipbtn' onclick="javascript:getip();">Averiguar IP</button>

                            </div>

                            <div class="form-group form-inline">
                                <label class="col-lg-4">Archivo de arranque</label>
                                <input type='text' class='form-control' name='bootFile' id='bootFile' value="{$u->attr('bootFile')}" /> 
                                <p class="help-block">(por defecto vacío)</p>
                            </div>
                            
                            
                            
                            <div class="form-group form-inline">
                                <label class="col-lg-4">Aula</label>
                                <select class='form-control' name='aula' id='aula' > 
                                    <option value=''></option> 
                                    {foreach from=$aulas key=k item=o}
                                    <option value='{$o->attr('cn')}' {if $o->attr('cn') == $u->attr('aula')}selected="selected"{/if}>{$o->attr('cn')}</option>
                                    {/foreach}
                                </select>  
                            </div>


                            
                            <input type='hidden' name='hostname' name='{$action}' value='{$hostname}' />
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
<form action='{$urlform}' method='post'> 
    <table class='formTable'> 
    <tr> 
        <td class='tright'><span class="ftitle">Dirección MAC:</span></td>
        <td><input type='text' class='inputText' name='macAddress' id='macAddress' value="{$u->attr('macAddress')}" /> 
        <input class='inputButton' type='button' name='getmacbtn' value="Averiguar MAC" alt="Averiguar MAC" onclick="javascript:getmac();"/>
        (ejemplo 00:00:00:00:00:00)
        </td>
    </tr>
    
    <tr> 
        <td class='tright'><span class="ftitle">Dirección IP:</span></td>
        <td>
        <input type='text' class='inputText' name='ipHostNumber' id='ipHostNumber' value="{$u->attr('ipHostNumber')}" /> 
        <input class='inputButton' type='button' name='getipbtn' value="Averiguar IP" alt="Averiguar IP" onclick="javascript:getip();"/>
        (ejemplo 192.168.1.23) 
        </td>
    </tr> 
    
    <tr> 
        <td class='tright'><span class="ftitle">Archivo de arranque:</span></td>
        <td><input type='text' class='inputText' name='bootFile' id='bootFile' value="{$u->attr('bootFile')}" /> (por defecto vacío)</td>
    </tr> 
    
    <!--<tr> 
        <td class='tright'><span class="ftitle">Parámetros de arranque:</span></td>
        <td><input type='text' class='inputText' name='bootParameter' value="{$u->attr('bootParameter')}" /> (variable=valor)</td>
    </tr> -->


    <tr>
        <td class='tright'><span class='ftitle'>Aula:</span></td> 
        <td> 
            <select name='aula' id='aula' > 
                <option value=''></option> 
                {foreach from=$aulas key=k item=o}
                <option value='{$o->attr('cn')}' {if $o->attr('cn') == $u->attr('aula')}selected="selected"{/if}>{$o->attr('cn')}</option>
                {/foreach}
            </select> 
        </td> 
    </tr>

    <tr> 
        <td></td> 
        <td> 
        <input class='inputButton' type='submit' name='{$action}' value="Guardar" alt="Guardar" /> 
        <input type='hidden' name='hostname' value='{$hostname}' />
        </td> 
    </tr>

    </table> 
</form> 
*}

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Aviso</h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->



<script type="text/javascript">
    var hostname="{$hostname}";
    var ajaxurl="{$baseurl}/index.php?ajax=1";
</script>

{literal}
<script type="text/javascript">
<!--
function getip() {
    // intentar cargar IP con AJAX
    $.ajax({
      type: "POST",
      url: ajaxurl,
      data: "accion=getip&hostname="+hostname,
      success: function(data) {
        if (data != '') {
            $('#ipHostNumber').val(data);
        }
        else {
            $('#ipHostNumber').val('0.0.0.0');
            $('.modal-body').html('No se pudo averiguar la dirección IP con su nombre de equipo.<br>¿Está el equipo apagado?');
            $('#myModal').modal('show');
        }
      }
    });
}
function getmac() {
    // intentar cargar IP con AJAX
    $.ajax({
      type: "POST",
      url: ajaxurl,
      data: "accion=getmac&hostname="+hostname,
      success: function(data) {
        if (data != '') {
            $('#macAddress').val(data);
        }
        else {
            // no sobreescribir
            //$('#macAddress')[0].value='00:00:00:00:00:00';
            $('.modal-body').html('No se pudo averiguar la dirección MAC del equipo.<br>¿Está el equipo apagado?');
            $('#myModal').modal('show');
        }
      }
    });
}
-->
</script>
{/literal}

{*
{if $DEBUG}
{debug}
{/if}
*}
