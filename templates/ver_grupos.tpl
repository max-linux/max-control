
<h2>Listado de grupos ({$pager->getMAX()})</h2>



<table class="bDataTable"> 
    <tr> 
        <td> 
        <form id="group" action="{$urlform}" method="post"> 
          <input type='text' name='Filter' id='Filter' value="{$filter}" /> 
          <input type='submit' name='button' value="Buscar" title="Buscar" /> 
          <input type='submit' name='button' value="Añadir grupo" title="Añadir grupo" />
          <input style="display:none;float:right;" type='button' name='btnDelete' id='btnDelete' 
           value="Borrar seleccionados" title="Borrar seleccionados" onclick="javascript:deleteSelected();"/>
        </form>
        
        </td> 
    </tr> 
</table> 

<form id="formdeletemultiplegroup" name="formdeletemultiplegroup" action="{$urlborrar}" method="post">
    <input type='hidden' name='groupnames' id="groupnames" value='' />
</form>

<table class='dataTable'> 
    <thead> 
      <tr>
      <th class=''>Nombre {$pager->getSortIcons('cn')}</th> 
      <th class=''>Miembros {$pager->getSortIcons('numUsers')}</th>
      <th class=''>Borrar <input title='Seleccionar todos los visibles' class="nomargin" type='checkbox' onchange="javascript:enableAll(this);"/></th> 
      </tr>
    </thead>
 
 
    <tbody> 
      {foreach from=$groups key=k item=u}
      <tr class='border' id="group-{$u->attr('cn')}"> 
        <td class='tcenter'><span>{$u->attr('cn')}</span></td> 
        <td class='tcenter'><span>
                        {$u->attr('numUsers')}
                        <a href="{$urlmiembros}/{$u->cn}"><img src="{$baseurl}/img/edit-table.gif" alt="editar" /></a>
                        </span>
        </td> 
        <td class='tcenter'> 
            {*<a href="{$urlborrar}/{$u->cn}"><img src="{$baseurl}/img/delete.gif" alt="borrar" /></a>*}
            <input type='checkbox' class="groupdel" name="{$u->attr('cn')}" id="{$u->attr('cn')}" onchange="javascript:oncheckboxChange();"/>
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
    $.each($('.groupdel'), function(i) { 
        if ($('.groupdel')[i].checked) {
            toDelete.push($('.groupdel')[i].id);
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
    $.each($('.groupdel'), function(i) { 
        if ($('.groupdel')[i].checked) {
            toDelete.push($('.groupdel')[i].id);
            multiple=true;
        }
    });
    $('#groupnames')[0].value=toDelete;
    $('#formdeletemultiplegroup')[0].submit();
}

function enableAll(obj){
    $.each($('.groupdel'), function(i) { 
        $('.groupdel')[i].checked=obj.checked;
    });
    if(obj.checked)
        $('#btnDelete')[0].style.display='';
    else
        $('#btnDelete')[0].style.display='none';
}

-->
</script>
{/literal}



{*
{if $DEBUG}
{debug}
{/if}
*}
