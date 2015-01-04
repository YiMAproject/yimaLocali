<?php
return [
	'yimaLocali' => [
        # Locale Detector Strategies, Implemented DetectorInterface
		'detector' => [
            #  Used By Default AggregateStrategyFactory (yimaLocali.Detector.Strategy)
            'aggregate' => [
                'strategies' => [
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
                    [
                        'object'   => new \yimaLocali\Detector\RestrictLocaleStrategy(),
                        'priority' => -1000
                    ],
                ],
            ],
        ],

        # content of this key will pass to LocaleSupport(class) on Bootstrap
        'available_locales' => [
            'default'    => 'en_US',
            'locales'  => [
                'en_US', // locale with no options, will use system default
                'nl_BE', // locale with no options, will use system default
                'ar_AE' => [
                    'calendar'          => 'islamic',
                ],
                'fa_IR' => [
                    # these are locale options default data, used or set by other modules
                    'default_time_zone' => 'Asia/Tehran',
                    'calendar'          => 'persian',

                    'rtl'               => true, // used by isRtl() plugin
                ],
            ],
            'aliases'    => [
                'en'    => 'en_US',
                'ar'    => 'ar_AE',
                'nl'    => 'nl_BE',
                'fa'    => 'fa_IR',
                'farsi' => 'fa',
            ],
        ],

		# if you need detected locale before continue of running application
		'throw_exceptions' => false,
	],

    # Plugins services for locale() plugin manager
    # you can catch this plugins within view and controller:
    #  $this->locale()->test() or $this->plugin('test', $options);
    'yima_locali_plugins' => [
        #'invokables' => array(),
        'factories'  => [
            'isRtl' => function ($sl) {
                $sm = $sl->getServiceLocator();
                $locale = $sm->get('locale.detected');
                $lData  = \yimaLocali\Service\LocaleRegistry::getLocaleData($locale);

                $result = (!empty($lData) && isset($lData['rtl']) && $lData['rtl']);
                return function() use ($result) {
                    return $result;
                };
            }
        ],
    ],
];
