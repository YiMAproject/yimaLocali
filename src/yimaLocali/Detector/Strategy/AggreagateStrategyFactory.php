<?php
namespace yimaLocali\Detector\Strategy;

use yimaLocali\Detector\DetectorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AggreagateStrategyFactory implements
    FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DetectorInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $detector = new AggreagateStrategy();
        
        // attach detectors strategies by module config ...................................................
        $config = $serviceLocator->get('config');
        if (is_array($config) && isset($config['yimaLocali'])) {
        	if (is_array($config['yimaLocali']) && isset($config['yimaLocali']['strategies'])) {
        		$config = $config['yimaLocali']['strategies'];
        		
        		if (is_array($config)){
        			foreach ($config as $p => $dtc) {
        				if (is_scalar($dtc)) {
        					$detector->attach($serviceLocator->get($dtc), $p);
        				} elseif (is_array($dtc)) {
        					if (isset($dtc['invokable'])) {
        						$prio = (isset($dtc['priority'])) ? $dtc['priority'] : -100; 
        						$detector->attach($serviceLocator->get($dtc['invokable']), $prio);
        					}
        				}
        			}
        		}
        	}
        }
        
        $detector->attach($serviceLocator->get('yimaLocali\Strategy\UriPathStrategy'),90);
        $detector->attach($serviceLocator->get('yimaLocali\Strategy\CookieStrategy'),80);
        
        #$detector->attach($serviceLocator->get('yimaLocali\Strategy\HostStrategy'),80);
        #$detector->attach($serviceLocator->get('yimaLocali\Strategy\QueryStrategy'),60);
        #$detector->attach($serviceLocator->get('yimaLocali\Strategy\HttpAcceptLanguageStrategy'),50);
        
        // run last
        $detector->attach($serviceLocator->get('yimaLocali\Strategy\RestrictedStrategy'),-1000);
        
        return $detector;
    }
}
