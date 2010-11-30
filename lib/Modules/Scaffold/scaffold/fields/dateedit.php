<?php
Hlp::addCSS('timepicker/jquery-ui-1.8.custom.css');
Hlp::addJs('js/jquery.js');
Hlp::addJs('timepicker/jquery-ui-1.8.custom.min.js');
Hlp::addJs('timepicker/jquery-ui-timepicker-addon.min.js');
Hlp::addJs('timepicker/jquery-ui-datepicker-ru.js');
?><input type="text" name="<?php echo $name ?>" default="<?php echo $value?>" class="datepicker" />
<?php Hlp::sJSe() ?>
$('.datepicker').datetimepicker({
	showSecond: true,
	dateFormat: 'yy-mm-dd',
	timeFormat: 'hh:mm:ss',
	timeOnlyTitle: 'Выберите время',
	timeText: 'Время',
	hourText: 'Час',
	minuteText: 'Мин',
	secondText: 'Сек',
	currentText: 'Теперь',
});
<?php Hlp::eJSe() ?>
