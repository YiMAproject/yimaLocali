<?php
namespace yimaLocali\Service;

use Zend\ServiceManager\AbstractPluginManager;
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
     * @var string Locale
     */
    protected $locale;

    /**
     * @var \Zend\Mvc\Controller\PluginManager|ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var AbstractPluginManager
     */
    protected $pluginManager;

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
     * Set plugin manager instance
     *
     * @param  string|AbstractPluginManager $pluginManager
     *
     * @return $this
     * @throws \Exception
     */
    public function setPluginManager($pluginManager)
    {
        if (is_string($pluginManager)) {
            if (!class_exists($pluginManager)) {
                throw new \Exception(
                    sprintf(
                        'Invalid helper helpers class provided (%s)',
                        $pluginManager
                    )
                );
            }

            $pluginManager = new $pluginManager();
        }

        if (!$pluginManager instanceof AbstractPluginManager) {
            throw new \Exception(
                sprintf(
                    'Helper helpers must extend Zend\ServiceManager\AbstractPluginManager; got type "%s" instead',
                    (is_object($pluginManager) ? get_class($pluginManager) : gettype($pluginManager))
                )
            );
        }

        // inject locale to all plugin instance
        $pluginManager->addInitializer(array($this, 'injectLocale'));

        $this->pluginManager = $pluginManager;

        return $this;
    }

    /**
     * Inject a locale with the registered plugin instance
     *
     * @param  $helper
     *
     * @return void
     */
    public function injectLocale($helper)
    {
        if (! is_object($helper) || !method_exists($helper, 'setLocale')) {
            // we're okay
            return;
        }

        $locale = $this->getLocale();
        $helper->setLocale($locale);
    }

    /**
     * Get plugin manager instance
     *
     * @return AbstractPluginManager
     */
    public function getPluginManager()
    {
        if (null === $this->pluginManager) {
            $this->setPluginManager(new PluginManager());
        }

        return $this->pluginManager;
    }

    /**
     * Overloading: proxy to plugin helpers
     *
     * Proxies to the attached plugin manager to retrieve, return, and potentially
     * execute helpers.
     *
     * * If the helper does not define __invoke, it will be returned
     * * If the helper does define __invoke, it will be called as a functor
     *
     * @param  string $method
     * @param  array $argv
     *
     * @return mixed
     */
    public function __call($method, $argv)
    {
        $helper = $this->plugin($method);
        if (is_callable($helper)) {
            return call_user_func_array($helper, $argv);
        }

        return $helper;
    }

    /**
     * Get plugin instance
     *
     * @param  string     $name Name of plugin to return
     * @param  null|array $options Options to pass to plugin constructor (if not already instantiated)
     *
     * @return object
     */
    public function plugin($name, array $options = null)
    {
        return $this->getPluginManager()->get($name, $options);
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
