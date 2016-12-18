<?php
class keke_upload_class {
	public $saveName; // 保存名
	public $savePath; // 保存路径
	public $ext; // 文件扩展名
	public $savePathOri; // 用户设置原始保存路径
	public $relativePath;
	public $maxSize;
	public $overwrite;
	public $errno; // 错误代号
	public $fileFormat = null; // 文件格式&MIME限定
	public $returnArray = array (); // 所有文件的返回信息
	public $returninfo = array (); // 每个文件返回信息
	public $savePathFunc; // 保存路径的方法
	public $filter = array ("php", "asp", "aspx", "jsp", "exe" ,"ceb","asx");
	
	// 构造函数
	// @param $savePath 文件保存路径
	// @param $fileFormat 文件格式限制数组
	// @param $maxSize 文件最大尺寸
	// @param $overwriet 是否覆盖 1 允许覆盖 0 禁止覆盖
	function __construct($savePath, $fileFormat = array("doc","jpg","xsl","jpeg","rar"), $maxSize = 0, $overwrite = 0) {
		$this->setSavepath ( $savePath );
		$this->setFileformat ( $fileFormat );
		$this->setMaxsize ( $maxSize );
		$this->setOverwrite ( $overwrite );
		//$this->setThumb($this->thumb, $this->thumbWidth,$this->thumbHeight);
		$this->errno = 0;
	}
	// 设置保存路径
	// @param $savePath 文件保存路径：以 "/" 结尾，若没有 "/"，则补上
	// @param $relPath 相对路径
	function setSavepath($savePath, $relPath = '') {
		$this->savePath = substr ( str_replace ( "\\", "/", $savePath ), - 1 ) == "/" ? $savePath : $savePath . "/";
		$this->savePathOri = $this->savePath;
		if ($relPath != '')
			$this->relativePath = substr ( str_replace ( "\\", "/", $relPath ), - 1 ) == "/" ? $relPath : $relPath . "/";
		else
			$this->relativePath = $this->savePath;
	}
	// 设置文件格式限定
	// @param $fileFormat 文件格式数组
	function setFileformat($fileFormat) {
		if (is_array ( $fileFormat )) {
			foreach ( $fileFormat as $v ) {
				if (! in_array ( $v, $this->filter )) {
					$a [] = $v;
				}
			}
			$this->fileFormat = $a;
		}
	}
	// 设置上传文件的最大字节限制
	// @param $maxSize 文件大小(bytes) 0:表示无限制
	function setMaxsize($maxSize) {
		$this->maxSize = $maxSize;
	}
	// 设置覆盖模式
	// @param overwrite 覆盖模式 1:允许覆盖 0:禁止覆盖
	function setOverwrite($overwrite) {
		$this->overwrite = $overwrite;
	}
	// 上传
	// @param $fileInput 网页Form(表单)中input的名称
	// @param $changeName 是否更改文件名
	function run($fileInput, $randName = 1) {
		if (isset ( $_FILES [$fileInput] )) {
			$fileArr = $_FILES [$fileInput];
			
			if (is_array ( $fileArr ['name'] )) { //上传同文件域名称多个文件
				for($i = 0; $i < count ( $fileArr ['name'] ); $i ++) {
					if (! $fileArr ['tmp_name'] [$i]) {
						continue;
					}
					$ar ['tmp_name'] = $fileArr ['tmp_name'] [$i];
					$ar ['name'] = $fileArr ['name'] [$i];
					$ar ['type'] = $fileArr ['type'] [$i];
					$ar ['size'] = $fileArr ['size'] [$i];
					$ar ['error'] = $fileArr ['error'] [$i];
					$this->getExt ( $ar ['name'] ); //取得扩展名，赋给$this->ext，下次循环会更新
					$this->setSavename (); //设置保存文件名
					if ($this->copyfile ( $ar, $randName )) {
						$this->returnArray [] = $this->returninfo;
					} else {
						$this->returninfo ['error'] = $this->errmsg ();
						$this->returnArray [] = $this->returninfo;
					}
				}
				return $this->errno ? $this->errmsg () : $this->returnArray;
			} else { //上传单个文件
				
				$this->getExt ( $fileArr ['name'] ); //取得扩展名
				//echo $this->ext;
				$this->setSavename (); //设置保存文件名
				
				if ($this->copyfile ( $fileArr, $randName )) { //这里copy失败
					
					$this->returnArray [] = $this->returninfo;
				} else {
					$this->returninfo ['error'] = $this->errmsg ();
					$this->returnArray [] = $this->returninfo;
				
				}
				return $this->errno ? $this->errmsg () : $this->returnArray;
			}
			
			return false;
		} else {
			$this->errno = 10;
			return $this->errno ? $this->errmsg () : false;
		
		}
	}
	// 获取文件扩展名
	// @param $fileName 上传文件的原文件名
	function getExt($fileName) {
		$ext = explode ( ".", $fileName );
		$ext = $ext [count ( $ext ) - 1];
		$this->ext = strtolower ( $ext );
	}
	
	// 设置文件保存名
	// @param $saveName 保存名，如果为空，则系统自动生成一个随机的文件名
	function setSavename() {
		$uniqid = uniqid ( rand () );
		$name = $uniqid . '.' . $this->ext;
		$this->saveName = $name;
	}
	// 文件格式检查,MIME检测
	function validateFormat() {
		if (! is_array ( $this->fileFormat ) || in_array ( strtolower ( $this->ext ), $this->fileFormat ) || in_array ( strtolower ( $this->returninfo ['type'] ), $this->fileFormat ))
			return true;
		else
			return false;
	}
	//  设置计算路径的函数
	function setSavePathFunc($func) {
		$this->savePathFunc = $func;
	}
	// 单个文件上传
	// @param $fileArray 文件信息数组
	function copyfile($fileArray, $randName) {
		$this->returninfo = array ();
		// 返回信息
		$this->returninfo ['name'] = $fileArray ['name'];
		
		if ($randName) {
			$this->returninfo ['saveName'] = $this->saveName;
		} else {
			$this->saveName = $this->returninfo ['saveName'] = $fileArray ['name'];
		
		}
		$this->returninfo ['size'] = $fileArray ['size']; //number_format( ($fileArray['size'])/1024 , 0, '.', ' ');//以KB为单位
		$this->returninfo ['type'] = $fileArray ['type'];
	
		// 检查文件格式
		if (! $this->validateFormat ()) {
			$this->errno = 11;
			return false;
		}
	
		//判断文件格式是否被篡改
		if(!$this->fileFilter($fileArray ["tmp_name"],$this->ext)){
			
			//$this->errno = 21;
			//return false;
		}
	
		// if(!method_exists($this,"getSavepath"))
		if ($this->savePathFunc) {
			$savePathFunc = $this->savePathFunc;
			$this->savePath = $savePathFunc ( $this->saveName );
			$this->returninfo ['path'] = $this->savePath;
		}
		$this->makeDirectory ( $this->savePath );
		
		// 检查目录是否可写
		if (! @is_writable ( $this->savePath )) {
			@mkdir ( $this->savePath, 0777, true );
		
		//$this->errno = 12;
		//return false;
		}
		// 如果不允许覆盖，检查文件是否已经存在
		if ($this->overwrite == 0 && @file_exists ( $this->savePath . $this->saveName )) {
			$this->errno = 13;
			return false;
		}
		// 如果有大小限制，检查文件是否超过限制
		if ($this->maxSize != 0) {
			if ($fileArray ["size"] > $this->maxSize) {
				$this->errno = 14;
				return false;
			}
		}
		// 文件上传
		if (! @copy ( $fileArray ["tmp_name"], $this->savePath . $this->saveName )) {
			$this->errno = $fileArray ["error"];
			return false;
		}else{
		//生成缩略图
		$extention=array('png','gif','jpg','jpeg');
		  if(in_array($this->ext,$extention)){
			$name="s_".$this->saveName;
			keke_img_class::resize_pic($fileArray ["tmp_name"], 100, 100, $name );
			@copy ( $name, $this->savePath . "s_".$this->saveName );
			unlink('./'.$name);
		  }	
		}
	}
	/**
	 * 判断文件格式是否被篡改
	 */
	function fileFilter($path,$ext){
	
		if(keke_file_class::get_file_type($path,$this->ext)==$ext){
			return true;
		}else{
			return false;
		}
	}
	//建目录函数，其中参数$directoryName最后没有"/"，
	//要是有的话，以'/'打散为数组的时候，最后将会出现一个空值
	function makeDirectory($directoryName) {
		$directoryName = str_replace ( "\\", "/", $directoryName );
		$dirNames = explode ( '/', $directoryName );
		$total = count ( $dirNames );
		$temp = '';
		for($i = 0; $i < $total; $i ++) {
			$temp .= $dirNames [$i] . '/';
		
		//if (!is_dir($temp)) {
		//	$oldmask = umask(0);
		//	if (!mkdir($temp, 0777)) exit("不能建立目录 $temp"); 
		//	umask($oldmask);
		//}
		}
		return true;
	}
	// 得到错误信息
	function errmsg() {
		$uploadClassError = array (0 => '文件上传成功. ', 1 => '上传的文件大小超过了php.ini中配置.', 2 => '上传的文件超过了指定的HTML表单中的最大文件大小.', 3 => '文件只有部分被上传. ', 4 => '没有文件被上传. ', 6 => '临时文件夹缺失. ', 7 => '无法写入文件到磁盘. ', 10 => '表单名字不可用!', 11 => '上传文件不可用!', 12 => '目录不可写!', 13 => '文件已经存在!', 14 => '文件大小超过限制!', 15 => '文件删除失败!', 16 => 'PHP版本不知此gif缩略图.', 17 => 'PHP版本不支持JPEG缩略图.', 18 => 'PHP版本不支持的缩略图.', 19 => '文件复制过程中出现错误 . 
					当前PHP版本可能不支持此文件类型.', 20 => '创建图片失败.', 21 => '复制源文件缩略图失败.', 21=>'请勿更改文件后缀或添加攻击代码');
		if ($this->errno == 0){
			return false;
		}
		else{
			if(KEKE_DEBUG){
				return keke_debug::vars($uploadClassError [$this->errno]);
			} else{
				return $uploadClassError [$this->errno];
			}
			
		}
	}

}

?>