<?php
namespace yimaLocali\Mvc\Service;

use Zend\Mvc\Service\AbstractPluginManagerFactory;

use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PluginManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'yimaLocali\LocalePluginManager';

    /**
     * An array of helper configuration classes to ensure are on the helper_map stack.
     *
     * @var array
     */
    protected $defaultHelperMapClasses = array(
        #'Zend\Form\View\HelperConfig',
    );

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $plugins = parent::createService($serviceLocator);

        foreach ($this->defaultHelperMapClasses as $configClass) {
            if (is_string($configClass) && class_exists($configClass)) {
                $config = new $configClass;

                if (!$config instanceof ConfigInterface) {
                    throw new \Exception(sprintf(
                        'Invalid service manager configuration class provided; received "%s", expected class implementing %s',
                        $configClass,
                        'Zend\ServiceManager\ConfigInterface'
                    ));
                }

                $config->configureServiceManager($plugins);
            }
        }
        
        /*
        $plugins->setFactory('testPlugin', function($sm) use($serviceLocator) {
            return 'testPlugin';
        });
        */

        return $plugins;
    }
}
