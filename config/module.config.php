<?php
return array (
	'yimaLocali' => array(
		'default'    => 'en_US',
		'supported'  => array(
			'en_US', 
			'fa_IR',
		),
		'aliases'    => array(
			'en'    => 'en_US', 
			'fa'    => 'fa_IR',
			'farsi' => 'fa',
		),
		'strategies' => array(
			# dar haalate restricted ['yimaLocali']['default'] be onvaane locale e konooni set mishavad
			//-10 => 'yimaLocali\Strategy\RestrictedStrategy',
			# or
			/*
			array(
				'invokable' => 'yimaLocali\Strategy\RestrictedStrategy',
				'priority'  => -10
			),
			*/
		),
		# if you need application have a detected locale to run, default false
		'throw_exception' => false,
	),
		
	'translator' => array(
		'translation_file_patterns' => array(
			array(
				'type'     => 'phparray',
				'base_dir' => __DIR__ . '/../language',
				'pattern'  => '%s.php',
			),
		),
	),
);
