<?php
class Zend_Gdata_DouBan_Extension_Uid
{

    protected $_rootElement = 'uid';
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
