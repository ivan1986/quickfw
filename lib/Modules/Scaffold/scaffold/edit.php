<?php if (count($errors)) foreach($errors as $k=>$err) { ?>
	<?php if ($err) echo $err; else {?>
	Поле <?php echo $fields[$k]->title ?> имеет некорректное значение<br />
	<?php } ?>
<?php } ?>
<form action="<?php echo Url::C('edit/'.$id) ?>" class="scaffoldEdit"
	  method="post" id="form_<?php echo $table ?>" enctype="multipart/form-data">
	<dl>
<?php foreach($data as $k=>$v) {
	$i = $fields[$k];
	if ($i->hide)
		continue;
	//по умолчанию первичный ключ не редактируем, но если принудительно установим показ
	if (!$i->primaryKey && !($i->hide === false))
		continue;
	?>
	<dt<?php if (isset($errors[$k])) echo ' class="err"'; ?>>
			<?php echo $i->title ?></dt>
	<dd><?php
		if (isset($methods['editor_'.ucfirst($k)]))
			echo call_user_func($class.'::editor_'.ucfirst($k), $id, $v);
		else
			echo $i->editor($id, $v); ?>
		<?php if ($i->desc) {?><small><?php echo $i->desc ?></small><?php } ?>
	</dd>
<?php } ?>
	<dt></dt>
	<dd><input type="submit" value="Отправить" name="send" /></dd>
	</dl>
</form>
