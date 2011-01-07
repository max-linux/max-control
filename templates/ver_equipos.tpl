
<h2>Listado de equipos ({$pager->getMAX()})</h2>



<table class="bDataTable"> 
    <tr> 
        <td> 
        <form id="hosts" action="{$urlform}" method="post"> 
          <input type='text' name='Filter' id='Filter' value="{$filter}" /> 
          <input type='submit' name='button' value="Buscar" title="Buscar" /> 
          <input type='submit' name='button' value="Actualizar MAC e IP de todos" title="Actualizar todos" />
          <!--<input type='submit' name='button' value="Limpiar cache WINS" title="Limpiar cache WINS" />-->
          <input type='hidden' name='aula' id="aula" value='{$aula}' />
          <input style="display:none;float:right;" type='button' name='btnDelete' id='btnDelete' 
           value="Borrar seleccionados" title="Borrar seleccionados" onclick="javascript:deleteSelected();"/>
        </form>
        </td> 
    </tr> 
</table> 

<form id="formdeletemultiplehost" name="formdeletemultiplehost" action="{$urlborrar}" method="post">
    <input type='hidden' name='hostnames' id="hostnames" value='' />
</form>

<table class='dataTable'> 
    <thead> 
    <tr>
      <th class=''>Nombre {$pager->getSortIcons('uid')}</th> 
      <th class=''>IP {$pager->getSortIcons('ipHostNumber')} / MAC {$pager->getSortIcons('macAddress')}</th> 
      <th class=''>Aula {$pager->getSortIcons('sambaProfilePath')} 
          <select name='selectaula' id='selectaula' onchange="javascript:aulaFilter(this);">
            <option value='' {if $aula == ''}selected='selected'{/if}>----------</option>
            {foreach from=$aulas key=k item=u}
            <option value='{$u}' {if $aula == $u}selected='selected'{/if}>{$u}</option>
            {/foreach}
          </select>
      </th> 
      <th class=''>Editar</th> 
      <th class=''>Borrar <input title='Seleccionar todos los visibles' class="nomargin" type='checkbox' onchange="javascript:enableAll(this);"/></th> 
    </tr>
    </thead>
 
 
    <tbody> 
      {foreach from=$equipos key=k item=u}
      <tr class='border' id="computer-{$u->hostname()}"> 
        <td class='tcenter'><span>{$u->attr('uid')}</span></td> 
        <td class='tcenter'><span>{$u->attr('ipHostNumber')} / {$u->attr('macAddress')}</span></td> 
        <td class='tcenter'><span>{$u->attr('sambaProfilePath')}</span></td>
        <td class='tcenter'> 
            <a href="{$urleditar}/{$u->hostname()}"><img src="{$baseurl}/img/edit-table.gif" alt="editar" /></a>
        </td>
        <td class='tcenter'> 
            <input type='checkbox' class="hostdel" name="{$u->attr('uid')}" id="{$u->attr('uid')}" onchange="javascript:oncheckboxChange();"/>
        </td>
      </tr>
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
    $.each($('.hostdel'), function(i) { 
        if ($('.hostdel')[i].checked) {
            toDelete.push($('.hostdel')[i].id);
            multiple=true;
        }
    });
    if(multiple)
        $('#btnDelete')[0].style.display='';
    else
        $('#btnDelete')[0].style.display='none';
}

function deleteSelected(){
    var toDelete=new Array();
    $.each($('.hostdel'), function(i) { 
        if ($('.hostdel')[i].checked) {
            toDelete.push($('.hostdel')[i].id);
            multiple=true;
        }
    });
    $('#hostnames')[0].value=toDelete;
    $('#formdeletemultiplehost')[0].submit();
}

function enableAll(obj){
    $.each($('.hostdel'), function(i) { 
        $('.hostdel')[i].checked=obj.checked;
    });
    if(obj.checked)
        $('#btnDelete')[0].style.display='';
    else
        $('#btnDelete')[0].style.display='none';
}

function aulaFilter(obj) {
    $('#aula')[0].value=obj.value;
    document.forms.hosts.submit();
}
-->
</script>
{/literal}


{*
{if $DEBUG}
{debug}
{/if}
*}
