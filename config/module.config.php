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

    # Plugins services for locale() plugin manager
    # you can catch this plugins within view and controller:
    #  $this->locale()->test() or $this->plugin('test', $options);
    'yima_locali_plugins' => array(
//        'invokables' => array(),
        /*'factories'  => array(
            'test' => function ($sm) {
                    return 'This is test';
            }
        ),*/
    ),
);
