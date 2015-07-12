<?php

/*
    Bagira.CMS Copyright 2015

    Абстрактный класс модель, для работы с БД.
*/

abstract class Model extends innerErrorList {
	
	protected static $table = ''; // <<table>>
	
	protected static $fields  = array();
	protected static $rfields = array(); //обязательные поля
	protected static $zfields = array(); //поля с 0 значениями, числовые поля
	protected static $ffields = array(); //файловые поля
	
	//поля с выпадающим списком
	protected static $lfields = array();
	
	protected $id;
	protected $cur_props = array(); //данные из БД
	protected $new_props = array(); //любые новые данные

	/**
	 * вернет существующий объект
	 * @param $id
	 * @return bool|Model
	 */
	public static function get($id) {
		if (!is_numeric($id)) {
			return false;
		}

		$obj = new static($id);
		
		if (!$obj->isNew()) {
			return $obj;
		}
		
		return false;
	}

	public function __construct($id = false) {
		if (!empty($id) && is_numeric($id)) {
			$this->id = $id;
			$this->loadData();
		} else {
			foreach (static::$zfields as $field) {
				$this->{$field} = 0;
			}
		}
		
	}
	
	/**
	 * Вернет список значений справочника
	 * @param $sname
	 * @return array
	 */
	public function getList($sname) {
		if (isset(static::$lfields[$sname])) {
			return static::$lfields[$sname];
		}
		
		return array();
	}
	
	/**
	 * Вернет конкретное значение у поля справочника
	 * @param $field
	 * @return null|mixed
	 */
	public function listVal($field) {
		$arr = $this->getList($field);
		
		if (array_key_exists($this->{$field}, $arr)) {
			return $arr[$this->{$field}];
		}
		
		return NULL;
	}
	
	public static function table() {
		$table = trim(static::$table);
		
		if (empty($table)) {
			$table = trim(get_called_class());
			$table = mb_strtolower($table, 'UTF-8');
			$table = '<<'.$table.'s>>';
		}
		
		return $table;
	}
	
	public function fill($arr) {
		if (is_array($arr)) {
			foreach ($arr as $key => $val) {
				$this->{$key} = $val;
			}
			
			foreach (static::$ffields as $field) {
				$value = $arr[$field];
				$cur_file = $this->{$field};

				if (isset($_FILES['file_'.$field])) {
					$dirname = '/upload/file/'.date('Y_m');
					if (!file_exists(ROOT_DIR.$dirname)) {
						mkdir(ROOT_DIR.$dirname, 0755, true);
					}
					$tmp = system::copyFile($_FILES['file_'.$field]['tmp_name'], $_FILES['file_'.$field]['name'], $dirname);
					$value = (empty($tmp)) ? $value : $tmp;
				}

				// Если файл был загружен через выбор на сервере, не удаляем его
				if (system::fileName($value) != system::fileName($cur_file) && strpos($cur_file, '/upload/custom/') === false) {
					@unlink(ROOT_DIR.$cur_file); //удаляем прошлый файл
				}

				$this->{$field} = $value;
			}
		}
	}
	
	public function loadData() {
		if (!empty($this->id)) {
			$sel = new fastSelect(get_called_class().' loadData');
			$sel->select('*')
				->from(static::table())
				->where('id = :id', array(':id' => $this->id))
				->limit(1);
			
			if ($data = $sel->queryRow()) {
				foreach ($data as $key => $val) {
					$this->cur_props[$key] = $val;
				}
			} else {
				$this->id = NULL;
				$this->newError(801, 'Объект с указанным ID не существует');
			}
		}
	}

	/**
	 * Проверяет является ли объект новым или есть данные в таблице
	 * @return bool
	 */
	public function isNew() {
		if (!empty($this->id)) {
			return false;
		}
		
		return true;
	}
	
	public function save() {
		
		foreach (static::$rfields as $name) {
			if (empty($this->{$name})) {
				$this->newError(704, 'Не задано обязательное поле "'.$name.'"');
				return false;
			}
		}
		
		if ($this->issetErrors()) {
			return false;
		}
		
		$tmp = array();
		
		foreach (static::$zfields as $field) {
			if (empty($this->{$field})) {
				$this->{$field} = 0;
			}
		}
		
		foreach (static::$fields as $field) {
			$tmp[] = '`'.$field.'` = "'.addslashes($this->{$field}).'"';
		}
		
		if (!$this->isNew()) {  //update
			$sql = 'UPDATE '.static::table().' SET '.implode(', ', $tmp).' WHERE id = "'.$this->id.'"';
			db::q($sql);
		} else {  //new
			$sql = 'INSERT INTO '.static::table().' SET '.implode(', ', $tmp); 
			$id = db::q($sql);
			if (is_numeric($id)) {
				$this->id = $id;
			} else {
				$this->newError(705, 'Объект не удалось сохранить');
				return false;
			}
		}
		
		return true;
	}
	
	
	public function delete() {
		if (!$this->isNew()) {
			$sql = 'DELETE FROM '.static::table().' WHERE id = '.$this->id;
			db::q($sql);
		}

		foreach (static::$ffields as $field) {
			$value = $this->{$field};

			if (!empty($value)) {
				// Если файл был загружен через выбор на сервере, не удаляем его
				if (strpos($value, '/upload/custom/') === false) {
					@unlink(ROOT_DIR.$value); //удаляем прошлый файл
					$this->deleteCacheImages($value);
				}
			}
		}
	}
	
	
	// Удаляем кешированые миниатюры изображений
	private function deleteCacheImages($del_file, $from_path = '/cache/img/') {
		
		if (is_dir(ROOT_DIR.$from_path) && !empty($del_file)) {
			
			$filename = system::filePathToPrefix($del_file).system::fileName($del_file);
			
			$full = ROOT_DIR.$from_path.'*/'.$filename;
			$arr = glob($full);
			$arr = ($arr === false) ? array() : $arr;
			
			$full = ROOT_DIR.$from_path.'/'.$filename;
			$arr2 = glob($full);
			$arr2 = ($arr2 === false) ? array() : $arr2;
			
			$arr = array_merge($arr, $arr2);
			
			foreach ($arr as $file) {
				@unlink($file);
			}
		}
		
	}
	
	
	public function __isset($name) {
		if ($name == 'id') {
			return true;
		}
		
		if (isset($this->new_props[$name]) || isset($this->cur_props[$name])) {
			return true;
		}
		
		return false;
	}
	
	public function __get($name) {
		
		if ($name == 'id') {
			return $this->id;
		} else if (isset($this->new_props[$name])) {
			return $this->new_props[$name];
		} else if (isset($this->cur_props[$name])) {
			return $this->cur_props[$name];
		}
		
		return NULL;
	}
	
	public function __set($name, $value) {
		if (in_array($name, static::$fields)) {
			$this->new_props[$name] = $value;
		}
	}
	
}

?>