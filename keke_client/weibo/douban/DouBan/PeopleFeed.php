<?php

class Zend_Gdata_DouBan_PeopleFeed
{
	protected $_entryClassName = 'Zend_Gdata_DouBan_PeopleEntry';
	protected $_feedClassName = 'Zend_Gdata_DouBan_PeopleFeed';

	public function __construct($element)
	{
		
		foreach (Zend_Gdata_DouBan::$namespaces as $nsPrefix => $nsUri) {
			$this->registerNamespace($nsPrefix, $nsUri);
		}
		parent::__construct($element);
	}
}
?>
