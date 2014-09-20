<?php

class __system {

	public function view() {

		if (user::issetRight('delete'))
			ui::newButton(lang::get('BTN_CLEAR_JORNAL'), 'javascript:clearJornal();');
		
        ui::addLeftButton('Системный журнал', 'system_view');
        ui::addLeftButton('Журнал БД', 'db_view');

        function removeQuotes($val, $obj){
			$val = trim($val);
        	return substr($val, 1, strlen($val) - 2);
        }

        function rqDateTime($val, $obj){
        	return date('d.m.Y H:i:s', $val);
        }

        function sortByTime($a, $b){
            if ($a[1] == $b[1])
                return 0;
            return ($a[1] > $b[1]) ? -1 : 1;
        }


        $mas = array();
	    $system_file = ROOT_DIR.'/logs/revue.log';

	    if (file_exists($system_file)) {

            // Читаем файл, формируем массив
            $tmp_mas = array();
            $file = file($system_file);
	    	while(list($key, $val) = each($file)){
                $tmp = explode(Chr(9), $val);
                if (!empty($tmp[1])) {
                    $tmp[0] = strtotime(removeQuotes($tmp[0], $tmp[0]));    
                    $tmp_mas[] = $tmp;
                }
            }

            // Сортиуем массив по времени
            usort($tmp_mas, 'sortByTime');

            // Выбераем часть массива в соотвествии с постраничной навигацией
            $count = count($tmp_mas);
            $max_count = uiTable::getMaxCount();
            if(uiTable::getCurPage() != 1) {
                $niz = (empty($start_pos)) ? uiTable::getCurPage() * $max_count - $max_count : 0;
                $mas = array_slice($tmp_mas, $niz, $max_count);
            } else $mas = array_slice($tmp_mas, 0, $max_count);
            
	    } else $count = 0;

	    $table = new uiTable($mas, $count);
		$table->emptyText('В системном журнале нет записей!');
		$table->addColumn('1', 'Важность', 0, false, false, 'removeQuotes');
		$table->addColumn('3', 'Пользователь', 0, false, false, 'removeQuotes');
		$table->addColumn('4', 'Действие', 400);
		$table->addColumn('2', 'IP', 0, false, false, 'removeQuotes');
		$table->addColumn('0', 'Дата / Время', 0, false, false, 'rqDateTime');
		
		
		
		
		
		

        return $table->getHTML();
 	}

}

?>