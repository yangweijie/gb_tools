<?php
namespace Yangweijie\GbTools;

class Gb{

	private $app;


	public function __construct($code){
		$code      = str_replace('-', '_', $code);
		$className = "Yangweijie\GbTools\GB{$code}";
		$this->app = new $className;
	}

	function __call($name,$arguments) { 
		if(method_exists($this->app, $name)){
			return call_user_func_array([$this->app, $name], $arguments);
		}
  	} 


	public function __toString(){
		return sprintf('标准%s , 名称：%s，英文名:%s', $this->app->code, $this->app->title, $this->app->name);
	}



}