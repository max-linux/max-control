
<h2>Apagado o reinicio de equipos ({$pager->getMAX()})</h2>



<table class="bDataTable"> 
    <tr> 
        <td> 
        <form action="{$urlform}" method="post"> 
          <input type='text' name='Filter' id='Filter' value="{$filter}" /> 
          <input type='submit' name='button' value="Buscar" title="Buscar" /> 
          
          <select style="display:none;float:right;" name='selAction' id='selAction' onchange="javascript:actionSelected();">
            <option value=''>Seleccionar acción...</option>
            {foreach from=$multiple_actions key=k item=u}
            <option value='{$k}'>&nbsp;&nbsp;&nbsp;&nbsp;{$u}</option>
            {/foreach}
          </select>
          
        </form>
        </td> 
    </tr> 
</table> 

<form id="formactionmultiplecomputer" name="formactionmultiplecomputer" action="{$urlformmultiple}" method="post">
    <input type='hidden' name='computers' id="computers" value='' />
    <input type='hidden' name='faction' id="faction" value='' />
</form>

<table class='dataTable'> 
    <thead> 
    <tr>
      <th class=''>Nombre {$pager->getSortIcons('cn')}</th> 
      <th class=''>IP {$pager->getSortIcons('ipHostNumber')} / MAC {$pager->getSortIcons('macAddress')}</th> 
      <th class=''>Aula {$pager->getSortIcons('sambaProfilePath')}</th> 
      <th class=''>Acciones</th> 
      <th class=''>Encender/Reiniciar en</th> 
      <th class=''>Estado</th> 
      <th class=''>Múltiple
       <input title='Seleccionar todos los visibles' class="nomargin" type='checkbox' onchange="javascript:enableAll(this);"/>
      </th>
    </tr>
    </thead>
 
 
    <tbody> 
      {foreach from=$equipos item=u}
      {if $u->teacher_in_computer()}
      <tr class='border' id="computer-{$u->hostname()}"> 
        <td class='tcenter'><span>{$u->attr('cn')}</span></td> 
        <td class='tcenter'><span>{$u->attr('ipHostNumber')} / {$u->attr('macAddress')}</span></td> 
        <td class='tcenter'><span>{$u->get_aula()}</span></td>
        <td class='tcenter'> 
            <a href="{$urlpoweroff}/{$u->hostname()}" title="Apagar equipo {$u->hostname()}"><img src="{$baseurl}/img/poweroff.png" alt="apagar" /></a>
            <a href="{$urlreboot}/{$u->hostname()}" title="Reiniciar equipo {$u->hostname()}"><img src="{$baseurl}/img/reboot.png" alt="reiniciar" /></a>
            <a href="{$urlwakeonlan}/{$u->hostname()}" title="Encender {$u->hostname()}"><img src="{$baseurl}/img/poweron.png" alt="encendido de equipos" /></a>
        </td>
        <td class='tcenter'>
            <a href="{$urlrebootwindows}/{$u->hostname()}" title="Reiniciar equipo '{$u->hostname()}' en Windows"><img src="{$baseurl}/img/windows.png" alt="windows" /></a>
            <a href="{$urlrebootmax}/{$u->hostname()}" title="Reiniciar equipo '{$u->hostname()}' en MAX"><img src="{$baseurl}/img/linux-logo.jpg" alt="MAX" /></a>
            {if $mode == 'admin' && $backharddi_installed}
            <a href="{$urlbackharddi}/{$u->hostname()}" title="Reiniciar equipo '{$u->hostname()}' en Backharddi-NG"><img src="{$baseurl}/img/backharddi.png" alt="Backharddi-NG" /></a>
            {/if}
        </td>
        <td class='tcenter'> 
            <img src="{$baseurl}/status.php?hostname={$u->hostname()}&amp;rnd={$u->rnd()}" alt="calculando..." />
        </td>
        <td class='tcenter'> 
            <input type='checkbox' class="computeraction" name="{$u->hostname()}" id="{$u->hostname()}" onchange="javascript:oncheckboxChange();"/>
        </td>
      </tr>
      {/if}
      {/foreach}

    </tbody> 
</table> 

<!-- paginador -->
{if $pager}
{$pager->getHTML()}
{/if}


{literal}
<script type="text/javascript">
<!--

function oncheckboxChange() {
    var multiple=false;
    var toDelete=new Array();
    $.each($('.computeraction'), function(i) { 
        if ($('.computeraction')[i].checked) {
            toDelete.push($('.computeraction')[i].id);
            multiple=true;
        }
    });
    if(multiple)
        $('#selAction')[0].style.display='';
    else
        $('#selAction')[0].style.display='none';
}

function actionSelected(){
    var toDelete=new Array();
    $.each($('.computeraction'), function(i) { 
        if ($('.computeraction')[i].checked) {
            toDelete.push($('.computeraction')[i].id);
            multiple=true;
        }
    });
    $('#computers')[0].value=toDelete;
    $('#faction')[0].value=$('#selAction :selected').val();
    $('#formactionmultiplecomputer')[0].submit();
}

function enableAll(obj){
    $.each($('.computeraction'), function(i) { 
        $('.computeraction')[i].checked=obj.checked;
    });
    if(obj.checked)
        $('#selAction')[0].style.display='';
    else
        $('#selAction')[0].style.display='none';
}

function rolFilter(obj) {
    $('#role')[0].value=obj.value;
    document.forms.formuser.submit();
}
-->
</script>
{/literal}

{*
{if $DEBUG}
{debug}
{/if}
*}
