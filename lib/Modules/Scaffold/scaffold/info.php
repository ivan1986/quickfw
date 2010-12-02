<?php foreach($messages as $type => $messages_t) {
	if (!empty($messages_t)) foreach($messages_t as $key=>$message) { ?>
	<span class="<?php echo $type ?>"><b></b><?php echo $message; ?></span>
	<?php } ?>
<?php } ?>
<?php
	//вывели сообщения, очищаем
	$session['messages']=array();
?>
