<?php
namespace yimaLocali;

use Zend\ModuleManager\Feature\LocatorRegisteredInterface;
use Zend\Mvc\MvcEvent;
use yimaLocali\Detector\DetectorInterface;
use yimaLocali\Detector\Feature\SystemUsableInterface as SystemUsable;

use Locale as StdLocale;

class Module implements LocatorRegisteredInterface
{
	public function getServiceConfig()
	{
		return array(
			'invokables' => array(
				# Strategy factories
				'yimaLocali\Strategy\RestrictedStrategy'         => 'yimaLocali\Detector\Strategy\RestrictedLocaleStrategy',
				'yimaLocali\Strategy\CookieStrategy'             => 'yimaLocali\Detector\Strategy\CookieStrategy',
				'yimaLocali\Strategy\HostStrategy'               => 'yimaLocali\Detector\Strategy\HostStrategy',
				'yimaLocali\Strategy\UriPathStrategy'            => 'yimaLocali\Detector\Strategy\UriPathStrategy',
				'yimaLocali\Strategy\QueryStrategy'              => 'yimaLocali\Detector\Strategy\QueryStrategy',
				'yimaLocali\Strategy\HttpAcceptLanguageStrategy' => 'yimaLocali\Detector\Strategy\HttpAcceptLanguageStrategy',
				# Translation Table
				'yimaLocali\I18nTable' => 'yimaLocali\Db\TableGateway\I18n',
				
			),
			'factories' => array(
				'yimaLocali\Locale\Detector' => 'yimaLocali\Mvc\Service\LocaleDetectorFactory',
				'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
				# managing plugin for locale object	
				'yimaLocali\PluginManager'   => 'yimaLocali\Mvc\Service\PluginManagerFactory',
			),
		);
	}
	
	public function init($moduleManager)
	{
		// Locale detection must run before all others on bootsraping
		// and make it available on module onBootstrap()
		$events        = $moduleManager->getEventManager();
		$sharedEvents  = $events->getSharedManager();
		$sharedEvents->attach('Zend\Mvc\Application', MvcEvent::EVENT_BOOTSTRAP, array($this, 'onBootstraping'),10000);
	}
	
	public function onBootstraping(MvcEvent $event)
	{
		$app = $event->getApplication();
		$sm  = $app->getServiceManager();
		
		$detector = $sm->get('yimaLocali\Locale\Detector');
		if (!$detector instanceof DetectorInterface) {
			throw new Exception\InvalidLocaleDetectorException(sprintf(
				'Locale Detector Service must be instance of "yimaLocali\Detector\DetectorInterface" '
				.'but "%s" given'
				, get_class($detector))
			);
		}
		
		$locale = $detector->getLocale();
		if ($locale) {
			if ($detector instanceof SystemUsable) {
				$detector->makeItUsable();
			}
			
			// create locale object and register as a service
			$localeObject = new Locale($locale);
			# set plugin manager
			if ($sm->has('yimaLocali\PluginManager')) {
				$pluginManager = $sm->get('yimaLocali\PluginManager');
				$localeObject->setPluginManager($pluginManager);
			}
			
			$sm->setService('locale',$localeObject);
			
			/*
			 * Here we register default locale to use around classes of application
			 */
			if (class_exists('\Locale',true)) {
			    // in some host Locale az a PECL mybe not installed
			    StdLocale::setDefault($locale);
			}
			
			# reset locale if set before
			$translator = $sm->get('translator');
			$translator->setLocale($locale);
			
			return;
		}
		
		// ..........................................................................................................
		$config = $sm->get('config');
		if (is_array($config) && isset($config['yimaLocali'])) {
			if (isset($config['yimaLocali']['throw_exception'])) {
				if ($config['yimaLocali']['throw_exception']) {
					throw new Exception\LocaleNotFoundException(sprintf(
						'No locale found in locale detection by "%s" Detector'
						, get_class($detector))
					);
				}
			}
		}
	}
	
	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}
	
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    protected function deriveModuleNamespace($controller)
    {
    	if (!strstr($controller, '\\')) {
    		return '';
    	}
    	$module = substr($controller, 0, strpos($controller, '\\'));
    	return $module;
    }
}
