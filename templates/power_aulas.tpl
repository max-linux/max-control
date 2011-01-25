
<h2>Apagado o reinicio de aulas ({$pager->getMAX()})</h2>



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

<form id="formactionmultipleaulas" name="formactionmultipleaulas" action="{$urlformmultiple}" method="post">
    <input type='hidden' name='aulas' id="aulas" value='' />
    <input type='hidden' name='faction' id="faction" value='' />
</form>

<table class='dataTable'> 
    <thead> 
    <tr>
      <th class=''>Nombre {$pager->getSortIcons('cn')}</th> 
      <th class=''>Equipos en este aula {$pager->getSortIcons('cachednumcomputers')}</th>
      <th class=''>Acciones</th> 
      <th class=''>Encender/Reiniciar en</th> 
      <th class=''>Múltiple
       <input title='Seleccionar todos los visibles' class="nomargin" type='checkbox' onchange="javascript:enableAll(this);"/>
      </th>
    </tr>
    </thead>
 
 
    <tbody> 
      {foreach from=$aulas item=u}
      <tr class='border' id="{$u->safecn()}"> 
      {if $u->teacher_in_aula()}
        <td class='tcenter'><span>{$u->cn}</span></td> 
        <td class='tcenter'><span>{$u->get_num_computers()}</span></td>
        <td class='tcenter'> 
            {if $u->get_num_computers() > 0 }
            <a href="{$urlpoweroff}/{$u->cn}" title="Apagar aula '{$u->cn}'"><img src="{$baseurl}/img/poweroff.png" alt="apagar" /></a>
            <a href="{$urlreboot}/{$u->cn}" title="Reiniciar aula '{$u->cn}'"><img src="{$baseurl}/img/reboot.png" alt="reiniciar" /></a>
            <a href="{$urlwakeonlan}/{$u->cn}" title="Encender '{$u->cn}'"><img src="{$baseurl}/img/poweron.png" alt="encendido de equipos" /></a>
            {/if}
        </td>
        <td class='tcenter'>
            {if $u->get_num_computers() > 0 }
            <a href="{$urlrebootwindows}/{$u->cn}" title="Reiniciar aula '{$u->cn}' en Windows"><img src="{$baseurl}/img/windows-logo.jpg" alt="windows" /></a>
            <a href="{$urlrebootmax}/{$u->cn}" title="Reiniciar aula '{$u->cn}' en MAX"><img src="{$baseurl}/img/linux-logo.jpg" alt="MAX" /></a>
            <a href="{$urlbackharddi}/{$u->cn}" title="Reiniciar equipo '{$u->cn}' en Backharddi-NG"><img src="{$baseurl}/img/backharddi-logo.jpg" alt="Backharddi-NG" /></a>
            {/if}
        </td>
        <td class='tcenter'> 
            {if $u->get_num_computers() > 0 }
            <input type='checkbox' class="aulaaction" name="{$u->cn}" id="{$u->cn}" onchange="javascript:oncheckboxChange();"/>
            {else}
            aula vacía
            {/if}
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
    $.each($('.aulaaction'), function(i) { 
        if ($('.aulaaction')[i].checked) {
            toDelete.push($('.aulaaction')[i].id);
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
    $.each($('.aulaaction'), function(i) { 
        if ($('.aulaaction')[i].checked) {
            toDelete.push($('.aulaaction')[i].id);
            multiple=true;
        }
    });
    $('#aulas')[0].value=toDelete;
    $('#faction')[0].value=$('#selAction :selected').val();
    $('#formactionmultipleaulas')[0].submit();
}

function enableAll(obj){
    $.each($('.aulaaction'), function(i) { 
        $('.aulaaction')[i].checked=obj.checked;
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
