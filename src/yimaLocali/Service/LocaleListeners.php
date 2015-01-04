<?php
namespace yimaLocali\Service;

use yimaLocali\Detector\AggregateDetectorInterface;
use yimaLocali\Detector\DetectorInterface;
use yimaLocali\Detector\Feature\SystemWideInterface;
use yimaLocali\LocaleEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\Mvc\MvcEvent;

class LocaleListeners implements SharedListenerAggregateInterface
{
    /**
     * Identifier of Events Triggered in this class
     */
    const IDENTIFIER_EVENT_LOCALE = 'yimaLocali\Module';

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Attach to an event manager
     *
     * @param  SharedEventManagerInterface $events
     * @param  integer $priority
     */
    public function attachShared(SharedEventManagerInterface $events, $priority = 1000000)
    {
        // attach Bootstrap MVC Event to detect locale
        $events->attach(
            'Zend\Mvc\Application',
            MvcEvent::EVENT_BOOTSTRAP,
            array($this, 'onBootstraping'),
            $priority
        );

        // attach Locale Default Events
        $events->attach(
            self::IDENTIFIER_EVENT_LOCALE,
            LocaleEvent::EVENT_LOCALE_DETECTED,
            array($this, 'prepareLocale'),
            1000
        );
    }

    /**
     * Detach all our listeners from the event manager
     *
     * @param SharedEventManagerInterface $events
     *
     * @return void
     */
    public function detachShared(SharedEventManagerInterface $events)
    {

    }

    /**
     * MVC EVent
     *
     * Get Locale From Default Registered Detector Service
     *
     * @param MvcEvent $event
     *
     * @throws \Exception
     */
    public function onBootstraping(MvcEvent $event)
    {
        $app = $event->getApplication();
        /** @var $sm \Zend\ServiceManager\ServiceManager */
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
                new LocaleRegistry($config['available_locales']);
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

        if ($detector instanceof AggregateDetectorInterface)
            // get detector class that detected te locale
            /** @var $detector AggregateDetectorInterface */
            $detector = $detector->getLastStrategyFound();

        if ($detector instanceof SystemWideInterface)
            // SystemWide Detectors to take affect to system to work!
            $detector->doSystemWide();

        // run events after locale detected --------  {
        $event = new LocaleEvent();
        $event->setLocale($locale)
            ->setDetector($detector)
            ->setServiceLocator($sm);

        /** @var $eventManager \Zend\EventManager\EventManager */
        $eventManager = $sm->get('eventManager');
        $eventManager->setIdentifiers(self::IDENTIFIER_EVENT_LOCALE);
        $eventManager->trigger(LocaleEvent::EVENT_LOCALE_DETECTED, $event);
        // ... -------- }

        $sm->setService('locale.detected', $locale);
    }

    /**
     * Locale Event
     *
     * Prepare some system setup around locales after locale detection
     */
    public function prepareLocale(LocaleEvent $event)
    {
        $locale = $event->getLocale();
        if (!$locale) {
            // locale not detected, leave everything to it's default !!
            return false;
        }

        // Set Locale Std Class -----------------------------------------
        if (extension_loaded('intl'))
            \Locale::setDefault($locale);

        // Set default time zone ----------------------------------------
        $localeData = LocaleRegistry::getLocaleData($locale);
        $timezone   = (isset($localeData['default_time_zone'])) ? $localeData['default_time_zone'] : 'UTC';
        date_default_timezone_set($timezone);

        // Set Locale for translator ------------------------------------
        // with setting default locale for translator we don't get exception
        // - on servers that Intl extension not installed.
        $sm = $event->getServiceLocator();

        if ($sm->has('MvcTranslator')) {
            /** @var $translator \Zend\Mvc\I18n\Translator */
            $translator = $sm->get('MvcTranslator');
            $translator->setLocale($locale);
        } elseif ($sm->has('translator')) {
            /** @var $translator \Zend\I18n\Translator\Translator */
            $translator = $sm->get('translator');
            $translator->setLocale($locale);
        }

        return true;
    }
}
