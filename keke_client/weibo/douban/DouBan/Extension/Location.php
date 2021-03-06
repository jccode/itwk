<?php
class Zend_Gdata_DouBan_Extension_Location
{

    protected $_rootElement = 'location';
    protected $_rootNamespace = 'db';

    public function __construct($text = null)
    {
        foreach (Zend_Gdata_DouBan::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri);
        }
        parent::__construct();
        $this->_text = $text;
    }

}
?>
