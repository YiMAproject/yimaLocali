<?php
namespace yimaLocali\Mvc;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;

class LocalizationListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Attach to an event manager
     *
     * @param  EventManagerInterface $events
     * @param  integer $priority
     */
    public function attach(EventManagerInterface $events, $priority = 10000)
    {
        $this->listeners[] = $events->attach('Zend\Mvc\Application', MvcEvent::EVENT_BOOTSTRAP, array($this, 'onBootstraping'),$priority);
    }
    
    

    /**
     * Detach all our listeners from the event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
    	foreach ($this->listeners as $index => $listener) {
    		if ($events->detach($listener)) {
    			unset($this->listeners[$index]);
    		}
    	}
    }
    
}
