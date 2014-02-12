Simple DB query to fetch translations
----------------------------------------------------------------------------------
SELECT `sampletable`.*, `I18n__description`.`content` AS `sampletableI18n__description` 
FROM   `sampletable` 
INNER JOIN `i18n` AS `I18n__description` 
	ON  `I18n__description`.`foreign_key` = `sampletable`.`sampletable_id`
	AND `I18n__description`.`model`       = 'cApplication\\Model\\TableGateway\\Sample'
	AND `I18n__description`.`field`       = 'description'
	AND `I18n__description`.`locale`      = 'fa_IR'
WHERE 1