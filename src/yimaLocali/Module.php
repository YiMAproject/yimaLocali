<?php
namespace yimaLocali;

use yimaLocali\Detector\AggreagateStrategy;
use yimaLocali\Detector\AggregateDetectorInterface;
use yimaLocali\Service\LocaleSupport;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\LocatorRegisteredInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;

use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Mvc\MvcEvent;

use yimaLocali\Detector\DetectorInterface;

use Locale as StdLocale;

class Module implements
    InitProviderInterface,
    ServiceProviderInterface,
    ConfigProviderInterface,
    AutoLoaderProviderInterface,
    LocatorRegisteredInterface
{
    const EVENT_IDENTIFIER = 'yimaLocali\Module';

    /**
     * Initialize workflow
     *
     * @param  ModuleManagerInterface $manager
     *
     * @return void
     */
    public function init(ModuleManagerInterface $moduleManager)
    {
        /** @var $moduleManager \Zend\ModuleManager\ModuleManager */
        $events        = $moduleManager->getEventManager();
        $sharedEvents  = $events->getSharedManager();
        $sharedEvents->attach(
            'Zend\Mvc\Application',
            MvcEvent::EVENT_BOOTSTRAP,
            array($this, 'onBootstraping'),
            1000000
        );

        // attach default events
        $sharedEvents->attach(
            self::EVENT_IDENTIFIER,
            LocaleEvent::EVENT_LOCALE_DETECTED,
            array($this, 'systemInitializers'),
            1000000
        );
    }

    public function systemInitializers(LocaleEvent $event)
    {
        $locale = $event->getLocale();

        if (class_exists('\Locale', true)) {
            // in some host Locale as a PECL maybe not installed
            StdLocale::setDefault($locale);
        }

        # reset locale if set before
        $sm = $event->getServiceLocator();

        $translator = $sm->get('translator');
        $translator->setLocale($locale);
    }

    /**
     * Get Locale From Default Registered Detector Service
     *
     * @param MvcEvent $event
     *
     * @throws \Exception
     */
    public function onBootstraping(MvcEvent $event)
    {
        $app = $event->getApplication();
        $sm  = $app->getServiceManager();

        $detector = $sm->get('yimaLocali.Detector.Strategy');
        if (!$detector instanceof DetectorInterface) {
            throw new \Exception(
                sprintf(
                    'Locale Detector Service must be instance of "yimaLocali\Detector\DetectorInterface" '
                    .'but "%s" given'
                    , get_class($detector)
                )
            );
        }

        // set Available Locales to Share Service
        $config = $sm->get('config');
        if (is_array($config) && isset($config['yimaLocali'])) {
            $config = $config['yimaLocali'];
            if (isset($config['available_locales'])) {
                // set available and supported locales
                // note: if you want to know one of a reason of this, take a look at uri detector strategy
                new LocaleSupport($config['available_locales']);
            }
        }

        // get Locale form detector
        $locale = $detector->getLocale();
        if (!$locale) {
            $config = $sm->get('config');
            if (is_array($config) && isset($config['yimaLocali'])) {
                if (isset($config['yimaLocali']['throw_exceptions'])) {
                    if ($config['yimaLocali']['throw_exceptions']) {
                        throw new \Exception(
                            sprintf(
                                'No locale found in locale detection by "%s" Detector'
                                , get_class($detector)
                            )
                        );
                    }
                }
            }
        }

        // run events after locale detected --------  {
        if ($detector instanceof AggregateDetectorInterface) {
            // get detector class that detected te locale

            /** @var $detector AggregateDetectorInterface */
            $detector = $detector->getLastStrategyFound();
        }

        $event = new LocaleEvent();
        $event->setLocale($locale)
            ->setDetector($detector)
            ->setServiceLocator($sm);

        /** @var $eventManager \Zend\EventManager\EventManager */
        $eventManager = $sm->get('eventManager');
        $eventManager->setIdentifiers(self::EVENT_IDENTIFIER);
        $eventManager->trigger(LocaleEvent::EVENT_LOCALE_DETECTED, $event);
        // ... -------- }

        // create locale object and register as a service
        $localeObject = new Locale($locale);
        # set plugin manager
        if ($sm->has('yimaLocali\PluginManager')) {
            $pluginManager = $sm->get('yimaLocali\PluginManager');
            $localeObject->setPluginManager($pluginManager);
        }

        $sm->setService('locale', $localeObject);
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getServiceConfig()
	{
		return array(
			'factories' => array(
                # default Locale Detector Strategy, implement DetectorInterface
				'yimaLocali.Detector.Strategy' => 'yimaLocali\Detector\AggreagateStrategyFactory',

				# managing plugin for locale object
				'yimaLocali\PluginManager'   => 'yimaLocali\Mvc\Service\PluginManagerFactory',

				'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
			),
			'invokables' => array(
				# Strategy factories
				'yimaLocali\Detector\UriPathStrategy'        => 'yimaLocali\Detector\UriPathStrategy',
				'yimaLocali\Detector\CookieStrategy'         => 'yimaLocali\Detector\CookieStrategy',

				# Translation Table
				'yimaLocali\I18nTable' => 'yimaLocali\Db\TableGateway\I18n',
			),
		);
	}

    /**
     * Returns configuration to merge with application configuration
     *
     * @return array|\Traversable
     */
	public function getConfig()
	{
		return include __DIR__ . '/../../config/module.config.php';
	}

    /**
     * Return an array for passing to Zend\Loader\AutoloaderFactory.
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    /*protected function deriveModuleNamespace($controller)
    {
    	if (!strstr($controller, '\\')) {
    		return '';
    	}
    	$module = substr($controller, 0, strpos($controller, '\\'));
    	return $module;
    }*/
}
