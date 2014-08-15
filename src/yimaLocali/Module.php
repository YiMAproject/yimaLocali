<?php
namespace yimaLocali;

use yimaLocali\Service\LocaleListeners;
use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\LocatorRegisteredInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ControllerPluginProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;

use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Mvc\MvcEvent;

use yimaLocali\Detector\DetectorInterface;

/**
 * Class Module
 *
 * @package yimaLocali
 */
class Module implements
    InitProviderInterface,
    ServiceProviderInterface,
    ControllerPluginProviderInterface,
    ViewHelperProviderInterface,
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
    public function init(ModuleManagerInterface $moduleModuleManager)
    {
        /** @var $moduleModuleManager \Zend\ModuleManager\ModuleManager */
        $events         = $moduleModuleManager->getEventManager();
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
				'yimaLocali\Detector\UriPathStrategy' => 'yimaLocali\Detector\UriPathStrategy',
				'yimaLocali\Detector\CookieStrategy'  => 'yimaLocali\Detector\CookieStrategy',

				# Translation Table
				'yimaLocali\I18nTable' => 'yimaLocali\Db\TableGateway\I18n',
			),
            // Inject Locale for services that implement LocaleInterface
            'initializers' => array (
                function ($instance, $sm) {
                    if ($instance instanceof LocaleAwareInterface) {
                        $instance->setLocale(
                            $sm->get('locale.detected')
                        );
                    }
                }
            ),
		);
	}

    /**
     * Controller helper services
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getControllerPluginConfig()
    {
        return array(
            'invokables' => array (
                'locale' => 'yimaLocali\Controller\Plugin\Locale',
            ),
        );
    }

    /**
     * View helper services
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getViewHelperConfig()
    {
        return array(
            'invokables' => array (
                'locale' => 'yimaLocali\View\Helper\Locale',
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
