<?php
namespace yimaLocali\Detector;

use Zend\ServiceManager\ServiceLocatorAwareInterface;

interface DetectorInterface extends ServiceLocatorAwareInterface
{
	/**
	 * Get Current Locale based on strategy found in class 
	 * 
	 */
    public function getLocale();
}
