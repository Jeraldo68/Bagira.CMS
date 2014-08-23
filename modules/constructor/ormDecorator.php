<?php

/**
 * Class ormDecorator
 * 
 * Декоратор
 */

class ormDecorator {

	/**
	 * @var ormObject|ormPage
	 */
	protected $obj; //объект который нужно обернуть

	/*
	 * переопределяем в наследуемом классе
	 * 
    public function __construct($obj = false) {
		
		if ($obj === false) {
			
			$obj = new ormObject();
			$obj->setClass('example');
			$this->obj = $obj;
			
		} if (is_numeric($obj)) {
			
			if ($obj = ormObjects::get($obj, 'example')) {
				$this->obj = $obj;
			}
			
		} else if ($obj instanceof ormObject) {
			
			if ($obj->getClass()->getSName() == 'example') {
				$this->obj = $obj;
			}
			
		}
    }
	*/

	/**
	 * Проверяем правильность инициализации класса
	 * @return bool
	 */
	public function isOk() {
		if ($this->obj !== NULL) {
			return true;
		}
		return false;
	}
	
	/**
	 * пробрасываем isset для корректной работы empty()
	 * @param $name
	 * @return bool
	 */
	public function __isset($name) {
		return $this->obj->__isset($name);
	}
	
	/**
	 * пробрасываем геттеры
	 * @param $name
	 * @return any|array|bool|int|mixed|string
	 */
	public function __get($name) {
		return $this->obj->__get($name);
	}


	/**
	 * пробрасываем сеттеры
	 * @param $name
	 * @param $value
	 */
	public function __set($name, $value) {
		$this->obj->__set($name, $value);
	}

	/**
	 * пробрасываем методы
	 * @param $name
	 * @param $arguments
	 * @return any|array|bool|int|mixed|string|NULL
	 */
	public function __call($name, $arguments) {
		if (method_exists($this->obj, $name)) {
			return $this->obj->{$name}($arguments);
		}
		
		return NULL;
	}
	
}

?>