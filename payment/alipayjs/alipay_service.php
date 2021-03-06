<?php
 

require_once("alipay_function.php");

class alipay_service {

    public $gateway;			 
    public $security_code;		 
    public $mysign;			 
    public $sign_type;		 
    public $parameter;
    public $account_name;			 
    public $_input_charset;     

 
 
    function alipay_service($parameter,$security_code,$sign_type,$pay_type='js') {
        if($pay_type=='js'){
        	$this->gateway = "https://www.alipay.com/cooperate/gateway.do?";
        }elseif($pay_type=='batch'){
        	$this->gateway = "https://mapi.alipay.com/gateway.do?";
        }
        $this->security_code  = $security_code;
        $this->sign_type      = $sign_type;
        $this->parameter      = para_filter($parameter);
    
        if($parameter['_input_charset'] == '')
            $this->parameter['_input_charset'] = 'GBK';

        $this->_input_charset   = $this->parameter['_input_charset'];

 
        $sort_array   = arg_sort($this->parameter);    
        $this->mysign = build_mysign($sort_array,$this->security_code,$this->sign_type);
    }
    function create_url() {
        $url         = $this->gateway;
        $sort_array  = array();
        $sort_array  = arg_sort($this->parameter);
        $arg         = create_linkstring_urlencode($sort_array);	 
        
	 
        $url.= $arg."&sign=" .$this->mysign ."&sign_type=".$this->sign_type;
        return $url;
    }
 
    function build_postform() {

        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' target='_blank' action='".$this->gateway."_input_charset=".$this->parameter['_input_charset']."' method='post'>";

        while (list ($key, $val) = each ($this->parameter)) {
            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }

        $sHtml = $sHtml."<input type='hidden' name='sign' value='".$this->mysign."'/>";
        $sHtml = $sHtml."<input type='hidden' name='sign_type' value='".$this->sign_type."'/></form>";

        $sHtml = $sHtml."<button type='button' name='v_action' value='支付宝确认付款' onClick='document.forms[\"alipaysubmit\"].submit();'>支付宝确认付款</button>";
        return $sHtml;
    }
    

}
?>