<?php /* Smarty version 2.6.22, created on 2010-05-18 10:34:16
         compiled from editar_equipo.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'debug', 'editar_equipo.tpl', 75, false),)), $this); ?>


<h3><?php echo $this->_tpl_vars['action']; ?>
 equipo <u><?php echo $this->_tpl_vars['hostname']; ?>
</u></h3>

<form action='<?php echo $this->_tpl_vars['urlform']; ?>
' method='post'> 
    <table class='formTable'> 
    <tr> 
        <td class='tright'><span class="ftitle">Dirección MAC:</span></td>
        <td><input type='text' class='inputText' name='macAddress' value="<?php echo $this->_tpl_vars['u']->attr('macAddress'); ?>
"></td>
    </tr>
    
    <tr> 
        <td class='tright'><span class="ftitle">Dirección IP:</span></td>
        <td><input type='text' class='inputText' name='ipHostNumber' value="<?php echo $this->_tpl_vars['u']->attr('ipHostNumber'); ?>
"></td>
    </tr> 
    
    <tr> 
        <td class='tright'><span class="ftitle">Archivo de arranque:</span></td>
        <td><input type='text' class='inputText' name='bootFile' value="<?php echo $this->_tpl_vars['u']->attr('bootFile'); ?>
"> (ej: /pxelinux.0)</td>
    </tr> 
    
    <!--<tr> 
        <td class='tright'><span class="ftitle">Parámetros de arranque:</span></td>
        <td><input type='text' class='inputText' name='bootParameter' value="<?php echo $this->_tpl_vars['u']->attr('bootParameter'); ?>
"> (variable=valor)</td>
    </tr> -->


    <tr>
        <td class='tright'><span class='ftitle'>Grupo de arranque:</span></td> 
        <td> 
            <select name='sambaProfilePath' id='sambaProfilePath' > 
                <option value=''></option> 
                <?php $_from = $this->_tpl_vars['aulas']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['o']):
?>
                <option value='<?php echo $this->_tpl_vars['o']; ?>
' <?php if ($this->_tpl_vars['o'] == $this->_tpl_vars['u']->attr('sambaProfilePath')): ?>selected<?php endif; ?>><?php echo $this->_tpl_vars['o']; ?>
</option>
                <?php endforeach; endif; unset($_from); ?>
            </select> 
            
            <!--
            <input type='text' class='inputText' name='new_sambaProfilePath' id='new_sambaProfilePath' value=""/>
            <input class='inputButton' type='button' name='añadir' value="Añadir Grupo" alt="Añadir" onclick="javascript:append_sambaProfilePath(this.value);"/> 
            -->
        </td> 
    <tr>
 
    </tr> 
    <tr> 
        <td></td> 
        <td> 
        <input class='inputButton' type='submit' name='<?php echo $this->_tpl_vars['action']; ?>
' value="Guardar" alt="Guardar" /> 
        <input type='hidden' name='hostname' value='<?php echo $this->_tpl_vars['hostname']; ?>
' />
        </td> 
    </tr> 
    </table> 
    </form> 

<?php echo '
<script type="text/javascript">
function append_sambaProfilePath() {
        var sel = document.getElementById("sambaProfilePath");
        var text = document.getElementById("new_sambaProfilePath");
        var opt = document.createElement("option");
        var txt =document.createTextNode(text.value);
        opt.appendChild(txt);
        opt.setAttribute("value",text.value);
        sel.appendChild(opt);
        sel.selectedIndex=sel.length-1;
        console.log("select index="+sel.length-1);
        text.value=\'\';
        return false;
}
</script>
'; ?>


<?php if ($this->_tpl_vars['pruebas']): ?>
<?php echo smarty_function_debug(array(), $this);?>

<?php endif; ?>
