<?php
namespace yimaLocali\Service;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;

/**
 * Class PluginManager
 *
 * @package yimaLocali\Service
 *
 * @note injections implemented inside locale helper class
 *       and this plugin manager configured from those class
 * @see \yimaLocali\Service\AbstractLocaleHelper
 */
class PluginManager extends AbstractPluginManager
{
	/**
	 * Default set of helpers
	 *
	 * @var array
	 */
	protected $invokableClasses = array(
//		'date' => 'cLocali\Plugin\Date',
	);
	
	/**
	 * Constructor
	 *
	 * After invoking parent constructor, add an initializer to inject the
	 * attached renderer and translator, if any, to the currently requested helper.
	 *
	 * @param  null|ConfigInterface $configuration
	 */
	public function __construct(ConfigInterface $configuration = null)
	{
		parent::__construct($configuration);
	}
	
    /**
     * Validate the plugin
     * 
     */
    public function validatePlugin($plugin)
    {
        return true;
    }
}
