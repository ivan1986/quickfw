<?php require dirname(__FILE__).'/info.php' ?>
<?php echo $this->block(Url::C('preForm'), $id); ?>
<form class="scaffoldEdit" action="<?php echo Url::C('edit/'.$id) ?>"
	  method="post" id="form_<?php echo $table ?>" enctype="multipart/form-data">
	<?php echo $this->block(Url::C('preEdit'), $id); ?>
	<dl>
<?php foreach($data as $k=>$v) {?>
	<?php echo $this->block(Url::C('preEditField'.ucfirst($k)), $id); ?>
<?php
	$i = $fields[$k];
	if ( ($id == -1 && !$i->disp->new) || ($id != -1 && !$i->disp->edit))
		continue;
	//по умолчанию первичный ключ не редактируем, но если принудительно установим показ
	if (!$i->primaryKey && !($i->hide === false))
		continue;
	?>
	<?php if ($i->label) { ?><label><?php } ?>
	<dt<?php if (isset($errors[$k])) echo ' class="err"'; ?>>
		<?php echo $i->title ?><?php 
		if ($i->required) {?><span class="required"></span><?php }
	?></dt>
	<dd><?php
		if (isset($methods['editor_'.ucfirst($k)]))
			echo call_user_func($class.'::editor_'.ucfirst($k), $id, $v);
		else
			echo $i->editor($id, $v); ?>
		<?php if ($i->desc) {?><small><?php echo $i->desc ?></small><?php } ?>
	</dd>
	<?php if ($i->label) { ?></label><?php } ?>
<?php } ?>
	<?php echo $this->block(Url::C('preSend'), $id); ?>
	<dt></dt>
	<dd><input type="submit" value="Отправить" name="send" /></dd>
	</dl>
	<?php echo $this->block(Url::C('postEdit'), $id); ?>
	<div></div>
</form>
<?php echo $this->block(Url::C('postForm'), $id); ?>
