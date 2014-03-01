<?php
namespace yimaLocali\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AbstractLocaleHelper
 *
 * @package yimaLocali\Service
 */
class AbstractLocaleHelper implements
    ServiceLocatorAwareInterface
{
    /**
     * @var \Zend\Mvc\Controller\PluginManager|ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var string Locale
     */
    protected $locale;
	
    /**
     * Invoke as a functor
     *
     * @return mixed
     */
    public function __invoke()
    {
        return $this;
    }

    /**
     * Get detected locale
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getLocale()
    {
        if (!$this->locale) {
            $locale = null;

            $sl = $this->getServiceLocator();
            /** @var $sm \Zend\ServiceManager\ServiceManager */
            $sm = $sl->getServiceLocator();
            if ($sm->has('locale.detected')) {
                $locale = $sm->get('locale.detected');
            } elseif (extension_loaded('intl')) {
                $locale = \Locale::getDefault();
            }

            if (!$locale) {
                throw new \Exception(
                    'Locale not found as a service(locale.detected) or \\Locale::getDefault()'
                );
            }

            $this->locale = $locale;
        }

        return $this->locale;
    }

    /**
     * Return Current Locale as string
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getLocale();
    }

    /**
     * Returns the language part of the locale
     *
     * @return string
     */
    public function getLanguage()
    {
        $locale = explode('_', $this->getLocale());

        return $locale[0];
    }

    /**
     * Returns the region part of the locale if available
     *
     * @return string|false Region
     */
    public function getRegion()
    {
        $locale = explode('_', $this->getLocale());
        if (isset($locale[1]) === true) {

            return $locale[1];
        }

        return false;
    }

    /**
     * Set the main service locator so factories can have access to it to pull deps
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return $this
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Get the main plugin manager. Useful for fetching dependencies from within factories.
     *
     * @return \Zend\Mvc\Controller\PluginManager|ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
    	return $this->serviceLocator;
    }
}
