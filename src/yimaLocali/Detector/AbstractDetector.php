<?php
namespace yimaLocali\Detector;

use yimaLocali\Detector\DetectorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractDetector implements DetectorInterface
{
	protected $serviceLocator;
	
	/**
	 * Locale
	 * 
	 * @var string
	 */
	protected $locale;
	
	/**
	 * Aliases localies
	 *
	 * @var string
	 */
	protected $aliases;
	
	/**
	 * Get Current Locale based on strategy found in class
	 *
	 */
	public function getLocale()
	{
		if ($this->locale) {
			return $this->locale;
		}
		
		// try to detect locale and set it
		// ...
		
		return $this->locale;
	}
		
	// ......................................................................................................
	
	protected function isValidLocale($locale)
	{
		$locale = (string) $locale;
		$locale = $this->getAlias($locale);
		
		$supported = $this->getAvailableLocalies();
		if (in_array($locale,$supported)) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Tamaami e locale haii ke tavasote barname poshtibani mishavad
	 * 
	 */
	protected function getAvailableLocalies()
	{
		$config = $this->getConfig();
		
		if (isset($config['supported']) && is_array($config['supported'])) {
			$supported = $config['supported'];
		} else {
			$supported = array();
		}
		 
		if (isset($config['default']) && is_scalar($config['default'])) {
			if (! in_array($config['default'],$supported)) {
				array_unshift($supported, $config['default']);
			}
		}
		
		return $supported;
	}
	
	// ...........................
	
	/**
	 * Determine if we have an alias
	 *
	 * @param  string $alias
	 * @return bool
	 */
	protected function hasAlias($alias)
	{
		$alias = (string) $alias;
		$aliases = $this->getAliases();
		
		return (isset($aliases[$alias]));
	}
	
	/**
	 * Yek string migirad agar alias e chizi bood aan raa bar migardaanad
	 * dar gheire in soorat khode matn bargasht mishavad
	 * 
	 * @param string $alias
	 */
	protected function getAlias($alias)
	{
		$alias = (string) $alias;
		
		if (! $this->hasAlias($alias)) {
			// we dont have alias to this
			return $alias;	
		}
		
		$locale = $alias;
		
		$aliases = $this->getAliases(); 
		do {
			$locale = $aliases[$locale];
		} while ($this->hasAlias($locale));
		
		return $locale;
	}
	
	protected function getAliases()
	{
		$config = $this->getConfig();
		if (isset($config['aliases']) && is_array($config['aliases'])) {
			$this->aliases = $config['aliases'];
		}
		
		return $this->aliases;
	}
	
	// ...........................
	
	protected function getConfig($key = 'yimaLocali')
	{
		$serviceLocator = $this->getServiceLocator();
		
		$config = $serviceLocator->get('config');
		if (is_array($config) && isset($config[$key])) {
			$config = $config[$key];
		}
		
		return (is_array($config)) ? $config : array(); 
	}
	
	/**
	 * Set serviceManager instance
	 *
	 * @param  ServiceLocatorInterface $serviceLocator
	 * @return void
	 */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->serviceLocator = $serviceLocator;
	
		return $this;
	}
	
	/**
	 * Retrieve serviceManager instance
	 *
	 * @return ServiceLocatorInterface
	 */
	public function getServiceLocator()
	{
		return $this->serviceLocator;
	}
	
}
