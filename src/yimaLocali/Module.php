<?php
namespace yimaLocali;

use yimaLocali\Service\LocaleListeners;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\LocatorRegisteredInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;

use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Mvc\MvcEvent;

use yimaLocali\Detector\DetectorInterface;

class Module implements
    InitProviderInterface,
    ServiceProviderInterface,
    ConfigProviderInterface,
    AutoLoaderProviderInterface
{
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
        $events         = $moduleManager->getEventManager();
        $sharedEvents   = $events->getSharedManager();

        /** @var $defltListeners SharedListenerAggregateInterface */
        $defltListeners = new LocaleListeners();
        $defltListeners->attachShared($sharedEvents);
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
}
