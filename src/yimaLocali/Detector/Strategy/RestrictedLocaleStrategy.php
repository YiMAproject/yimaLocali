<?php
namespace yimaLocali\Detector\Strategy;

use yimaLocali\Detector\AbstractDetector;

class RestrictedLocaleStrategy extends AbstractDetector
{
	public function getLocale()
	{
		if ($this->locale) {
			return $this->locale;
		}
		
		$config = $this->getConfig();
		if (isset($config['default'])) {
			$this->setLocale($config['default']);
		}
		
		return $this->locale;
	}
	
	public function setLocale($locale)
	{
		$this->locale = (string) $locale;
	
		return $this;
	}
}