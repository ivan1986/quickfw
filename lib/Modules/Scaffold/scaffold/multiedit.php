<?php require dirname(__FILE__).'/info.php' ?>

<?php $cols = 0; // при удалении дополнительных колонок нет ?>
<form action="" method="post" enctype="multipart/form-data">
<table id="table_<?php echo $table ?>" class="scaffoldTable">
<thead>
<tr>
	<?php foreach(current($data) as $key=>$v) {
		$i = $fields[$key];
		if ($i->hide)
			continue;
		if (!$i->primaryKey && !($i->hide === false))
			continue;
		//по умолчанию первичный ключ не редактируем, но если принудительно установим показ
		$cols++;
		?>
		<th><?php echo $i->title ?></th>
	<?php } ?>
</tr>
</thead>
<tbody>
<?php foreach($data as $id=>$row) { ?>
<tr>
	<?php foreach($row as $key=>$v) {
		$i = $fields[$key];
		if ($i->hide)
			continue;
		if (!$i->primaryKey && !($i->hide === false))
			continue;
		//по умолчанию первичный ключ не редактируем, но если принудительно установим показ
		?>
		<td<?php if ($i->class) {?> class="<?php echo $i->class===true ? 'col_'.$key : $i->class ?>"<?php } ?>>
		<?php
		if (isset($methods['editor_'.ucfirst($key)]))
			echo call_user_func($class.'::editor_'.ucfirst($key), $id, $v);
		else
			echo $i->editor($id, $v);
		?></td>
	<?php } ?>
</tr>
<?php } ?>
</tbody>
<tfoot>
<tr><td colspan="<?php echo $cols ?>">
	<input type="submit" name="edit" value="Редактировать" />
	<input type="submit" name="cancel" value="Отменить" />
</td></tr>
</tfoot>
</table>
</form>
