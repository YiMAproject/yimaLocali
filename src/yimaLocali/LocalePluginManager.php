<?php
namespace yimaLocali;

use Zend\ServiceManager\AbstractPluginManager;
use yimaLocali\LocalePluginInterface as PluginInterface;

class LocalePluginManager extends AbstractPluginManager
{
	/**
	 * Default set of helpers
	 *
	 * @var array
	 */
	protected $invokableClasses = array(
		'date' => 'yimaLocali\Plugin\Date',
	);
	
	/**
	 * @var yimaLocali\Locale
	 */
	protected $locale;
	
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
		$this->addInitializer(array($this, 'injectLocale'));
	}
	
	/**
	 * Set locale
	 *
	 * @param  Renderer\RendererInterface $renderer
	 * @return HelperPluginManager
	 */
	public function setLocale(Locale $locale)
	{
		$this->locale = $locale;
		return $this;
	}
	
	/**
	 * Retrieve renderer instance
	 *
	 * @return null|Renderer\RendererInterface
	 */
	public function getLocale()
	{
		return $this->locale;
	}
	
	/**
	 * Inject a locale instance with the registered locale
	 *
	 * @param  yimaLocali\LocalePluginInterface $helper
	 * @return void
	 */
	public function injectLocale($helper)
	{
		if (! $helper instanceof PluginInterface) {
			// we're okay
			return;
		}
		
		$locale = $this->getLocale();
		if (null === $locale) {
			return;
		}
		$helper->setLocale($locale);
	}
	
    /**
     * Validate the plugin
     * 
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof PluginInterface) {
            // we're okay
            return;
        }

        throw new \Exception(sprintf(
            'Plugin of type %s is invalid; must implement %s\LocalePluginInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
