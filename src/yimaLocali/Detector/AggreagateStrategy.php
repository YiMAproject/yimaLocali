<?php
namespace yimaLocali\Detector;

use Countable;
use IteratorAggregate;
use Zend\Stdlib\PriorityQueue;

/**
 * Class AggreagateStrategy
 *
 * @package yimaLocali\Detector
 */
class AggreagateStrategy implements
    AggregateDetectorInterface,
	Countable,
	IteratorAggregate
{
	/**
	 * @var PriorityQueue
	 */
	protected $queue;
	
	/**
	 * Last Detector found in Queue
	 * 
	 * @var DetectorInterface
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

    /**
     * Get Current Locale based on strategy found in class
     *
     * @return bool|string
     */
    public function getLocale()
	{
		if (0 === count($this->queue)) {
			return false;
		}

		foreach ($this->queue as $detector) {
            /** @var $detector DetectorInterface */
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
	
    /**
     * Attach Detector to queue list
     *
     * @param DetectorInterface $detector
     * @param int $priority
     *
     * @return $this
     */
    public function attach(DetectorInterface $detector, $priority = 1)
	{
		$this->queue->insert($detector, $priority);

		return $this;
	}

    /**
     * Get last detector that detect locale
     *
     * @return DetectorInterface
     */
    public function getLastStrategyFound()
	{
		return $this->lastStrategyFound;
	}

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
}
