<?php
namespace yimaLocali\Detector;

interface DetectorInterface
{
	/**
	 * Get Current Locale based on strategy found in class
     *
     * @return string
     */
    public function getLocale();
}
