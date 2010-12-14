<?php require dirname(__FILE__).'/info.php' ?>
<?php if (isset($parent)) echo $parent; ?>

<?php if (isset($filter)) {
	include 'filterForm.php';
} ?>

<?php echo $pager; ?>

<?php if (count($data)>0) { $cols = 3 + (empty($actions) ? 0 : 1); // с чекбоксами,  редактирование+удаление, действия
//считаем колонки
foreach(current($data) as $key=>$v) 
	$cols += $fields[$key]->disp->list ? 1 : 0;
?>
<form action="" method="post" enctype="multipart/form-data">
<table id="table_<?php echo $table ?>" class="scaffoldTable">
<thead>
<tr>
	<td colspan="<?php echo $cols ?>">
	<input type="button" class="button" value="Добавить" onclick="window.location.href='<?php echo Url::C('edit/-1') ?>'" />
	<input type="submit" name="edit" value="Редактировать выбранных" />
	<input type="submit" name="delete" value="Удалить выбранных" />
	<?php echo $this->block(Url::C('multiPost')); ?>
	</td>
</tr>
<tr>
	<th><input type="checkbox" class="multiSelect" /></th>
	<?php foreach(current($data) as $key=>$v) {
		$i = $fields[$key];
		if (!$i->disp->list)
			continue;
		?>
		<th><a href="<?php echo Url::C('sort/'.$key) ?>"><?php echo $i->title ?></a>
			<?php if (isset($order) && $order['field'] == $key) { ?><span class="scaffoldSort">
				<?php if ($options['sortImages']) {?>
					<img src="/built-in/<?php echo $order['direction']=='ASC' ? 'az' : 'za' ?>.png"
						alt="<?php echo $order['direction']=='ASC' ? '↓' : '↑' ?>" />
				<?php } else { ?>
					<span><?php echo $order['direction']=='ASC' ? '↓' : '↑' ?></span>
				<?php } ?>
			</span><?php } ?>
		</th>
	<?php } ?>
	<th colspan="2"><a href="<?php echo Url::C('edit/-1') ?>">доб.</a></th>
	<?php if (count($actions)) { ?><th>действия</th><?php } ?>
</tr>
</thead>
<tbody>
<?php foreach($data as $id=>$row) { ?>
<tr>
	<td><input type="checkbox" name="id[]" value="<?php echo $id ?>" /></td>
	<?php foreach($row as $key=>$v) {
		$i = $fields[$key];
		if (!$i->disp->list)
			continue;
		?>
		<td<?php if ($i->class) {?> class="<?php echo $i->class===true ? 'col_'.$key : $i->class ?>"<?php } ?>><?php
			if (isset($methods['display_'.ucfirst($key)]))
				echo call_user_func($class.'::display_'.ucfirst($key), $id, $v);
			else
				echo $i->display($id, $v);
		?></td>
	<?php } ?>
	<td><a href="<?php echo Url::C('edit/'.$id) ?>">ред.</a></td>
	<td><a onclick="return confirm('Удалить?')" href="<?php echo
		Url::C('delete/'.$row[$primaryKey]) ?>">уд.</a></td>
	<?php if (count($actions)) {?><td><?php foreach ($actions as $tit => $uri) { ?>
		<a href="<?php echo Url::C($uri.'/'.$id) ?>"><?php echo $tit ?></a>
	<?php } ?></td><?php } ?>
</tr>
<?php } ?>
</tbody>
<tfoot>
<tr><td colspan="<?php echo $cols ?>">
	<input type="button" class="button" value="Добавить" onclick="window.location.href='<?php echo Url::C('edit/-1') ?>'" />
	<input type="submit" name="edit" value="Редактировать выбранных" />
	<input type="submit" name="delete" value="Удалить выбранных" />
	<?php echo $this->block(Url::C('multiPost')); ?>
</td></tr>
</tfoot>
</table>
</form>
<?php } else { ?>
	Записей нет
	<?php if (!$options['addOnBottom']) {?>
	<a href="<?php echo Url::C('edit/-1') ?>">добавить</a>
	<?php } ?>
<?php } ?>
<?php echo $pager; ?>
<?php echo $this->block(Url::C('postTable')); ?>

<?php if ($options['addOnBottom']) {?>
<div>
<?php if ($options['addOnBottom']!==true) echo $options['addOnBottom'] ?>
<?php echo $this->block(Url::C('new')) ?>
<p>&nbsp;</p>
</div>
<?php } ?>
