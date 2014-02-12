<?php
namespace SlmLocale\Strategy;

use SlmLocale\LocaleEvent;
use Zend\Http\Request as HttpRequest;

class QueryStrategy extends AbstractStrategy
{
    /**
     * Query key use in uri
     *
     * @var string $query_key
     */
    protected $query_key = 'lang';

    public function setOptions(array $options = array())
    {
        if (array_key_exists('query_key', $options)) {
            $this->query_key = (string) $options['query_key'];
        }
    }

    /**
     * {@inheritdoc }
     */
    public function detect(LocaleEvent $event)
    {
        /** @var HttpRequest $request */
        $request = $event->getRequest();

        if (!$request instanceof HttpRequest) {
            return;
        }

        if (!$event->hasSupported()) {
            return;
        }

        $locale  = $request->getQuery($this->query_key);

        if ($locale === null) {
            return;
        }

        if (!in_array($locale, $event->getSupported())) {
            return;
        }

        return $locale;
    }

}
