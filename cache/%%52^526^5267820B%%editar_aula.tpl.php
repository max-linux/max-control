<?php /* Smarty version 2.6.22, created on 2010-05-18 11:38:36
         compiled from editar_aula.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'debug', 'editar_aula.tpl', 67, false),)), $this); ?>


<h3>Administration del aula  <span class='stitle'><?php echo $this->_tpl_vars['aula']; ?>
</span></h3> 
 

 
<table> 
<thead> 
	<tr> 
		<th class="tleft">Profesores del aula</th> 
		<th></th> 
		<th class="tleft">Resto de profesores</th> 
	</tr> 
</thead> 
<tbody> 
<tr> 
    <td rowspan="2"> 
    <form action='<?php echo $this->_tpl_vars['urlform']; ?>
' method='post'> 
        <select name='deluser' size='15' multiple> 
            <?php $_from = $this->_tpl_vars['miembros']['ingroup']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['o']):
?>
                <option value="<?php echo $this->_tpl_vars['o']; ?>
"><?php echo $this->_tpl_vars['o']; ?>
</option> 
            <?php endforeach; endif; unset($_from); ?>
        </select> 
    </td> 
 
    <td> 

    <input class='inputButton' type='image' name='delfromgroup'
            value="Quitar"
            src='<?php echo $this->_tpl_vars['baseurl']; ?>
/img/right.gif'
            title="Quitar profesor del aula"
            alt="Quitar profesor del aula" /> 
    <input type="hidden" name="aula" value="<?php echo $this->_tpl_vars['aula']; ?>
"/> 
    </form>



    <br /> 
    <br /> 




    <form action='<?php echo $this->_tpl_vars['urlform']; ?>
' method='post'> 
    <input class='inputButton' type='image' name='addtogroup'
            value="Añadir usuarios al grupo"
            src='<?php echo $this->_tpl_vars['baseurl']; ?>
/img/left.gif'
            title="Añadir profesor al aula"
            alt="Añadir profesor al aula" /> 
    </td> 
 
	<td> 
        <select name='adduser' size='15' multiple> 
            <?php $_from = $this->_tpl_vars['miembros']['outgroup']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['o']):
?>
                <option value="<?php echo $this->_tpl_vars['o']; ?>
"><?php echo $this->_tpl_vars['o']; ?>
</option> 
            <?php endforeach; endif; unset($_from); ?>
        </select> 
		<input type="hidden" name="aula" value="<?php echo $this->_tpl_vars['aula']; ?>
"> 
    </form> 
	</td> 
</tr> 
</tbody> 
</table>


<?php if ($this->_tpl_vars['pruebas']): ?>
<?php echo smarty_function_debug(array(), $this);?>

<?php endif; ?>