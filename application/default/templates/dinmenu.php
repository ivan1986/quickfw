<style type="text/css">
ul#ttt ul, ul#ttt li {
	margin:-1px;
	padding:1px;
}
ul#ttt li {display:inline-block; position:relative; border:1px solid red;}
ul#ttt li a {display:block; width:200px}
ul#ttt li ul {
position:absolute;
top:25px;
left:10px;
}
ul#ttt li ul li {
display:block; position:relative; 
}
ul#ttt li ul {display:none}
ul#ttt li:hover > ul {display:block}
ul#ttt li ul li ul {
position:absolute;
top:0px;
left:95%;
}

</style>

<?php echo $this->block('helper.nav.menuTree', array(
	array(
		'title' => 'afgdfgdfggf',
		'url' => Url::A(),
	),
	array(
		'title' => 'asdffgdfgdfgdf',
		'url' => Url::A(),
		'childNodes' => array(
			array(
				'title' => 'askj',
				'url' => Url::A(),
				'childNodes' => array(
					array(
						'title' => 'afhgfgjgf',
						'url' => Url::A(),
						'childNodes' => array(
							array(
								'title' => 'afhgfgjgf',
								'url' => Url::A(),
							),
						),
					),
					array(
						'title' => 'afhgfgjgf',
						'url' => Url::A(),
						'childNodes' => array(
							array(
								'title' => 'afhgfgjgf',
								'url' => Url::A(),
							),
						),
					),
					array(
						'title' => 'afhgfgjgf',
						'url' => Url::A(),
						'childNodes' => array(
							array(
								'title' => 'afhgfgjgf',
								'url' => Url::A(),
							),
						),
					),
				),
			),
			array(
				'title' => 'asdfgsdf',
				'url' => Url::A(),
				'childNodes' => array(
					array(
						'title' => 'afhgfgjgf',
						'url' => Url::A(),
						'childNodes' => array(
							array(
								'title' => 'afhgfgjgf',
								'url' => Url::A(),
							),
						),
					),
					array(
						'title' => 'afhgfgjgf',
						'url' => Url::A(),
						'childNodes' => array(
							array(
								'title' => 'afhgfgjgf',
								'url' => Url::A(),
							),
						),
					),
					array(
						'title' => 'afhgfgjgf',
						'url' => Url::A(),
						'childNodes' => array(
							array(
								'title' => 'afhgfgjgf',
								'url' => Url::A(),
							),
						),
					),
				),
			),
			array(
				'title' => 'asdf',
				'url' => Url::A(),
			),
		),
	),
	array(
		'title' => 'a',
		'url' => Url::A(),
	),
), 'ttt');?>
