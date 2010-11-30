<?php
$messages_types = array(
	'error' => 'errors',
);
foreach($messages_types as $type => $v)
{
	if (!empty($$v)) foreach($$v as $key=>$message) { ?>
	<span class="<?php echo $type ?>"><b></b><?php echo $message; ?></span>
<?php } ?>
<?php } ?>
