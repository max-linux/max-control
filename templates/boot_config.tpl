
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Calendario del programador de arranque</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<form id='programerform' action='{$urlform}' method='post'>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            
        <div class="col-lg-9 alert alert-info">
            <ul style="font-size:18px;">
                <li>En las zonas en rojo el programador esta deshabilitado</li>
                <li>Seleccionar rangos para <u>deshabilitar</u> el programador</li>
                <li>Pulsar sobre rango en rojo para eliminar (pide confirmación)</li>
            </ul>
        </div>

            <div class="panel-body">

                <div data-provide="calendar" id="calendar"></div>

            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
</form>


<div class="modal" id="confirmdelete" style="display: none; z-index: 1050;">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header alert-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Borrar intervalo</h4>
              </div>

            <div class="modal-body" id="confirmMessage">
                ¿Quiere borrar el intervalo entre <span class="dstart"></span> y <span class="dend"></span>?
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete">Borrar</button>
                <button type="button" data-dismiss="modal" class="btn">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
var urlsave="{$urlsave}";
var calendar={$calendar|@json_encode};
</script>

{literal}
<script type="text/javascript">
<!--
var currentYear = new Date().getFullYear();
var CalendarEvents = [];


function DateFormat(m) {
    return m.getFullYear() + "/" +
           ("0" + (m.getMonth()+1)).slice(-2) + "/" +
           ("0" + m.getDate()).slice(-2);/* + " " +
           ("0" + m.getHours()).slice(-2) + ":" +
           ("0" + m.getMinutes()).slice(-2) + ":" +
           ("0" + m.getSeconds()).slice(-2);*/
}

function deleteEvent(event) {
    var dataSource = $('#calendar').data('calendar').getDataSource();

    day = event.date.getTime()/1000;

    newDataSource = [];
    var found = -1;

    for( var i in dataSource ) {        
        start = dataSource[i].startDate.getTime()/1000;
        end = dataSource[i].endDate.getTime()/1000;

        if( day >= start && day <= end ) {
            found = i;
            $('.dstart').html( dataSource[i].startDate.toLocaleDateString() );
            $('.dend').html( dataSource[i].endDate.toLocaleDateString() );

            // console.log( 'FOUND', DateFormat(dataSource[found].startDate), DateFormat(dataSource[found].endDate) );
            continue;
        }

        newDataSource.push( dataSource[i] );
    }

    if ( found >= 0 ) {
        // confirm change
        $('#confirmdelete').modal({
            backdrop: 'static',
            keyboard: true
        })
        .one('click', '#delete', function(ev) {
            console.log( DateFormat(dataSource[found].startDate), DateFormat(dataSource[found].endDate) );

            // FIXME save to server in AJAX request
            $.ajax({
              type: "POST",
              url: urlsave,
              data: { mode: 'del',
                      start: DateFormat(dataSource[found].startDate),
                      end: DateFormat(dataSource[found].endDate),
                     },
              success: function(data) {
                processCalendar(data, true);
              }
            });


        });
    }
}

function saveEvent(e) {
    var dataSource = $('#calendar').data('calendar').getDataSource();
    var event = {
        startDate: e.startDate,
        endDate: e.endDate,
        color: '#dd3923'
    };
    console.log( 'new event', event );
    dataSource.push(event);

    $.ajax({
      type: "POST",
      url: urlsave,
      data: { mode: 'add',
              start: DateFormat(e.startDate),
              end: DateFormat(e.endDate),
             },
      success: function(data) {
        processCalendar(data, true);
      }
    });
}

function processCalendar(cal, refresh) {
    CalendarEvents = [];
    for( var c=0; c < cal.length; c++ ) {
        CalendarEvents.push(event = {
            startDate: new Date(cal[c].start),
            endDate: new Date(cal[c].end),
            color: '#dd3923'
        });
    }
    if(refresh) {
        $('#calendar').data('calendar').setDataSource(CalendarEvents);
    }
    return CalendarEvents;
}


$(function() {

    processCalendar(calendar, false);

    $('#calendar').calendar({ 
        style:'background',
        enableRangeSelection:true,
        language:'es',
        dataSource: CalendarEvents,
        selectRange: function(e) {
            if( e.startDate == e.endDate ) {
                // bug
                return;
            }
            console.log( e.startDate.toLocaleDateString() +  ' -> ' + e.endDate.toLocaleDateString() );
            saveEvent( e );
        },
        clickDay: function(e) {
            deleteEvent( e );
        }
    });
    
});

-->
</script>
{/literal}


