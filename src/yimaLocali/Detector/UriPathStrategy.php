<?php
namespace yimaLocali\Detector;

use yimaLocali\Service\LocaleSupport;

use Zend\Console\Console;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UriPathStrategy implements
    DetectorInterface,
    Feature\SystemWideInterface,
    ServiceLocatorAwareInterface
{
	/**
	 * Request Object
	 * 
	 * @var \Zend\Http\Request
	 */
	protected $request;
	
	/**
	 * Router Object
	 *
	 * @var \Zend\Mvc\Router\Http\TreeRouteStack
	 */
	protected $router;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Get Locale
     *
     * @return string
     */
    public function getLocale()
    {
    	if (Console::isConsole()) {
    		// not supported on console
    		return false;
    	}

        $locale = false;

    	$firstSegment = $this->getRequestFirstSegment();
    	if (!empty($firstSegment) && localeSupport::isValidLocale($firstSegment)) {
            // first segment of request uri is valid locale or alias, exp. /en_US
    		$locale = localeSupport::getLocaleFromAlias($firstSegment);
    	}
    	
    	return $locale;
    }

    /**
     * SystemWideInterface Implementation
     *
     * Cut locale string from route uri path segments,
     *
     * in example:
     *  /fa/content/article => /content/article
     *  and fa as current language will registered by yimaLocali Module
     *  and cant retrieved later into application from this module.
     *
     */
    public function doSystemWide()
    {
        /** @var $request \Zend\Http\PhpEnvironment\Request */
        $request      = $this->getRequest();

    	$firstSegment = $this->getRequestFirstSegment();
    	$reqUri       = $request->getRequestUri();
    	if (strpos($reqUri, $firstSegment) === false) {
    		// we don`t have locale in uri segments
    		return;
    	}
    	
    	$baseUrl = $request->getBaseUrl().'/'.$firstSegment;
    	if ($baseUrl == $reqUri) {
    		// be dalile inke baseUrl(pathOffset) dar route haa mojood ast '/' va digar null nist
    		// '/' raa be entehaaie uri ezaafe mikonim ke dar address /fa route / ghabele shenasaii bashad
    		// /fa/
    		$request->setUri($reqUri.'/');
    	}

        /** @var $router \Zend\Mvc\Router\Http\TreeRouteStack */
        $router = $this->getRouter();
    	$router->setBaseUrl($baseUrl);
    }

    /**
     * Get first segment of current request uri path
     * exp. /fa/content/article => fa
     *
     * @return string
     */
    protected function getRequestFirstSegment()
    {
        /** @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $this->getRequest();

        $reqUri  = $request->getRequestUri();
        $baseUrl = $request->getBaseUrl();

        $url = str_replace($baseUrl, '', $reqUri);

        $firstSegment = ltrim($url, '/');
        if (strstr($firstSegment, '/')) {
            $firstSegment = substr($firstSegment, 0, strpos($firstSegment, '/'));
        }

        return $firstSegment;
    }

    // Inside Class Usage ..........................................................................................

    /**
     * Get request
     *
     * @return \Zend\Http\Request
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
     * Get Router
     *
     * @return TreeRouteStack
     */
    public function getRouter()
    {
    	if (!$this->router) {
            $sm = $this->getServiceLocator();
            if ($sm->has('Router')) {
                $this->router = $sm->get('Router');
            }
    	}

    	return $this->router;
    }

    /**
     * Set Router
     *
     * @param TreeRouteStack $router
     *
     * @return $this
     * @throws \Exception
     */
    public function setRouter(TreeRouteStack $router)
    {
    	$this->router = $router;

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

        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}