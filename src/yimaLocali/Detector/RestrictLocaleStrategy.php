<?php
namespace yimaLocali\Detector;

use yimaLocali\Service\LocaleRegistry;

/**
 * Class RestrictLocaleStrategy
 *
 * @package yimaLocali\Detector
 */
class RestrictLocaleStrategy implements
    DetectorInterface
{
    protected $locale;


    /**
     * Constructor
     *
     * @param null|string $locale
     */
    public function __construct($locale = null)
    {
        if ($locale) {
            $this->locale = $locale;
        }
    }

    /**
     * Get locale from configs default setting
     *
     * @return string
     */
    public function getLocale()
	{
		if (!$this->locale)
			$this->setLocale(
                LocaleRegistry::getDefaultLocale()
            );

		return $this->locale;
	}

    /**
     * Set Locale for
     *
     * @param $locale
     *
     * @return $this
     */
    public function setLocale($locale)
	{
		$this->locale = (string) $locale;
	
		return $this;
	}
}