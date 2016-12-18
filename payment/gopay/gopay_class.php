<?php
/**
 * 国付宝支付接口
 * @author Administrator
 *
 */
class gopay_class {
	static $VERSION='2.0';//版本
	static $_ANTI_GATE='https://www.gopay.com.cn/PGServer/time';//防钓鱼 服务器时间获取网关
	public $_params;//参数数组
	private $_sign;//签名
	static $_VERI_CODE;//商户识别码
	public $_ecode='0000';//错误码
	public $_gateway;//接口网关
	
	public function __construct($config){
		self::$_VERI_CODE = $config['veri_code'];
		$this->params_ini($config);
	}
	/**
	 * 基本参数配置
	 * Enter description here ...
	 */
	public function params_ini($config){
		global $_K;
			$this->_params = array(
						'version'=>self::$VERSION,
						'charset'=>2,//UTF_8字符集
						'language'=>1,//网关语言，中文
						'signType'=>1,//签名方式 MD5
						'tranCode'=>'8888',//交易码
						'merchantID'=>$config['gopay_uid'],//商户代码
						'virCardNoIn'=>$config['account'],//国付宝商家账号
						'currencyType'=>'156',//币种，，，RMB
						'frontMerUrl'=>$_K['siteurl'].'/payment/gopay/return.php',//$_K['siteurl'].'/payment/gopay/return.php',//同步响应地址
						'backgroundMerUrl'=>$_K['siteurl'].'/payment/gopay/return_background.php',//后台通知地址
						'tranIP'=>kekezu::get_ip(),//当前用户IP
						'isRepeatSubmit'=>1//允许重复提交同意订单
			);
	}
	/**
	 * 设置参数
	 */
	public function set_params($params){
		global $username;
		$this->_params['merOrderNum'] 		= $params['merOrderNum'];//订单号
		$this->_params['tranAmt']	 		= $params['tranAmt'];//交易金额
		$this->_params['feeAmt']	  		= $params['feeAmt'];//商户抽佣。预留
		$this->_params['tranDateTime']		= $params['tranDateTime'];//交易时间
		$this->_params['goodsName']	  		= $params['goodsName'];//商品名称
		$this->_params['goodsDetail'] 		= $params['goodsDetail'];//商品详情
		$this->_params['buyerName']   		= $username;//支付方姓名
		$this->_params['gopayOutOrderId']   = $params['gopayOutOrderId'];//国付宝银行流水
		$this->_params['orderId']  			= $params['orderId'];//国付宝订单号
		$this->_params['bankCode']          = $params['bankCode'];//银行代码
		$this->_params['respCode']  		= $params['respCode'];//支付状态码
		if ($params['tranIP'] != "")
		$this->_params['tranIP']  		= $params['tranIP'];
		$this->_ecode 						= $this->_params['respCode'];//错误码
	}
	/**
	 * 选择网关
	 */
	public function setGateway($test=false){
		if($test){//测试模式
			$this->_gateway='https://211.88.7.30/PGServer/Trans/WebClientAction.do?';
		}else{
			$this->_gateway='https://www.gopay.com.cn/PGServer/Trans/WebClientAction.do?';
		}
	}
	/**
	 * 开启防钓鱼模式
	 */
	public function anti_phishing($anti=false){
		if($anti){
			$this->_params['gopayServerTime'] = trim(file_get_contents(self::$_ANTI_GATE));
		}
	}
	/**
	 * 开启银行直连
	 */
	public function setDirectConnect($t=false,$bankCode){
		if($t){
			$this->_params['userType']=1;//个人支付银行
			$this->_params['bankCode']=$bankCode;//支付银行
		}
	}
	/**
	 * 生成签名
	 */
	public function build_sign(){
		$p =  $this->_params;
		$signValue='version=['.$p['version'].']tranCode=['.$p['tranCode'].']merchantID=['.$p['merchantID']
					.']merOrderNum=['.$p['merOrderNum'].']tranAmt=['.$p['tranAmt'].']feeAmt=['.$p['feeAmt']
					.']tranDateTime=['.$p['tranDateTime'].']frontMerUrl=['.$p['frontMerUrl'].']backgroundMerUrl=['
					.$p['backgroundMerUrl'].']orderId=['.$p['orderId'].']gopayOutOrderId=['
					.$p['gopayOutOrderId'].']tranIP=['.$p['tranIP'].']respCode=['.$p['respCode']
					.']VerficationCode=['.self::$_VERI_CODE.']';
		$this->_sign = md5($signValue);
	}
	/**
	 * 签名验证
	 */
	public function valid_sign($sign){
		$this->build_sign();
		if($sign==$this->_sign){
			return true;
		}else{
			return false;
		}
	}
	/**
	 * 生成字符串链接
	 * 	(待转换编码)
	 */
	public function create_string(){
		$string = '';
		foreach($this->_params as $k=>$v){
			if($k!='frontMerUrl'){
				//$this->_params[$k] = urlencode($v);
				$this->_params[$k] = $v;
			}
		}
		return http_build_query($this->_params);
	}
	/**
	 * 创建支付链接
	 */
	public function create_url(){
		$str = $this->create_string();
		return $this->_gateway.$str.'&signValue='.$this->_sign;
	}
	/**
	 * 创建支付表单
	 */
	public function create_form(){
		  $form = "<form id='gopaysubmit' name='gopaysubmit' target='_blank' action='".$this->_gateway."' method='post'>";
		 while (list ($key, $val) = each ($this->_params)) {
            $form.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }
        $form .="<input type='hidden' name='signValue' value='".$this->_sign."'/></form>";
        $form .="<button type='button' name='v_action' value='国付宝确认付款' onClick='document.forms[\"gopaysubmit\"].submit();'>国付宝确认付款</button>";
        return $form;
	}
	/**
	 * 银行列表
	 */
	public static function bank_code(){
		return array(
					 'CCB'=>'中国建设银行',
					 'CMB'=>'招商银行',
					 'ICBC'=>'中国工商银行',
					 'BOC'=>'中国银行',
					 'ABC'=>'中国农业银行',
					 'BOCOM'=>'交通银行',
					 'CMBC'=>'中国民生银行',
					 'HXBC'=>'华夏银行',
					 'CIB'=>'兴业银行',
					 'SPDB'=>'上海浦东发展银行',
					 'GDB'=>'广东发展银行',
					 'CITIC'=>'中信银行',
					 'CEB'=>'光大银行',
					 'PSBC'=>'中国邮政储蓄银行',
					 'SDB'=>'深圳发展银行');
	}
	/**
	 * 错误码
	 */
	public function output(){
		$arr = array('0000'=>'交易成功',
					 'GT01'=>'系统内部错误',
					 'GT03'=>'通讯失败',
					 'GT11'=>'虚拟账户不存在',
					 'GT12'=>'用户没有注册国付宝账户',
					 'GT14'=>'该笔记录不存在',
					 'GT16'=>'缺少字段',
					 'MR01'=>'验证签名失败',
					 'MR02'=>'报文某字段格式错误',
					 'MR03'=>'报文某字段超出允许范围',
					 'MR04'=>'报文某必填字段为空',
					 'MR05'=>'交易类型不存在',
					 'MR06'=>'证书验证失败或商户校验信息验证失败',
					 'MN01'=>'超过今日消费上限',
					 'MN02'=>'超过单笔消费上限',
					 'MN03'=>'超过今日提现上限',
					 'MN04'=>'超过单笔提现上限',
					 'MN07'=>'余额不足',
					 'MN11'=>'商户上送的单笔订单交易金额超限',
					 'SM01'=>'商户不存在',
					 'SM02'=>'商户状态非正常 ',
					 'SM03'=>'商户不允许支付',
					 'SM04'=>'商户不具有该权限',
					 'SC10'=>'支付密码错误',
					 'SC11'=>'卡状态非正常',
					 'SC13'=>'国付宝号不正确',
					 'ST07'=>'支付类交易代码匹配失败',
					 'ST10'=>'交易重复，订单号已存在',
					 'ST12'=>'不允许（商户||企业）给个人转账',
					 'ST13'=>'不允许自己给自己支付或转帐',
					 'SU05'=>'不存在该用户',
					 'SU10'=>'用户状态非正常',
					 '9999'=>'订单处理中');
		return $arr[$this->_ecode];
	}
}