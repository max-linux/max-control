<?php /* Smarty version 2.6.22, created on 2010-05-08 21:37:48
         compiled from doc.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'debug', 'doc.tpl', 22, false),)), $this); ?>


<h2>Documentación</h2>

<ul>
<?php $_from = $this->_tpl_vars['files']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['f']):
?>
    <?php if ($this->_tpl_vars['f']['dir'] == 1): ?>
      <h4><?php echo $this->_tpl_vars['f']['name']; ?>
</h4>
    <?php else: ?>
        <li class="file">
            <a href="<?php echo $this->_tpl_vars['f']['rel']; ?>
"><?php echo $this->_tpl_vars['f']['pname']; ?>
</a>
        </li>
    <?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
</ul>



<?php if (pruebas): ?>
<br/><br/><br/><br/>
<hr/>
<?php echo smarty_function_debug(array(), $this);?>

<?php endif; ?>