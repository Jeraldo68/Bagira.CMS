<?php

/**
 * Class ormFactory
 * 
 * Фабрика объектов
 * 
 * Если в системе имеется класс декортатор наследованный от ormDecorator
 * и совпадает с системным именем класса созданного в конструкторе, но с префиксом orm (например: ormNews)
 * то этот объект будет обернут декоратором
 * 
 */

class ormFactory {
	
	protected static $objects = array();
	protected static $system_classes = array('ormPage', 'ormObject', 'ormFactory', 'ormDecorator', 'ormClass', 'ormField', 'ormSelect');

	/**
	 * Получаем готовый объект
	 * @param int $obj_id
	 * @param string $class - фильтрация по классу 
	 * @return bool|ormObject|ormPage
	 */
	static function get($obj_id, $class = '') {
		
		if (isset(self::$objects[$obj_id])) {
			
			if (!empty($class)) {
				if (self::$objects[$obj_id]->getClass()->getSName() != $class) {
					return false;
				}
			}
			
			return self::$objects[$obj_id];
		}

		/**
		 * @var ormObject|ormPage|ormDecorator
		 */

		if (!($obj = ormPages::get($obj_id, $class))) {
			$obj = ormObjects::get($obj_id, $class);
		}
		
		if ($obj) {
			$class_name = $obj->getClass()->getSName();

			$ormClass = self::getOrmClassName($class_name);
			if (!in_array($ormClass, self::$system_classes) && class_exists($ormClass)) {
				$obj = new $ormClass($obj);

				if (!$obj->isOk()) {
					return false;
				}
			}
		}
		
		self::$objects[$obj_id] = $obj;
		
		return $obj;
	}


	/**
	 * создаем новый объект
	 * @param $class - класс объекта
	 * @return bool|ormObject|ormPage|ormDecorator
	 */
	static function create($class_name) {

		if (!($class = ormClasses::get($class_name))) {
			return false;
		}
		
		if ($class->isPage()) {
			$obj = new ormPage();
		} else {
			$obj = new ormObject();
		}
		$obj->setClass($class_name);

		$ormClass = self::getOrmClassName($class_name);
		if (!in_array($ormClass, self::$system_classes) && class_exists($ormClass)) {
			$obj = new $ormClass($obj);
		}
		
		return $obj;
	}

	/**
	 * генерирует название ormClass'a по системному имени объекта
	 * @param $name
	 * @return string
	 */
	static function getOrmClassName($name) {
		$name = strtolower($name);
		return 'orm'.ucfirst($name);
	}
	
}

?>