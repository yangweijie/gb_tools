<?php
namespace yangweijie\GbTools;

class GB11643_1999{
	// 英文
	public $name = 'Citizen identification number';
	
	// 中文
	public $title = '居民身份证';

	public $code  = 'GB11643-1999';

	// 校验
	public function check($str){
		$str = trim($str);
		$length = strlen($str);
		if(strlen($str) == 15){
			$str = $this->upgrade($str);
			goto check;
		}else if($length == 18){
			check:

		}else{

		}
	}

	// 15位升级18位
	public function upgrade($str){

	}

	public function generate(){

	}
}