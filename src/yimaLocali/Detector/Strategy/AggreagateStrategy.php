<?php
namespace yimaLocali\Detector\Strategy;

use Countable;
use IteratorAggregate;
use Zend\Stdlib\PriorityQueue;
use yimaLocali\Detector\DetectorInterface;
use yimaLocali\Detector\AbstractDetector;

use yimaLocali\Detector\Feature\SystemUsableInterface;

class AggreagateStrategy extends AbstractDetector implements 
	SystemUsableInterface,
	Countable, 
	IteratorAggregate
{
	/**
	 * @var PriorityQueue
	 */
	protected $queue;
	
	/**
	 * Last Detector found in Quee
	 * 
	 * @var yimaLocali\Detector\DetectorInterface
	 */
	protected $lastStrategyFound;

	/**
	 * Constructor
	 *
	 * Instantiate the internal priority queue
	 *
	 */
	public function __construct()
	{
		$this->queue = new PriorityQueue();
	}

	public function getLocale()
	{
		if (0 === count($this->queue)) {
			return false;
		}

		foreach ($this->queue as $detector) {
			$locale = $detector->getLocale();
			if (!$locale) {
				// No resource found; try next resolver
				continue;
			}

			// Resource found; return it
			$this->lastStrategyFound = $detector;
			return $locale;
		}

		return false;
	}
	
	// Implemented Features ........................................................................................
	
	public function makeItUsable()
	{
		$lastStrategy = $this->getLastStrategyFound();
		
		if ($lastStrategy instanceof SystemUsableInterface) {
			$lastStrategy->makeItUsable();
		} 
		
		return $this;
	}
	
	// Inside Class Usage ..........................................................................................

	/**
	 * Return count of attached resolvers
	 *
	 * @return int
	 */
	public function count()
	{
		return $this->queue->count();
	}
	
	/**
	 * IteratorAggregate: return internal iterator
	 *
	 * @return PriorityQueue
	 */
	public function getIterator()
	{
		return $this->queue;
	}
	
	/**
	 * Attach a detector stategy
	 *
	 */
	public function attach(DetectorInterface $detector, $priority = 1)
	{
		$this->queue->insert($detector, $priority);
		return $this;
	}

	public function getLastStrategyFound()
	{
		return $this->lastStrategyFound;
	}

}