<?php
return array (
	'yimaLocali' => array(
        # Locale Detector Strategies, Implemented DetectorInterface
		'detector' => array(
            #  Used By Default AggregateStrategyFactory (yimaLocali.Detector.Strategy)
            'aggregate' => array(
                'strategies' => array(
                    //'Registered\Service\Or\ClassName' => -10,
                    # or
                    /*
                    array(
                        'object'   => new StrategyObject(),
                        'priority' => -10
                    ),
                    */

                    // default ordered strategies
                    'yimaLocali\Strategy\UriPathStrategy' => 90,
                    'yimaLocali\Strategy\CookieStrategy'  => 80,
                    /*
                    array(
                        'object'   => new \yimaLocali\Detector\Strategy\RestrictedLocaleStrategy(),
                        'priority' => -1000
                    ),
                    */
                ),
            ),
        ),

        # content of this key will pass to LocaleAvailable on Bootstrap
        'available_locales' => array(
            'default'    => 'en_US',
            'locales'  => array(
                'en_US',
                'fa_IR',
            ),
            'aliases'    => array(
                'en'    => 'en_US',
                'fa'    => 'fa_IR',
                'farsi' => 'fa',
            ),
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
