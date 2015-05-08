<?php

/*
    Bagira.CMS Copyright 2015

    Класс для работы с сессией
*/

class Session {
	
	/**
	 * Получаем значение
	 * @param $name
	 * @param null $def
	 * @return null
	 */
	public static function get($key, $def = NULL) {
		return self::arrayGet($_SESSION, $key, $def);
	}
	
	/**
	 * Устанавливаем значение
	 * @param $name
	 * @param $val
	 */
	public static function set($key, $val) {
		self::arraySet($_SESSION, $key, $val);
	}
	
	/**
	 * Проверяем на существование
	 * @param $name
	 * @return bool
	 */
	public static function has($key) {
		return ! is_null(self::get($key));
	}
	
	/**
	 * Получаем всю сессию
	 * @return mixed
	 */
	public static function all() {
		return $_SESSION;
	}
	
	/**
	 * Удаляем значение
	 * @param $name
	 */
	public static function remove($key) {
		self::arrayRemove($_SESSION, $key);
	}
	
	/**
	 * очищаем всю сессию 
	 */
	public static function clear() {
		foreach ($_SESSION as $key => $val) {
			unset($_SESSION[$key]);
		}
	}
	
	/**
	 * добавляем элемент к массиву
	 */
	public static function push($key, $val) {
		$array = self::get($key, array());
		$array[] = $val;
		self::set($key, $array);
	}
	
	/**
	 * @param $array
	 * @param $key
	 */
	private static function arrayRemove(&$array, $key) {
		$keys = explode('.', $key);
		
		while (count($keys) > 1) {
			$key = array_shift($keys);
			
			if ( ! isset($array[$key]) or ! is_array($array[$key])) {
				return;
			}
			
			$array =& $array[$key];
		}
		
		unset($array[array_shift($keys)]);
	}
	
	/**
	 * @param $str
	 * @return array
	 */
	private static function strToArr($str) {
		$str = trim($str);
		$str = explode('.', $str);
		foreach($str as $key => $item) {
			$str[$key] = trim($item);
		}
		return array_filter($str);
	}
	
	/**
	 * @param $array
	 * @param $key
	 * @param $value
	 */
	private static function arraySet(&$array, $key, $value) {
		if (is_null($key)) return;
		
		$keys = self::strToArr($key);
		
		while (count($keys) > 1) {
			$key = array_shift($keys);
			if ( ! isset($array[$key]) or ! is_array($array[$key])) {
				$array[$key] = array();
			}
			
			$array =& $array[$key];
		}
		
		$array[array_shift($keys)] = $value;
	}
	
	private static function arrayGet($array, $key, $default = null) {
		if (is_null($key)) return $array;
		
		if (isset($array[$key])) return $array[$key];
		
		foreach (explode('.', $key) as $segment) {
			if ( ! is_array($array) or ! array_key_exists($segment, $array)){	
				return $default;
			}
		
			$array = $array[$segment];
		}
		
		return $array;
	}
}

?>