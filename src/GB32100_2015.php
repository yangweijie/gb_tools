<?php
namespace Yangweijie\GbTools;

use VerbalExpressions\PHPVerbalExpressions\VerbalExpressions;

// https://github.com/bluesky335/IDCheck
// 参考 http://c.gb688.cn/bzgk/gb/showGb?type=online&hcno=24691C25985C1073D3A7C85629378AC0
class GB32100_2015{
	// 英文
	public $name = 'Coding rule of the unified social credit identifier for legal entities and other organizations';
	
	// 中文
	public $title = '法人和其他组织统一社会信用代码编码规则';

	public $code  = 'GB32100-2015';

	//代码字符集
	public $charSet = [
		'0',
		'1',
		'2',
		'3',
		'4',
		'5',
		'6',
		'7',
		'8',
		'9',
		'A',
		'B',
		'C',
		'D',
		'E',
		'F',
		'G',
		'H',
		'J',
		'K',
		'L',
		'M',
		'N',
		'P',
		'Q',
		'R',
		'T',
		'U',
		'W',
		'X',
		'Y',
	];

	public $valueMap = [
		'0'=> 0,
		'1'=> 1,
		'2'=> 2,
		'3'=> 3,
		'4'=> 4,
		'5'=> 5,
		'6'=> 6,
		'7'=> 7,
		'8'=> 8,
		'9'=> 9,
		'A'=> 10,
		'B'=> 11,
		'C'=> 12,
		'D'=> 13,
		'E'=> 14,
		'F'=> 15,
		'G'=> 16,
		'H'=> 17,
		'J'=> 18,
		'K'=> 19,
		'L'=> 20,
		'M'=> 21,
		'N'=> 22,
		'P'=> 23,
		'Q'=> 24,
		'R'=> 25,
		'T'=> 26,
		'U'=> 27,
		'W'=> 28,
		'X'=> 29,
		'Y'=> 30,

	];

	public function isValid($str){

		$str         = strtoupper($str);
		$regex       = new VerbalExpressions();

		$regex->startOfLine()
			->range(0, 9,'A', 'Z')
			->limit(18)
			->endOfLine();
		if (!$regex->test($str)) {
		    return false;
		}

		$sum = 0;
		foreach (array_slice(str_split($str), 0, 17) as $index=>$c){
			$value  = $this->valueMap[$c];
			$weight = intval(pow(3, floatval($index))) % 31;
			$sum   += $value * $weight;
		}
		$mod     = $sum % 31;
		$sign    = 31 - $mod;
		if($sign == 31 ){
			$sign = 0;
		}
		$signChar = 0;

		foreach ($this->valueMap as $key => $value) {
			if($value == $sign){
				$signChar = $key;
				break;
			}
		}
		$lastStr = substr($str, 17,18);
		$signStr = strval($signChar);
		return $signStr  == $lastStr;
	}
}