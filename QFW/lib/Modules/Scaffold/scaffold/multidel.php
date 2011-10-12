<?php require dirname(__FILE__).'/info.php' ?>

<?php $cols = 0; // при удалении дополнительных колонок нет ?>
<form action="" method="post" enctype="multipart/form-data">
<table id="table_<?php echo $table ?>" class="scaffoldTable">
<thead>
<tr>
	<?php foreach(current($data) as $key=>$v) {
		$i = $fields[$key];
		if (!$i->disp->multidel)
			continue;
		$cols++;
		?>
		<th><?php echo $i->title ?></th>
	<?php } ?>
</tr>
</thead>
<tbody>
<?php foreach($data as $id=>$row) { $class::setCurRow($row); ?>
<tr>
	<?php foreach($row as $key=>$v) {
		$i = $fields[$key];
		if (!$i->disp->multidel)
			continue;
		?>
		<td<?php if ($i->class) {?> class="<?php echo $i->class===true ? 'col_'.$key : $i->class ?>"<?php } ?>><?php //отображение обычного не связанного поля
			if (isset($methods['display_'.ucfirst($key)]))
				echo call_user_func($class.'::display_'.ucfirst($key), $id, $v);
			else
				echo $i->display($id, $v);
		?></td>
	<?php } ?>
</tr>
<?php } ?>
</tbody>
<tfoot>
<tr><td colspan="<?php echo $cols ?>">
	<input type="submit" name="delete" value="Удалить" />
	<input type="submit" name="cancel" value="Отменить" />
</td></tr>
</tfoot>
</table>
</form>
