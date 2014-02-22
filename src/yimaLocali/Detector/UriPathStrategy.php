<?php
namespace yimaLocali\Detector;

use Zend\Console\Console;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Mvc\Router\Http\TreeRouteStack;

use yimaLocali\Detector\Feature\SystemUsableInterface;

class UriPathStrategy extends DetectorAbstract implements
	SystemUsableInterface
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
	
    public function getLocale()
    {
    	if (Console::isConsole()) {
    		// not supported on console
    		return;
    	}
    	
    	$request = $this->getRequest(); 
    	$reqUri  = $request->getRequestUri();
    	if (strpos($reqUri,'/') !== 0) {
    		// we dont have any request after basePath
    		return;
    	}
    	
    	$firstSegment = $this->getRequestFirstSegment();
    	// check first segment as available locale
    	if ($this->isValidLocale($firstSegment)) {
    		$this->locale =  $this->getAlias($firstSegment);
    	}
    	
    	return $this->locale;
    }
    
    // Implemented Features ........................................................................................

    public function makeItUsable()
    {
    	$request      = $this->getRequest();
    	
    	$reqUri       = $request->getRequestUri();
    	$firstSegment = $this->getRequestFirstSegment();
    	
    	if (!$this->isValidLocale($firstSegment)) {
    		return;
    	}
    	
    	/* locale ($firstSegment) raa az daroone uri hazf mikonim */
    	
    	if (strpos($reqUri, $firstSegment) === false) {
    		// we dont locale in uri segments
    		return;
    	}
    	
    	
    	$baseUrl = $request->getBaseUrl().'/'.$firstSegment;
    	
    	if ($baseUrl == $reqUri) {
    		// be dalile inke baseUrl(pathOffset) dar route haa mojood ast '/' va digar null nist
    		// '/' raa be entehaaie uri ezaafe mikonim ke dar address /fa route / ghabele shenasaii bashad
    		// /fa/
    		$request->setUri($reqUri.'/');
    	}
    	
    	$router = $this->getRouter();
    	$router->setBaseUrl($baseUrl);
    }

    // Inside Class Usage ..........................................................................................
    
    public function getRequest()
    {
    	if ($this->request) {
    		return $this->request;
    	}
    	
    	$serviceLocator = $this->getServiceLocator();
    	if ($serviceLocator->has('request')) {
    		$request = $serviceLocator->get('Application')->getRequest();
    	} else {
    		$request = new HttpRequest();
    	}
    	
    	$this->setRequest($request);
    	return $this->getRequest();
    }
    
    public function setRequest($request = null)
    {
    	# reset request to default
    	if ($request == null) {
    		$this->request = null;
    	}
    	
    	if (! $request instanceof HttpRequest) {
    		throw new \Exception(sprintf(
    			'Request must be instance of "HttpRequest" but "%s" given',get_class($request)
    		));
    	}
    	
    	# if request change we must getLocale again from request
    	$this->locale  = null;
    	
    	$this->request = $request;
    	return $this;
    }
    
    public function getRouter()
    {
    	if ($this->router) {
    		return $this->router;
    	}
    	
    	$sl = $this->getServiceLocator();
    	if ($sl->has('Router')) {
    		$router = $sl->get('Router'); 
    	}
    	
    	$this->setRouter($router);
    	return $this->getRouter();
    }
    
    public function setRouter($router = null)
    {
    	# reset request to default
    	if ($router == null) {
    		$this->router = null;
    	}
    	
    	if (! $router instanceof TreeRouteStack) {
    		throw new \Exception(sprintf(
    			'Router must be instance of "TreeRouteStack" but "%s" given',get_class($router)
    		));
    	}
    	
    	$this->router = $router;
    	return $this;
    }
    
    protected function getRequestFirstSegment()
    {
    	$request = $this->getRequest();
    	
    	$reqUri  = $request->getRequestUri();
    	$baseUrl = $request->getBaseUrl();
    	
    	$url = str_replace($baseUrl, '', $reqUri);
    	
    	$firstSegment = ltrim($url,'/');
    	if (strstr($firstSegment,'/')) {
    		$firstSegment = substr($firstSegment,0,strpos($firstSegment,'/'));
    	}
    	
    	return $firstSegment;
    }
}