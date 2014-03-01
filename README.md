yimaLocali
==========

*this module is part of Yima Application Framework*

[zf2 module] Detection of locale through different strategies.

<a name="config"></a>Configuration
-----------

This is most default configuration of module with description of each key as comment.

```php
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
```

How To Get Detected Locale?
-----------

#### From Service Manager

```php
class LocaleNeededService implement
    ServiceLocatorAwareInterface
{
    public function getLocale()
    {
        // get serviceManager
        $sm = $this->getServiceLocator();

        // we can get current locale from service manager
        return $sm->get('locale.detected');
    }

    // ...
}
```

#### From Locale Intl Extension

if we have Intl extension installed on server after detection of locale from strategies it will set to "Locale" class as defaultLocale

```php
Locale::getDefault();
```

#### From View Helper

```php
echo $this->locale()->getLocale(); // exp. "en_US"

echo $this->locale(); // exp. "en_US"

echo $this->locale()->getLanguage(); // en
echo $this->locale()->getRegion();   // US

```

#### From Controller Plugin

```php
use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        echo $this->locale()->getLocale(); // exp. "en_US"

        echo $this->locale(); // exp. "en_US"

        echo $this->locale()->getLanguage(); // en
        echo $this->locale()->getRegion();   // US
    }
}

```

Locale Detected From Aggregate Default Strategy, What About Else ?
-----------

If you want using your own complete different strategy for Locale Detection do the same as below.

#### Make your detection strategy class

```php
use yimaLocali\Detector\DetectorInterface;

class MyLocaleStrategy implements
    DetectorInterface
{
    /**
     * Get locale from configs default setting
     *
     * @return string
     */
    public function getLocale()
	{
	    // get Locale from default locale module config
	    return LocaleSupport::getDefaultLocale();
	}
}

```

#### Make it exclusive default detection service

you must register a service with name "yimaLocali.Detector.Strategy" it will replaced with default AggregateDetector


Register The Strategy To Default Aggregate
-----------

see [configuration](#config) settings

Installation
-----------

Composer installation:

require ```rayamedia/yima-locali``` in your ```composer.json```

Or clone to modules folder

Enable module in application config


## Support ##
To report bugs or request features, please visit the [Issue Tracker](https://github.com/RayaMedia/yimaLocali/issues).

*Please feel free to contribute with new issues, requests and code fixes or new features.*
