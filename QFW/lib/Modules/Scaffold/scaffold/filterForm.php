<form action="<?php echo Url::C('filter') ?>"
	  method="post" id="filter_<?php echo $table ?>" class="scaffoldFilter">
<?php foreach ($filter as $v) {?>
	<?php echo $v; ?>
<?php } ?>
	<br />
	<input type="submit" name="apply" value="Применить" />
	<input type="submit" name="clear" value="Сбросить" />
</form>