<?php

namespace yimaLocali\Db\TableGateway;

use Application\Db\TableGateway\AbstractTableGateway;

/**
 * Translation tables must instace of \Zend\Db\Adapter\AdapterAwareInterface
 * or Extended Application\Db\TableGateway\AbstractTableGateway
 * to have adapter database available to work.
 * 
 */
class I18n extends AbstractTableGateway
{
	# db table name
    protected $table = 'yimaLocali_i18n';

}