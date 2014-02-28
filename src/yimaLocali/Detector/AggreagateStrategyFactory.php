<?php
namespace yimaLocali\Detector;

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
        $AggregateStrategy = new AggreagateStrategy();
        
        // attach detectors strategies by module config ...................................................
        $config = $serviceLocator->get('config');
        if (is_array($config) && isset($config['yimaLocali'])) {
        	if (is_array($config['yimaLocali'])
                && isset($config['yimaLocali']['detector'])
                && isset($config['yimaLocali']['detector']['aggregate'])
            ) {
        		$config = $config['yimaLocali']['detector']['aggregate'];

        		if (is_array($config)
                    && isset($config['strategies'])
                    && is_array($config['strategies'])
                ){
                    $config = $config['strategies'];

        			foreach ($config as $detector => $priority) {
        				if (is_array($priority)) {
                            /*array(
                                'object'   => new StrategyObject(),
                                'priority' => -10
                            ),*/
                            if (!(isset($priority['object']) && isset($priority['priority']))) {
                                throw new \Exception(
                                    sprintf(
                                        'Invalid config provided for aggregate detector strategy. '.
                                        'Must have "object" and "priority" key you provide (%s)',
                                        implode(', ', array_keys($priority))
                                    )
                                );
                            }

                            $detector = $priority['object'];
                            $priority = $priority['priority'];
                        } else {
                            /* 'Registered\Service\Or\ClassName' => -10, */
                            if ($serviceLocator->has($detector)) {
                                $detector = $serviceLocator->get($detector);
                            } else if (class_exists($detector)) {
                                $detector = new $detector();
                            }
                        }

                        if (!$detector instanceof DetectorInterface) {
                            throw new \Exception(
                                sprintf(
                                    'Invalid Detector Interface, you provided "%s"',
                                    (is_object($detector)) ? get_class($detector) : gettype($detector).' '.serialize($detector)
                                )
                            );
                        }

                        $AggregateStrategy->attach($detector, $priority);
        			}
        		}
        	}
        }
        
        return $AggregateStrategy;
    }
}
