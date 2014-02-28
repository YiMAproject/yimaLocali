<?php
namespace yimaLocali\Detector;

use yimaLocali\Service\LocaleSupport;
use Zend\Console\Console;
use Zend\Http\Header\Cookie;
use Zend\Http\Header\SetCookie;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Http\PhpEnvironment\Response as HttpResponse;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CookieStrategy implements
    DetectorInterface,
    ServiceLocatorAwareInterface

{
    /**
     * @var string
     */
    protected static $DEFAULT_COOKIE_NAME;

    /**
     * @var string
     */
    protected $cookieName;

    /**
     * @var \Zend\Http\Request
     */
    protected $request;

    /**
     * @var
     */
    protected $response;

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceLocator;

    /**
     * Get Locale
     *
     * @return bool|string
     */
    public function getLocale()
    {
    	if (Console::isConsole()) {
    		// not supported on console
    		return false;
    	}

        $return = false;

    	$cookie = $this->getRequest()->getCookie();
    	$locale = $cookie->offsetGet($this->getCookieName());
    	if (LocaleSupport::isValidLocale($locale)) {
    		$return = $locale ;
    	}
    	
    	return $return;
    }

    /**
     * Set name of cookie key
     *
     * @param string $name Name of cookie key
     *
     * @return $this
     */
    public function setCookieName($name)
    {
        $this->cookieName = (string) $name;

        return $this;
    }

    /**
     * Return cookie key name
     *
     * @return string
     */
    public function getCookieName()
    {
        if (!$this->cookieName) {
            $this->cookieName = self::getDefaultCookieName();
        }

        return $this->cookieName;
    }

    /**
     * Get default cookie name
     *
     * @return string
     */
    public static function getDefaultCookieName()
    {
        if (! self::$DEFAULT_COOKIE_NAME) {
            self::$DEFAULT_COOKIE_NAME = 'yimalocali.detected.locale';
        }

        return self::$DEFAULT_COOKIE_NAME;
    }

    /**
     * Set default cookie name
     *
     * @param string $name
     */
    public static function setDefaultCookieName($name)
    {
        self::$DEFAULT_COOKIE_NAME = (string) $name;
    }
    
    // Inside Class Usage ..........................................................................................

    /**
     * Get Request
     *
     * @return \Zend\Http\Request
     *
     * @throws \Exception
     */
    public function getRequest()
    {
        if (!$this->request) {
            $sm  = $this->getServiceLocator();

            $app = $sm->get('Application');
            if (!$request = $app->getRequest()) {
                $request = new HttpRequest();
            }

            $this->request = $request;
        }

        return $this->request;
    }

    /**
     * Set request
     *
     * @param \Zend\Http\Request $request
     *
     * @return $this
     * @throws \Exception
     */
    public function setRequest(\Zend\Http\Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return \Zend\ServiceManager\ServiceManager
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}