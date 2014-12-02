<?php

/**
 * Date.php Copyright 2014
 * https://github.com/unlike777/Laravel-localized-format-date
 *
 * Класс для локализованного форматирования даты
 */

if(!function_exists('mb_ucfirst')) {
	function mb_ucfirst($str, $encoding = NULL) {
		if($encoding === NULL)  {
			$encoding    = mb_internal_encoding();
		}

		return mb_substr(mb_strtoupper($str, $encoding), 0, 1, $encoding) . mb_substr($str, 1, mb_strlen($str)-1, $encoding);
	}
}

class Date {
	
	protected static $time;
//	protected static $lang;
	
	public function __construct($time = false) {
		self::setTime($time);
	}
	
//	public static function setLocale($lang) {
//		self::$lang = $lang;
//	}

	/**
	 * работаем с текущей датой
	 * @return Date
	 */
	public static function now() {
		return new self();
	}

	/**
	 * парсим время
	 * @param bool $time
	 * @return Date
	 */
	public static function parse($time = false) {
		return new self($time);
	}

	/**
	 * Устанавливаем время в формате timestamp
	 * @param bool $time
	 */
	private static function setTime($time = false)
	{
		if ($time === false) {
			$time = time();
		}

		if (!is_numeric($time) && is_string($time)) {
			$time = strtotime($time);
		}

		self::$time = $time;
	}

	/**
	 * Заменяет аббревиатуры на нужные значения
	 * 
	 * @param $format - формат даты "j mmm Y H:i"
	 * @param $num - порядковый номер в массиве
	 * @param $arr - массив соответсвия, какой элемент массива соответсвует той или иной аббревиатуре
	 */
	private static function replace(&$format, $num, $type, $arr) {
		
		foreach ($arr as $key => $val) {
			$word = Lang::get($type);
			$word = $word[$val][$num];
			$format = str_replace($key, $word, $format);
			$format = str_replace(mb_strtoupper($key, 'UTF-8'), mb_ucfirst($word, 'UTF-8'), $format);
		}
		
	}
	
	/**
	 * Форматирует дату в заданном формате
	 * поддерживаемые аббревиатуры:
	 * dd, ddd - день недели
	 * mm, mmm, mmmm - месяц
	 * для получения первой буквы заглавной, используем DD, MMM 
	 * 
	 * @param string $format
	 * @return bool|string
	 */
	public function format($format = 'Y-m-d H:i:s') {

		$num_day = date('w', self::$time) + 1;
		self::replace($format, $num_day, 'DAY', array(
			'ddd' => 1,
			'dd' => 2,
		));
		
		$num_month = date('m', self::$time);
		self::replace($format, $num_month, 'MONTH', array(
			'mmmm' => 1,
			'mmm' => 2,
			'mm' => 3,
		));
		
		return date($format, self::$time);
	}
	
}