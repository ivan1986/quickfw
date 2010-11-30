<?php if (count($errors)) foreach($errors as $k=>$err) { ?>
	<?php if ($err) echo $err; else {?>
	Поле <?php echo $fields[$k]->title ?> имеет некорректное значение<br />
	<?php } ?>
<?php } ?>
<?php echo $this->block(Url::C('preForm'), $id); ?>
<form action="<?php echo Url::C('edit/'.$id) ?>" class="scaffoldEdit"
	  method="post" id="form_<?php echo $table ?>" enctype="multipart/form-data">
	<?php echo $this->block(Url::C('preEdit'), $id); ?>
	<dl>
<?php foreach($data as $k=>$v) {?>
	<?php echo $this->block(Url::C('preEditField'.ucfirst($k)), $id); ?>
<?php
	$i = $fields[$k];
	if ($i->hide)
		continue;
	//по умолчанию первичный ключ не редактируем, но если принудительно установим показ
	if (!$i->primaryKey && !($i->hide === false))
		continue;
	?>
	<label>
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
	</label>
<?php } ?>
	<?php echo $this->block(Url::C('preSend'), $id); ?>
	<dt></dt>
	<dd><input type="submit" value="Отправить" name="send" /></dd>
	</dl>
	<?php echo $this->block(Url::C('postEdit'), $id); ?>
</form>
<?php echo $this->block(Url::C('postForm'), $id); ?>
