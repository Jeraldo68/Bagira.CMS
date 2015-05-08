<?php

/*
    Быстрый Select
*/

class fastSelect {
	
	private $comment = '';
	private $select = array();
	private $from = array();
	private $where = array();
	private $orderBy = array();
	private $groupBy = '';
	private $limit = '';
	private $alias = '';
	
	public $time = 0; //время выполнения последнего запроса
	
	public function __construct($comment = '') {
		$this->setComment($comment);
	}
	
	public function setComment($str) {
		$str = trim($str);
		if (!empty($str)) {
			$this->comment = '/* '.trim($str).' */';
		}
		return $this;
	}
	
	public function select($data) {
		if (is_array($data)) {
			array_merge($this->select, $data);
		} else {
			$this->select[] = $data;
		}
		
		return $this;
	}
	
	public function clearSelect() {
		$this->select = array();
		return $this;
	}
	
	public function from($data) {
		if (is_array($data)) {
			array_merge($this->from, $data);
		} else {
			$this->from[] = $data;
		}

		return $this;
	}
	
	public function where($condition, $params = array()) {		
		if (!empty($params)) {
			
			foreach ($params as $key => $val) {
				//	если нужно без кавычек используем двойное двоеточие
				if (strpos($key, '::') !== false) {
					$condition = str_replace($key, $val, $condition);
				} else {
					$condition = str_replace($key, '"'.$val.'"', $condition);
				}
				
			}
		}
		
		$this->where[] = $condition;

		return $this;
	}
	
	public function groupBy($data) {
		$this->groupBy = 'GROUP BY '.$data;

		return $this;
	}

	public function clearGroupBy() {
		$this->groupBy = '';
		return $this;
	}

	public function orderBy($data) {
		if (is_array($data)) {
			array_merge($this->orderBy, $data);
		} else {
			$this->orderBy[] = $data;
		}

		return $this;
	}
	
	public function limit($limit, $offset = 0) {
		$this->limit = 'LIMIT '.$offset.', '.$limit;
		
		return $this;
	}

	public function alias($data) {
		$this->alias = $data;
		return $this;
	}

	/**
	 * генерирует строку запрос
	 * @return bool|string
	 */
	private function createQuery() {
		$this->time = 0;
		
		if (empty($this->from)) {
			return false;
		}
		
		$ret = $this->createComment().$this->createSelect().' '.$this->createFrom().' '.$this->createWhere().' '.$this->createGroupBy().' '.$this->createOrderBy().' '.$this->createLimit();
		
		if (!empty($this->alias)) {
			$ret = '('.$ret.') '.$this->alias;
		}
		
		return $ret;
	}
	
	private function createComment() {
		if (!empty($this->comment)) {
			return $this->comment.' ';
		}

		return '';
	}
	
	private function createSelect() {
		if (empty($this->select)) {
			return 'SELECT *';
		}

		return 'SELECT '.implode(', ', $this->select);
	}

	private function createFrom() {
		
		return 'FROM '.implode(', ', $this->from);
	}
	
	private function createGroupBy() {
		if (empty($this->groupBy)) {
			return '';
		}

		return $this->groupBy;
	}

	private function createWhere() {
		if (empty($this->where)) {
			return '';
		}
		
		$temp = array();
		foreach ($this->where as $item) {
			$temp[] = '('.$item.')';
		}
		
		return 'WHERE '.implode(' AND ', $temp);
	}

	private function createOrderBy() {
		if (empty($this->orderBy)) {
			return '';
		}

		return 'ORDER BY '.implode(', ', $this->orderBy);
	}

	private function createLimit() {
		if (empty($this->limit)) {
			return '';
		}

		return $this->limit;
	}
	
	//Возвращает сгенерированный запрос
	public function query() {
		return $this->createQuery();
	}

	/**
	 * @param bool $debug
	 * @return bool|array
	 */
	public function queryAll($debug = false) {
		$query = $this->createQuery();
		
		if ($query === false) {
			return false;
		}
		
		$time = microtime(true);
		$ret = db::q($query, records, $debug);
		$this->time = round(microtime(true) - $time, 4);
		
		return $ret;
	}

	/**
	 * @param bool $debug
	 * @return bool|array
	 */
	public function queryRow($debug = false) {
		$query = $this->createQuery();

		if ($query === false) {
			return false;
		}

		$time = microtime(true);
		$ret = db::q($query, records, $debug);
		$this->time = round(microtime(true) - $time, 4);
		
		if (empty($ret) || !is_array($ret)) {
			return false;
		}
		
		return $ret[0];
	}

	/**
	 * @param bool $debug
	 * @return bool|string
	 */
	public function queryVal($debug = false) {
		$query = $this->createQuery();

		if ($query === false) {
			return false;
		}
		
		$time = microtime(true);
		$ret = db::q($query, value, $debug);
		$this->time = round(microtime(true) - $time, 4);
		
		return $ret; 
	}
}
?>