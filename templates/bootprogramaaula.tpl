
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Configurar el arranque programado del aula <u>{$aula->cn}</u></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<form id='programerform' action='{$urlform}' method='post'>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class='thAction'>Acción</th>
                                <th>Sistema Operativo</th>
                                {foreach from=$programer->weekDays() key=k item=u}
                                    <th>{$u}</th>
                                {/foreach}
                            </tr>
                        </thead>
                        <tbody>
                    
                            <tr>
                                <td>Arrancar</td>
                                <td>{$programer->getSO('wakeonlan', $tipos)}</td>
                                {foreach from=$programer->getTimers('wakeonlan') key=k item=u}
                                    <td class="form-inline">{$u}</td>
                                {/foreach}
                            </tr>
                            <tr class='table-programer'>
                                <td>Reiniciar</td>
                                <td>{$programer->getSO('reboot', $tipos)}</td>
                                {foreach from=$programer->getTimers('reboot') key=k item=u}
                                    <td class="form-inline">{$u}</td>
                                {/foreach}
                            </tr>
                            <tr class='table-programer'>
                                <td>Parar</td>
                                <td>{* poweroff no tiene select *}</td>
                                {foreach from=$programer->getTimers('poweroff') key=k item=u}
                                    <td class="form-inline">{$u}</td>
                                {/foreach}
                            </tr>

                        </tbody>
                    </table>

                    <div class="col-lg-6">
                        <input type='hidden' name='cn' value='{$aula->cn}' />
                        <input type='hidden' name='safecn' value='{$aula->safecn()}' />
                        <input type='hidden' name='faction' id='faction' value='save' />

                        <button type="button" class="btn btn-danger btn-reset">Resetear</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>


                </div>
                <!-- /.table-responsive -->



            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
</form>


{literal}
<script type="text/javascript">
<!--

function programer(classname, ssource) {
    var obj=$('#'+ssource)[0];
    $.each($('.'+classname), function(i) { 
        $('.'+classname)[i].selectedIndex=obj.selectedIndex;
    });
}


$(document).ready(function() {
    $('.btn-reset').on('click', function(event) {
        event.preventDefault();
        /* Act on the event */

        var answer = confirm("¿Está seguro de borrar la programación para este aula?")
        if (answer ==0 ) {
            return false;
        }
        $('#faction')[0].value='delete';
        var form = $("#programerform");
        form.find(':input').each(function() {
            if ( $(this).attr('type') == 'select-one' ) {
                $(this)[0].selectedIndex=0;
            }
        });
        form.submit();
    });
});

-->
</script>
{/literal}


