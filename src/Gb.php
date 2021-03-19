<?php
namespace yangweijie\GbTools;

class Gb{

	private $app;


	public function __construct($code){
		$code      = str_replace('-', '_', $code);
		$className = "GB{$code}";
		$this->app = new $className;
	}


	public function __toString(){
		return sprintf('标准%s , 名称：%s，英文名:%s', $this->app->code, $this->app->title, $this->app->name);
	}



}