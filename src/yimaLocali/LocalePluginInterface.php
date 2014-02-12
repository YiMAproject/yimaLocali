<?php
namespace yimaLocali;

interface LocalePluginInterface
{
	/**
	 * Set the Locale object
	 *
	 * @param  Renderer $view
	 * @return HelperInterface
	 */
	public function setLocale(Locale $locale);
	
	/**
	 * Get the Locale object
	 *
	 * @return Renderer
	 */
	public function getLocale();
}
