<?php
namespace yangweijie\GbTools;

// https://github.com/bluesky335/IDCheck
class GB11643_1999{
	// 英文
	public $name = 'Citizen identification number';
	
	// 中文
	public $title = '居民身份证';

	public $code  = 'GB11643-1999';

	private $_addressCodeList = []; // 现行地址码数据
    private $_addressCodeTimeline = []; // 地址码变更时间线

    // 生肖
    public $chineseZodiac = [
    	'子鼠',
	    '丑牛',
	    '寅虎',
	    '卯兔',
	    '辰龙',
	    '巳蛇',
	    '午马',
	    '未羊',
	    '申猴',
	    '酉鸡',
	    '戌狗',
	    '亥猪',
    ];

    // 星座
    public $constellation = [
    	1 => [
	        'name'       => '水瓶座',
	        'start_date' => '01-20',
	        'end_date'   => '02-18',
	    ],
	    2 => [
	        'name'       => '双鱼座',
	        'start_date' => '02-19',
	        'end_date'   => '03-20',
	    ],
	    3 => [
	        'name'       => '白羊座',
	        'start_date' => '03-21',
	        'end_date'   => '04-19',
	    ],
	    4 => [
	        'name'       => '金牛座',
	        'start_date' => '04-20',
	        'end_date'   => '05-20',
	    ],
	    5 => [
	        'name'       => '双子座',
	        'start_date' => '05-21',
	        'end_date'   => '06-21',
	    ],
	    6 => [
	        'name'       => '巨蟹座',
	        'start_date' => '06-22',
	        'end_date'   => '07-22',
	    ],
	    7 => [
	        'name'       => '狮子座',
	        'start_date' => '07-23',
	        'end_date'   => '08-22',
	    ],
	    8 => [
	        'name'       => '处女座',
	        'start_date' => '08-23',
	        'end_date'   => '09-22',
	    ],
	    9 => [
	        'name'       => '天秤座',
	        'start_date' => '09-23',
	        'end_date'   => '10-23',
	    ],
	    10 => [
	        'name'       => '天蝎座',
	        'start_date' => '10-24',
	        'end_date'   => '11-22',
	    ],
	    11 => [
	        'name'       => '射手座',
	        'start_date' => '11-23',
	        'end_date'   => '12-21',
	    ],
	    12 => [
	        'name'       => '摩羯座',
	        'start_date' => '12-22',
	        'end_date'   => '01-19',
	    ],
    ];

    /**
     * IdValidator constructor.
     */
    public function __construct()
    {
        $this->_addressCodeList     = include __DIR__.'/../data/addressCode.php';
        $this->_addressCodeTimeline = include __DIR__.'/../data/addressCodeTimeline.php';
    }

	/**
     * 获取地址码信息.
     *
     * @param string $addressCode  地址码
     * @param string $birthdayCode 出生日期码
     *
     * @return bool|mixed|string
     */
    private function _getAddressInfo($addressCode, $birthdayCode)
    {
        $addressInfo = [
            'province' => '',
            'city'     => '',
            'district' => '',
        ];

        // 省级信息
        $provinceAddressCode = substr($addressCode, 0, 2).'0000';
        $addressInfo['province'] = $this->_getAddress($provinceAddressCode, $birthdayCode);

        $firstCharacter = substr($addressCode, 0, 1); // 用于判断是否是港澳台居民居住证（8字开头）

        // 港澳台居民居住证无市级、县级信息
        if ($firstCharacter == '8') {
            return $addressInfo;
        }

        // 市级信息
        $cityAddressCode = substr($addressCode, 0, 4).'00';
        $addressInfo['city'] = $this->_getAddress($cityAddressCode, $birthdayCode);

        // 县级信息
        $addressInfo['district'] = $this->_getAddress($addressCode, $birthdayCode);

        return empty($addressInfo['province']) ? false : $addressInfo;
    }

    /**
     * 获取省市区地址码.
     *
     * @param string $addressCode  地址码
     * @param string $birthdayCode 出生日期码
     *
     * @return string
     */
    private function _getAddress($addressCode, $birthdayCode)
    {
        $address = '';
        if (isset($this->_addressCodeTimeline[$addressCode])) {
            $timeline = $this->_addressCodeTimeline[$addressCode];
            $year = substr($birthdayCode, 0, 4);
            foreach ($timeline as $key => $val) {
                $start_year = $val['start_year'] != '' ? $val['start_year'] : '0001';
                $end_year = $val['end_year'] != '' ? $val['end_year'] : '9999';
                if ($year >= $start_year and $year <= $end_year) {
                    $address = $val['address'];
                }
            }
        }

        return $address;
    }

    /**
     * 获取星座信息.
     *
     * @param string $birthdayCode 出生日期码
     *
     * @return string
     */
    private function _getConstellation($birthdayCode)
    {
        $constellationList = include __DIR__.'/../data/constellation.php';
        $month = (int) substr($birthdayCode, 4, 2);
        $day = (int) substr($birthdayCode, 6, 2);

        $start_date = $constellationList[$month]['start_date'];
        $start_day = (int) explode('-', $start_date)[1];

        if ($day < $start_day) {
            $tmp_month = $month == 1 ? 12 : $month - 1;

            return $constellationList[$tmp_month]['name'];
        } else {
            return $constellationList[$month]['name'];
        }
    }

    /**
     * 获取生肖信息.
     *
     * @param string $birthdayCode 出生日期码
     *
     * @return mixed
     */
    private function _getChineseZodiac($birthdayCode)
    {
        $chineseZodiacList = include __DIR__.'/../data/chineseZodiac.php';
        $start = 1900; // 子鼠
        $end = substr($birthdayCode, 0, 4);
        $key = ($end - $start) % 12;

        return $chineseZodiacList[$key];
    }

    /**
     * 生成顺序码
     *
     * @param int $sex 性别
     *
     * @return int|string
     */
    private function _generatorOrderCode($sex)
    {
        $orderCode = rand(111, 999);

        if ($sex !== null && $sex !== $orderCode % 2) {
            $orderCode -= 1;
        }

        return $orderCode;
    }

    /**
     * 生成出生日期码
     *
     * @param $addressCode
     * @param $address
     * @param int|string $birthday 出生日期
     *
     * @return string
     */
    private function _generatorBirthdayCode($addressCode, $address, $birthday)
    {
        $start_year = '0001';
        $end_year   = '9999';
        $year       = $this->_datePad(substr($birthday, 0, 4), 'year');
        $month      = $this->_datePad(substr($birthday, 4, 2), 'month');
        $day        = $this->_datePad(substr($birthday, 6, 2), 'day');

        if ($year < 1800 || $year > date('Y')) {
            $year = $this->_datePad(rand(1950, date('Y') - 1), 'year');
        }

        if (isset($this->_addressCodeTimeline[$addressCode])) {
            $timeline = $this->_addressCodeTimeline[$addressCode];
            foreach ($timeline as $key => $val) {
                if ($val['address'] == $address) {
                    $start_year = $val['start_year'] != '' ? $val['start_year'] : $start_year;
                    $end_year   = $val['end_year'] != '' ? $val['end_year'] : $end_year;
                }
            }
        }

        if ($year < $start_year) {
            $year = $start_year;
        }
        if ($year > $end_year) {
            $year = $end_year;
        }

        if ($month < 1 || $month > 12) {
            $month = $this->_datePad(rand(1, 12), 'month');
        }

        if ($day < 1 || $day > 31) {
            $day = $this->_datePad(rand(1, 28), 'day');
        }

        if (!checkdate((int) $month, (int) $day, (int) $year)) {
            $year  = $this->_datePad(rand(max($start_year, 1950), min($end_year, date('Y')) - 1), 'year');
            $month = $this->_datePad(rand(1, 12), 'month');
            $day   = $this->_datePad(rand(1, 28), 'day');
        }

        return $year.$month.$day;
    }

    /**
     * 生成地址码
     *
     * @param string $address 地址（行政区全称）
     *
     * @return false|int|string
     */
    private function _generatorAddressCode($address)
    {
        $addressCode = array_search($address, $this->_addressCodeList);
        $classification = $this->_addressCodeClassification($addressCode);
        switch ($classification) {
            case 'country':
                $pattern = '/\d{4}(?!00)[0-9]{2}$/';
                $addressCode = $this->_getRandAddressCode($pattern);
                break;
            case 'province':
                $provinceCode = substr($addressCode, 0, 2);
                $pattern = '/^'.$provinceCode.'\d{2}(?!00)[0-9]{2}$/';
                $addressCode = $this->_getRandAddressCode($pattern);
                break;
            case 'city':
                $cityCode = substr($addressCode, 0, 4);
                $pattern = '/^'.$cityCode.'(?!00)[0-9]{2}$/';
                $addressCode = $this->_getRandAddressCode($pattern);
                break;
        }

        return $addressCode;
    }

    /**
     * 生成校验码
     * 详细计算方法 @lint https://zh.wikipedia.org/wiki/中华人民共和国公民身份号码
     *
     * @param string $body 身份证号 body 部分
     *
     * @return string
     */
    private function _generatorCheckBit($body)
    {
        // 位置加权
        $posWeight = [];
        for ($i = 18; $i > 1; $i--) {
            $weight = pow(2, $i - 1) % 11;
            $posWeight[$i] = $weight;
        }

        // 累身份证号 body 部分与位置加权的积
        $bodySum = 0;
        $bodyArray = str_split($body);
        $count = count($bodyArray);
        for ($j = 0; $j < $count; $j++) {
            $bodySum += (intval($bodyArray[$j]) * $posWeight[18 - $j]);
        }

        // 生成校验码
        $checkBit = (12 - ($bodySum % 11)) % 11;

        return $checkBit == 10 ? 'X' : (string) $checkBit;
    }

    /**
     * 地址码分类.
     *
     * @param $addressCode
     *
     * @return string
     */
    private function _addressCodeClassification($addressCode)
    {
        if (!$addressCode) {
            // 全国
            return 'country';
        }
        if (substr($addressCode, 0, 1) == 8) {
            // 港澳台
            return 'special';
        }
        if (substr($addressCode, 2, 4) == '0000') {
            // 省级
            return 'province';
        }
        if (substr($addressCode, 4, 2) == '00') {
            // 市级
            return 'city';
        }
        // 县级
        return 'district';
    }

    /**
     * 获取随机地址码.
     *
     * @param string $pattern 模式
     *
     * @return string
     */
    private function _getRandAddressCode($pattern)
    {
        $keys = array_keys($this->_addressCodeList);
        $result = preg_grep($pattern, $keys);

        return $result[array_rand($result)];
    }

    /**
     * 日期补全.
     *
     * @param string|int $date 日期
     * @param string     $type 类型
     *
     * @return string
     */
    private function _datePad($date, $type = 'year')
    {
        $padLength = $type == 'year' ? 4 : 2;
        $newDate = str_pad($date, $padLength, '0', STR_PAD_LEFT);

        return $newDate;
    }

	/**
     * 检查并拆分身份证号.
     *
     * @param string $id 身份证号
     *
     * @return array|bool
     */
    private function _checkIdArgument($id)
    {
        $id = strtoupper($id);
        $length = strlen($id);

        if ($length === 15) {
            return $this->_generateShortType($id);
        } elseif ($length === 18) {
            return $this->_generatelongType($id);
        }

        return false;
    }

    /**
     * Generation for the short type.
     *
     * @param string $id 身份证号
     *
     * @return array
     */
    private function _generateShortType($id)
    {
        preg_match('/(.{6})(.{6})(.{3})/', $id, $matches);

        return [
            'body'         => $matches[0],
            'addressCode'  => $matches[1],
            'birthdayCode' => '19'.$matches[2],
            'order'        => $matches[3],
            'checkBit'     => '',
            'type'         => 15,
        ];
    }

    /**
     * Generation for the long type.
     *
     * @param string $id 身份证号
     *
     * @return array
     */
    private function _generateLongType($id)
    {
        preg_match('/((.{6})(.{8})(.{3}))(.)/', $id, $matches);

        return [
            'body'         => $matches[1],
            'addressCode'  => $matches[2],
            'birthdayCode' => $matches[3],
            'order'        => $matches[4],
            'checkBit'     => $matches[5],
            'type'         => 18,
        ];
    }

    /**
     * 检查地址码
     *
     * @param string $addressCode  地址码
     * @param string $birthdayCode 出生日期码
     *
     * @return bool
     */
    private function _checkAddressCode($addressCode, $birthdayCode)
    {
        return (bool) $this->_getAddressInfo($addressCode, $birthdayCode);
    }

    /**
     * 检查顺序码
     *
     * @param string $orderCode 顺序码
     *
     * @return bool
     */
    private function _checkOrderCode($orderCode)
    {
        return strlen($orderCode) === 3;
    }

    /**
     * 检查出生日期码
     *
     * @param string $birthdayCode 出生日期码
     *
     * @return bool
     */
    private function _checkBirthdayCode($birthdayCode)
    {
        $date = DateTime::createFromFormat($format = 'Ymd', $birthdayCode);

        return $date->format($format) === $birthdayCode && (int) $date->format('Y') >= 1800;
    }

    /**
     * 验证身份证号合法性.
     *
     * @param string $id 身份证号
     *
     * @return bool
     */
    public function isValid($id)
    {
        // 基础验证
        $code = $this->_checkIdArgument($id);
        if (empty($code)) {
            return false;
        }

        // 分别验证：*地址码*、*出生日期码*和*顺序码*
        if (!$this->_checkAddressCode($code['addressCode'], $code['birthdayCode']) || !$this->_checkBirthdayCode($code['birthdayCode']) || !$this->_checkOrderCode($code['order'])) {
            return false;
        }

        // 15位身份证不含校验码
        if ($code['type'] === 15) {
            return true;
        }

        // 验证：校验码
        $checkBit = $this->_generatorCheckBit($code['body']);

        // 检查校验码
        return $checkBit == $code['checkBit'];
    }

    /**
     * 获取身份证信息.
     *
     * @param string $id 身份证号
     *
     * @return array|bool
     */
    public function getInfo($id)
    {
        // 验证有效性
        if ($this->isValid($id) === false) {
            return false;
        }
        $code        = $this->_checkIdArgument($id);
        $addressInfo = $this->_getAddressInfo($code['addressCode'], $code['birthdayCode']);

        return [
            'addressCode'   => $code['addressCode'],
            'abandoned'     => isset($this->_addressCodeList[$code['addressCode']]) ? 0 : 1,
            'address'       => is_array($addressInfo) ? implode($addressInfo) : '',
            'addressTree'   => array_values($addressInfo),
            'birthdayCode'  => date('Y-m-d', strtotime($code['birthdayCode'])),
            'constellation' => $this->_getConstellation($code['birthdayCode']),
            'chineseZodiac' => $this->_getChineseZodiac($code['birthdayCode']),
            'sex'           => ($code['order'] % 2 === 0 ? 0 : 1),
            'length'        => $code['type'],
            'checkBit'      => $code['checkBit'],
        ];
    }


    /**
     * * 生成假数据.
     *
     * @param bool            $isEighteen 是否为 18 位
     * @param null|string     $address    地址
     * @param null|string|int $birthday   出生日期
     * @param null|int        $sex        性别（1为男性，0位女性）
     *
     * @return string
     */
    public function fakeId($isEighteen = true, $address = null, $birthday = null, $sex = null)
    {
        // 生成地址码
        if (empty($address)) {
            $addressCode = array_rand($this->_addressCodeList);
            $address = $this->_addressCodeList[$addressCode];
        } else {
            $addressCode = $this->_generatorAddressCode($address);
        }

        // 出生日期码
        $birthdayCode = $this->_generatorBirthdayCode($addressCode, $address, $birthday);

        // 顺序码
        $orderCode = $this->_generatorOrderCode($sex);

        if (!$isEighteen) {
            return $addressCode.substr($birthdayCode, 2).$orderCode;
        }

        $body = $addressCode.$birthdayCode.$orderCode;

        $checkBit = $this->_generatorCheckBit($body);

        return $body.$checkBit;
    }

	/**
     * 15位升级18位号码.
     *
     * @param string $id 身份证号
     *
     * @return bool|string
     */
    public function upgradeId($id)
    {
        if (!$this->isValid($id)) {
            return false;
        }
        $code = $this->_generateShortType($id);
        $body = $code['addressCode'].$code['birthdayCode'].$code['order'];

        return $body.$this->_generatorCheckBit($body);
    }
}