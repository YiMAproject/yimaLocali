<?php
namespace yimaLocali\Detector;

/**
 * Interface AggregateDetectorInterface
 *
 * @package yimaLocali\Detector
 */
interface AggregateDetectorInterface extends DetectorInterface
{
    /**
     * Get last detector that detect locale in aggregate queue
     *
     * @return DetectorInterface
     */
    public function getLastStrategyFound();
}
