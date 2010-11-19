<?php echo Hlp::outHead('h','1','2'); ?><br>
Это урл <?php echo Url::site('test'); ?><br>
<?php Hlp::addJS('file.js'); ?>
<?php Hlp::addJS('file.js'); ?>
<?php Hlp::addCSS('file.css'); ?>
<?php Hlp::addCSS('file.css'); ?>
<?php $this->begin()->strtoupper(); ?>
zzzzzzzzzzzzzzzzzzz
<?php $this->end(); ?>

<?php Hlp::getHead(false,'h');?>HEAD<?php Hlp::getHead(true,'h');?>
А сюда у нас подключен блок test/index<br />
<?php echo $this->block('default.test', 11, 34, 'sdfsf');?><br />
<?php echo $this->block('default.test.index(1,2,3,4)');?><br />
<?php echo $this->block('test(\'34\\\'23\',342,\'sfsdf,sfs\'\'df\',"234545\"fdsgdf")');?><br />
<?php echo $this->block("test(lsfdjhskjhg sjskh ,'jks'dfkjhsdf',jsh,kjs sdhf)");?><br />
<?php echo $this->block('test');?><br />
А вот тут он закончился
<?php Hlp::getHead(false,'h');?>HEAD<?php Hlp::getHead(true,'h');?>
