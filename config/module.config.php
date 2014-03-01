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
                    'yimaLocali\Detector\UriPathStrategy' => 90,
                    'yimaLocali\Detector\CookieStrategy'  => 80,
                    array(
                        'object'   => new \yimaLocali\Detector\RestrictLocaleStrategy(),
                        'priority' => -1000
                    ),
                ),
            ),
        ),

        # content of this key will pass to LocaleSupport(class) on Bootstrap
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

		# if you need detected locale before continue of running application
		'throw_exceptions' => false,
	),

    // Translator settings
	'translator' => array(
		'translation_file_patterns' => array(
			array(
				'type'     => 'phparray',
				'base_dir' => __DIR__ . '/../language',
				'pattern'  => '%s.php',
                //yimaLocali translations using as default Text Domain
                #'text_domain' => 'default',
            ),
		),
	),
);
