<?php
namespace yimaLocali\Detector\Feature;

interface SetProgrammabilityLocaleInterface
{
	/**
	 * Dar in haalat mitavan az daroon e barname locale morede nazar raa ta'rif kard
	 * masalan dar safheie intro zaban entekhaab mishavad, pas az entekhaab tavasote
	 * strategy e cookie zakhire mishavad tavasote sedaa zadan e in method
	 * 
	 * @param string | toString $locale
	 */
    public function setLocale($locale);
    
    /**
     * Clear all affected by setLocale
     */
    public function resetLocale();
}
