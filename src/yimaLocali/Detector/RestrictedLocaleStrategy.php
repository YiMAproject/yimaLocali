<?php
namespace yimaLocali\Detector;

class RestrictedLocaleStrategy extends DetectorAbstract
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