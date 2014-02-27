<?php
namespace yimaLocali;

use yimaLocali\Detector\DetectorInterface;
use Zend\EventManager\Event;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LocaleEvent extends Event implements
    ServiceLocatorAwareInterface
{
    /**#@+
     * Events triggered by eventmanager
     */
    const EVENT_LOCALE_DETECTED = 'locale.detected';
    /**#@-*/

    /**
     * @var DetectorInterface Detector of locale
     */
    protected $detector;

    /**
     * @var string detected Locale
     */
    protected $locale;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * set locale
     *
     * @param $locale
     *
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set Detector
     *
     * @param $detector
     *
     * @return $this
     */
    public function setDetector($detector)
    {
        $this->detector = $detector;

        return $this;
    }

    /**
     * Get detector
     *
     * @return DetectorInterface
     */
    public function getDetector()
    {
        return $this->detector;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
