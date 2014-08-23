<?php

class coreMacros {

    /**
	* @return stirng - Путь до изображения
	* @param string $file_name - Исходное изображение
	* @param CONST $scale_type - Способ масштабирования рисунка, одна из трех констант
				stRateably	-	Масштабирование с учетом пропорций, относительно $width или $height
				stSquare    - 	Обрезать по квадрату со стороной $width
				stInSquare  - 	Вписать в квадрат со стороной $width
	* @param int $width - Ширина конечного изображения, если == 0 не учитывается
	* @param int $height - Высота конечного изображения, если == 0 не учитывается
	* @param string $watermark - Способ наложения водяного знака. Одно из нескольких значений:
				0 		- 	Водяной знак не накладывается
				1-9 	-	Водяной знак накладывается в одну из 9 позиций квадрата (см. документацию)
	* @desc МАКРОС: При необходимости масштабирует изображение под заданные параметры и
					возвращает путь до кешированного файла.
	*/
 	public function resize($file_name, $scale_type, $width = 0, $height = 0, $watermark = 0) {

            if (!empty($file_name)) {

                if (system::checkVar($file_name, isAbsUrl))
                    return $file_name;

	            $scale = (!is_numeric($scale_type)) ? constant($scale_type) : $scale_type;

				$dir = '/cache/img/'.$scale_type.'_'.$width.'x'.$height.'_'.$watermark;

	            $new_file = $dir.'/'.system::filePathToPrefix($file_name).system::fileName($file_name);
				
				if (!file_exists(ROOT_DIR.$new_file)) {

	                if (!is_dir(ROOT_DIR.$dir)) @mkdir(ROOT_DIR.$dir, 0777, true);

					$img = new resizer($file_name, $scale, $width, $height);

					if (is_numeric($watermark) && $watermark > 0)
						$img->setWatermark(reg::getKey('/core/watermark'), $watermark);

					if (!$img->save(ROOT_DIR.$new_file)) {
						return $file_name;
					}
	            }

				if (file_exists(ROOT_DIR.$new_file))
					return $new_file;
            }
 	}

	
	/**
	 * @return string - Путь до заресайзенного изображения
	 * @param string $file_name - Исходное изображение
	 * @param int $width - Ширина конечного изображения, если == 0 не учитывается
	 * @param int $height - Высота конечного изображения, если == 0 не учитывается
	 * @param $scale_type - Способ масштабирования рисунка
	   0	-	Изображение заполняет всю область, лишнее обрезается равномерно со всех сторон
	   1    - 	Изображение вписывается в заданные рамки, но не обрезается
	 * @param int $bg - Задает фон (HEX), если 0 оставляет прозрачным
	 * @param string $watermark - Способ наложения водяного знака. Одно из нескольких значений:
	   0 		- 	Водяной знак не накладывается
	   1-5 	-	Водяной знак накладывается в одну из 5 позиций квадрата (см. документацию)
	 * @desc МАКРОС: При необходимости масштабирует изображение под заданные параметры и
	   возвращает путь до кешированного файла.
	 */
	public function resizeImage($file_name, $width = 0, $height = 0, $scale_type = 0, $bg = 0, $watermark = 0) {

		if (empty($file_name))
			return '';

		if (system::checkVar($file_name, isAbsUrl))
			return $file_name;

		$scale_type = ($scale_type != 1) ? 0 : 1; //может быть только 0 или 1

		$width = intval($width);
		$height = intval($height);
		
		$dir = '/cache/img/'.$width.'x'.$height.'_'.$scale_type.'_'.$watermark.'_'.str_replace('#', '', $bg);
		$resize_file_name = $dir.'/'.system::filePathToPrefix($file_name).system::fileName($file_name);

		if (!file_exists(ROOT_DIR.$resize_file_name)) {
			if (!is_dir(ROOT_DIR.$dir)) @mkdir(ROOT_DIR.$dir, 0777, true);
			
			$img = AcImage::createImage(ROOT_DIR.$file_name);

			if (!empty($width) && !empty($height)) {

				if ( ($img->getWidth() <= $width) && ($img->getHeight() < $height)) {
					return $file_name;
				}
				
			} else if (!empty($width)) {
				
				if ($img->getWidth() <= $width) {
					return $file_name;
				}
				
			} else if (!empty($height)) {
				
				if ($img->getHeight() <= $height) {
					return $file_name;
				}
				
			}
			
			if ($bg !== 0) {
				$bg = system::hex2rgb($bg);
				$img->setBackgroundColor($bg['red'], $bg['green'], $bg['blue']);
				$img->setTransparency(false);
			} else {
				$img->setTransparency(true);
			}
			
			$img->setQuality(75);
				
			if (!empty($width) && !empty($height)) {

				if ($scale_type == 0) {
					$img->cropCenter($width.'pr', $height.'pr');
				}
				
				$img->resize($width, $height);
			} else if (!empty($width)) {
				$img->resizeByWidth($width);
			} else if (!empty($height)) {
				$img->resizeByHeight($height);
			}

			$wimage = reg::getKey('/core/watermark');
			if (is_numeric($watermark) && $watermark > 0 && !empty($wimage)) {
				if (file_exists(ROOT_DIR.$wimage)) {

					$watermark = ($watermark > 5) ? 5 : $watermark;
					
					$img->setTransparency(true);
					$img->drawLogo(ROOT_DIR.$wimage, $watermark - 1);
					if ($bg !== 0) {
						$img->setTransparency(false);
					}
				}
			}
			

			$img->save(ROOT_DIR.$resize_file_name);
		}

		return $resize_file_name;
	}


	/**
	 * @return stirng - путь до файла с get строкой (последнего времени редактирования файла)
	 * @param string $file_path - Путь до файла (/css_js/style.css)
	 * @desc МАКРОС: Добавляет суффикс к файлу, для того что бы он не кешировался браузером,
	 * суффикс определяется по последней дате редактирования файла
	 */
	public function unCache($file_path) {
		$full_path = ROOT_DIR.$file_path;

		$suffix = is_file($full_path) ? filemtime($full_path) : 'not_found';

		return $file_path.'?'.$suffix;
	}

	/**
	 * @return stirng - человекопонятный размер
	 * @param int $bytes - размер в байтах
	 * @desc МАКРОС: Приводит размер в байтах к понятной для человека форме (10 Gb)
	 */
	public function niceSize($bytes) {
		$type = array("", "K", "M", "G", "T", "P", "E", "Z", "Y");

		$i = 0;
		while($bytes >= 1024) {
			$bytes /= 1024;
			$i++;
		}

		return round($bytes, 1)." ".$type[$i]."b";
	}
	
	/**
	 * @return HTML
	 * @param string $field_name - Системное имя поля
	 * @param int $obj_id - ID объекта
	 * @param string $templ_block - Имя используемого блока в шаблоне оформления
	 * @param string $templ_name - Имя файла шаблона оформления
	 * @desc МАКРОС: Выводит значение поля в указанном оформлении
	 */
	function getProp($field_name, $obj_id, $templ_block = 0, $templ_name = 'default')
	{
		$templ_file = '/structure/getProp/' . $templ_name . '.tpl';
		$TEMPLATE = page::getTemplate($templ_file);

		if(!is_array($TEMPLATE))
			return page::errorNotFound(__CLASS__.'.'.__FUNCTION__, $templ_file);

		if(empty($templ_block) && isset($TEMPLATE[$field_name]))
			$templ_block = $field_name;
		else if(!isset($TEMPLATE[$templ_block]))
			$templ_block = 'default';

		if(isset($TEMPLATE[$templ_block]) && $obj = ormObjects::get($obj_id))
		{

			$value2 = '';
			$value = $obj->__get($field_name);
			$field = $obj->getClass()->getField($field_name);

			if($obj->getClass()->issetField($field_name))
			{

				if($field->getType() < 91 && $field->getType() != 73)
				{

					if($field->getType() == 90)
						// Тип выпадающий список
						$value2 = $obj->__get('_' . $field_name);
					else if($field->getType() == 75 && !file_exists(ROOT_DIR . $value))
						// Тип изображение
						$value = '';

					page::assign('obj.id', $obj->id);
					page::assign('obj.name', $obj->name);
					page::assign('title', $obj->getClass()->getFieldName($field_name));

					if(!empty($value))
					{

						page::assign('value', $value);
						page::assign('value_name', $value2);
						page::assign('obj.' . $field_name, $value);
						page::assign('obj._' . $field_name, $value2);

						return page::parse($TEMPLATE[$templ_block]);

					} else if(isset($TEMPLATE[$templ_block . '_empty']))
						return page::parse($TEMPLATE[$templ_block . '_empty']);

				} else
					return page::error(__CLASS__.'.'.__FUNCTION__, $field_name, lang::get('ERROR_BAD_TYPE'));

			} else
				return page::error(__CLASS__.'.'.__FUNCTION__, $field_name, lang::get('ERROR_NOTFOUND_FIELD'));

		}
	}
	
	
	/**
	 * @return HTML
	 * @param int $count - количество
	 * @param string $one - склонение слова при количестве 1
	 * @param string $two - склонение слова при количестве 2
	 * @param string $five - склонение слова при количестве 5
	 * @desc МАКРОС: Вернет слово в правильном склонении взависимости от количества
	 */
	public function decl($count = 0, $one, $two, $five) {
		$mas = array(1 => $one, 2 => $two, 5 => $five);
		return $mas[ruNumbers::getDeclNum($count)];
	}

	/**
	 * @return stirng - отформатированное число.
	 * @param float $number - исходное число
	 * @param int $dec - количество знаков после запятой
	 * @param string $point - вид разделителя
	 * @param string $sep - разделитель только для тысячного разряда
	 * @desc МАКРОС: Форматирует число по разрядности, аналог PHP-функции number_format()
	 */
	function capacity($number, $dec = 0, $point = ',', $sep = ' ') {
		$number = number_format($number, $dec, $point, $sep);
		return str_replace(" ", "&nbsp;", $number);
	}

    /**
	* @return stirng - дата и время в указанном формате.
	* @param string $format - Формат вывода, по аналогии с PHP-функцией date()
	* @param string $time - Дата и Время в формате TIMESTAMP или текстовом. Если 0, используется текущее время.
	* @desc МАКРОС: Выводит указанную дату и время в заданном формате, аналог PHP-функции date()
	*/
 	function fdate($format = 'd.m.Y', $time = 0) {
		$time = (is_string($time) && !is_numeric($time)) ? strtotime($time) : time();
	  	return date($format, $time);
	}

    /**
	* @return stirng - Название месяца.
	* @param string $time - Дата и Время в формате TIMESTAMP или текстовом. Если 0, используется текущее время.
    * @param Int $type - Формат вывода названия месяца от 1 до 3
	* @desc МАКРОС: Вернет название месяца на русском языке
	*/
	function rus_month($time = 0, $type = 3) {

		$time = (is_string($time) && !is_numeric($time)) ? strtotime($time) : time();

        $months = lang::get('MONTH', $type);

	  	return $months[date("m", $time)];
	}

    /**
	* @return stirng - Название дня недели.
	* @param string $time - Дата и Время в формате TIMESTAMP или текстовом. Если 0, используется текущее время.
    * @param Int $type - Формат вывода названия дня недели от 1 до 3
	* @desc МАКРОС: Вернет название дня недели на русском языке
	*/
    function rus_weekday($time = 0, $type = 1) {

		$time = (is_string($time) && !is_numeric($time)) ? strtotime($time) : time();

        $months = lang::get('DAY', $type);

	  	return $months[date("N", $time)];
	}

    /**
	* @return string
	* @param string $file_name - Путь к файлу
	* @desc МАКРОС: Вернет расширение указанного файла
	*/
	function fileExt($file_name) {
	  	return system::fileExt($file_name);
	}

    /**
	* @return string
	* @param string $file_name - Путь к файлу
	* @desc МАКРОС: Вернет размер в килобайтах для указанного файла
	*/
    function fileSize($file_name) {
	  	if (file_exists(ROOT_DIR.$file_name))
	  		return ceil(filesize(ROOT_DIR.$file_name) / 10240) / 100;
	 	else
	 		return 0;
	}

    /**
	* @return string
	* @param string $url - URL для обработки
	* @desc МАКРОС: Обрезает указанный урл на один уровень с конца, используется для формирования ссылки назад
	*/
	function preUrl($url) {
	  	return system::preUrl($url);
	}

    /**
	* @return string
	* @param string $num - Номер части урла
	* @desc МАКРОС: Вернет указанную часть текущего урла страницы
	*/
    function url($num) {
	  	return system::url($num);
	}

	/**
	* @param string $url - Ссылка на любой ресурс.
	* @desc МАКРОС: Делает редирект на указанный URL.
	*/
 	public function redirect($url, $absolut = 0) {
		system::redirect($url, $absolut);
 	}


    /**
	* @return stirng - Контент
	* @param string $templ_name - имя шаблона
	* @desc МАКРОС: Возвращает пропарсенный шаблон из папки /template/structure
	*/
	function include_templ($templ_name) {

		$site_prefix = (domains::curId() == 1 && languages::curId() == 1) ? '' : '/__'.str_replace('.', '_', domains::curDomain()->getName()).'_'.languages::curPrefix();
		$file = TEMPL_DIR.$site_prefix.'/structure/'.$templ_name.'.tpl';

		if (!file_exists($file))
		     return str_replace('%name%', $templ_name, 'Указанный шаблон (%name%.tpl) не найден!');
		else {

		     $file_tpl = implode('', file($file));
		     return page::parse($file_tpl);

		}
	}

    /**
	* @return HTML
	* @param int(string) $section - ID объекта, подразделы которой будут выводиться в списке
						 или системное имя класса, объекты которого нужно вывести
	* @param string $templ_name - Шаблон оформления по которому будет строится список
	* @param int $max_count - Максимальное количество элементов в списке
	* @param string $order_by - Способ сортировки элементов списка. SQL-подобный синтаксис, например: "name DESC".
	* @param int $start_pos - Номер элемента по порядку с которого будет выводиться список.
	* @desc МАКРОС: Выводит список объектов.
	*/
 	public function objList($section, $TEMPLATE = 'default', $max_count = 0, $order_by = 0, $start_pos = 0) {

        $list = '';

        // Определяем источник данных: ID, имя класса, путь, объект ormPage
        $independent = ($section instanceof ormObject) ? false : true;
        $class_name = $class_frame = '';

        if ($independent) {

            if (!is_numeric($section)) {
                $pos = strpos($section, ' ');
                if ($pos) {
                    $class_name = substr($section, $pos + 1);
                    $section = substr($section, 0, $pos);
                } else {
                    $class_name = $section;
                    $section = -1;
                }
            }
        }

        // Если нужно, подгружаем файл шаблона
        if (!is_array($TEMPLATE)) {
	        $templ_file = '/core/objects/'.$TEMPLATE.'.tpl';
	        $TEMPLATE = page::getTemplate($templ_file);

		    if (!is_array($TEMPLATE))
				return page::errorNotFound(__CLASS__.'.'.__FUNCTION__, $templ_file);
        }

        // Формируем выборку объектов
	    $sel = new ormSelect($class_name);

	    if ($section >= 0) {
            page::assign('parent_id', $section);
            $sel->where('parents', '=', $section);
	 	}

        // Сортировка списка
        if (!empty($order_by)) {
            $pos = strpos($order_by, ' ');
            if ($pos) {
            	$parram = substr($order_by, $pos + 1);
            	$order_by = substr($order_by, 0, $pos);
            } else $parram = '';
        	$sel->orderBy($order_by, $parram);
        } else $sel->orderBy(position, asc);

        $class_list = $sel->getClassesList();

        if (!empty($class_list)) {

	        // Узнаем какие поля объектов будут участвовать в выборке
	        $fields_str = '';
	        $fields = page::getFields('obj', $TEMPLATE, $class_list, $class_frame);
	        if (isset($fields['obj']))
		        while(list($key, $val) = each($fields['obj']))
		        	if ($val != 'url' && $val != 'class' && $val != 'num')
		        		$fields_str .= (empty($fields_str)) ? $val : ', '.$val;
	        $sel->fields($fields_str);

            // Количество элементов и постраничная навигация
            if (!empty($max_count))
		        if (isset($fields['funct']) && in_array('structure.navigation', $fields['funct'])) {
			        $count_page = ceil($sel->getCount() / $max_count);
			        page::assign('count_page', $count_page);
			        if(system::getCurrentNavNum() != 0) {
			        	$niz = (empty($start_pos)) ? system::getCurrentNavNum() * $max_count - $max_count : $start_pos;
			        	$sel->limit($niz, $max_count);
			       	} else $sel->limit($max_count);
	            } else if (!empty($start_pos)) {
                	$sel->limit($start_pos, $max_count);
	            } else $sel->limit($max_count);

            // Формируем список
	        while($obj = $sel->getObject()) {

	            // Парсим поля страницы
		        if (isset($fields['obj_all'])) {
		            reset($fields['obj_all']);
		        	while(list($num, $name) = each($fields['obj_all']))
		            	page::assign('obj.'.$name, $obj->__get($name));
	            }

	            $class = $obj->getClass()->getSName();
	            $num = $sel->getObjectNum() + 1;

                page::assign('obj.num', $num);
                page::assign('obj.class', $class);
	            page::assign('class-first', ($num == 1) ? 'first' : '');
	            page::assign('class-last', ($num == $sel->getObjectCount()) ? 'last' : '');
	            page::assign('class-odd', ($num % 2 == 0) ? 'odd' : '');
	            page::assign('class-even', ($num % 2 != 0) ? 'even' : '');
                page::assign('class-third', ($num % 3 == 0) ? 'third' : '');

                if ($num === 1)
                	page::assign('first_children_id', $obj->id);
                page::assign('last_children_id', $obj->id);

	            if (isset($TEMPLATE['list_'.$class]))
	            	$templ = 'list_'.$class;
	            else if (isset($TEMPLATE['list']))
            		$templ = 'list';
                else $templ = '';

	            if (isset($TEMPLATE[$templ])) {
					if ($num > 1 && isset($TEMPLATE['separator']))
						$list .= $TEMPLATE['separator'];
				    $list .= page::parse($TEMPLATE[$templ]);
	            }
	    	}
    	}

    	if (!empty($list) && $independent) {
            page::assign('list', $list);
            if (isset($TEMPLATE['frame_list']))
        		$list = page::parse($TEMPLATE['frame_list']);
        	else
        		$list = page::errorBlock('core.objList', $templ_file, 'frame_list');
    	}

    	return $list;
 	}

}

?>