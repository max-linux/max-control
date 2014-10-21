
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Importador en progreso</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>


<div class="row">
    <div class="col-lg-9 alert alert-info">

    <div id="stop" class="pull-right">
        <form action='{$urlstop}' method='post'> 
        <button type="submit" class="btn btn-danger">Detener importación</button>
        </form>
    </div>

    <ul style="font-size:18px;">
        <li>Fecha y hora de importación: {$status.date}</li>
        <li id="doneDate" style="display:none;">Fecha y hora de finalización: &nbsp;<span id="doneDateValue"></span></li>
        <li>Número total de cuentas a importar: <span id="total">{$status.number}</span></li>
        <li style="display:none;">Número de cuentas importadas: <span id="done">{$status.done}</span></li>
        <li>Número de cuentas importadas con éxito: <span id="doneok">{$status.done}</span></li>
        <li id="doneFailedli" style="display:none;">Número de cuentas no importadas: <span id="donefailed">0</span></li>
        <li id="numberLongUsernames" style="display:none;color:#f00;">Número de cuentas cuyo identificador se ha acortado: <span id="numberLongUsernames_txt">0</span></li>
    </ul>

    <div id="progress_bar">
        <div class="percent" id="percentValue">0%</div>
        <div style="height: 20px; width: 100%; margin: 5px 0; background-color: #d49292; -moz-border-radius: 5px; border-radius: 5px">
            <div id="progressValue" style="height: 20px; background-color: rgb(221, 41, 40); border-top-left-radius: 5px 5px; border-top-right-radius: 5px 5px; border-bottom-right-radius: 5px 5px; border-bottom-left-radius: 5px 5px; width: 0%; ">
            </div>
        </div>
    </div>

    <div class="warning" id="finished" style="display:none;width: 625px;">
    <h2>Terminado</h2>

    <form action='{$urldelete}' method='post'> 
      Para hacer una nueva importación pulse en 
      <button type="submit" class="btn btn-primary">Borrar información de importación</button>
    </form>
    </div>

    <div class="warning" id="longusernames" style="display:none;width: 625px;">
    <h2>Usuarios con identificador de más de 20 caracteres</h2>
    <h4>Para garantizar la compatibilidad con todos los sistemas, el identificador de usuarios no puede tener más de 20 carateres, por eso se han acortado los siguientes identificadores de usuario.</h4>
    <div id="longusernames_txt" style="width: 575px;"></div>
    </div>

    <div id="error" style="display:none;">
    <h2>Mensajes de error</h2>
    <pre id="error_messages" style="width: 660px;"></pre>
    </div>

    <div id="info" style="display:none;">
    <h2>Mensajes de información</h2>
    <pre id="info_messages" style="width: 660px;"></pre>
    </div>


    </div>
</div>

<script type="text/javascript">
    var ajaxurl="{$baseurl}/index.php?ajax=1";
    var percent=0;
</script>

{literal}
<script type="text/javascript">
<!--
var timer=null;
function stopAjaxRequest() {
    setTimeout("clearInterval("+timer+")",2000);
}
function ObjectSize(obj) {
  var len = obj.length ? --obj.length : -1;
    for (var k in obj)
      len++;
  return len+1;
}
$(document).ready(function() {
    update_progressbar();
    timer=setInterval('update_progressbar()', 1500);
});

function update_progressbar() {
    $.ajax({
      type: "POST",
      url: ajaxurl,
      data: "accion=importprogress",
      dataType: 'json',
      success: function(data) {
          percent=parseInt(data.done/$('#total').html()*100);
          $('#done').html(data.done);
          $('#doneok').html(data.ok);
          $('#donefailed').html(data.failed);
          if(data.failed > 0 ) {
            $('#doneFailedli').show();
          }
          if(percent > 100) {
            percent=100;
            $('#done').html($('#total').html());
            $('#doneok').html(data.ok);
          }
          $('#percentValue').html(percent + "%");
          $('#progressValue')[0].style.width=percent + "%";
          
          if(percent > 99) {
            $('#finished').show();
            $('#title').html("Importador finalizado");
            $('#stop').hide();
            $('#doneDate').show();
            $('#doneDateValue').html(data.doneDateValue);
            if(data.timeNeeded)
                $('#doneDateValue')[0].innerHTML+=" <small>("+data.timeNeeded+")</small>";
            /* stop timer */
            //clearTimeout(timer);
            stopAjaxRequest();
          }
          
          $('#info_messages').html(data.info);
          $('#error_messages').html(data.error);
          if (data.info != '') {
            $('#info').show();
          }
          if (data.error != '') {
            $('#error').show();
          }
          
          var numberLongUsernames=ObjectSize(data.longUsernames);
          if( numberLongUsernames > 0 ) {
            //console.log(data.longUsernames);
            $('#longusernames').show();
            var txt='';
            txt+='<table class="dataTable">';
            txt+='<thead><tr><th style="width:50%;">Identificador original</th>';
            txt+='<th>Identificador acortado</th></tr></thead>';
            for (var i in data.longUsernames) {
                //console.log(data.longUsernames[i]);
                txt+="<tr><td>"+data.longUsernames[i][0]+"</td>";
                txt+="<td>" +data.longUsernames[i][1]+"</td></tr>";
            }
            txt+='</table>';
            $('#longusernames_txt').html(txt);
            
            $('#numberLongUsernames').show();
            $('#numberLongUsernames_txt').html(numberLongUsernames);
          }
      }
    });
}
-->
</script>
{/literal}
