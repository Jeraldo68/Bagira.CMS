<?php

class usersMacros {

    /**
	* @return string
	* @param string $templ_name - Шаблон оформления
    * @param string $services - Список социальных сервисов разделенных "|", в заданном порядке, через которые доступна авторизация
	* @desc МАКРОС: Выводит форму авторизации или ссылку на личный кабинет текущего пользователя
	*/
 	function authForm($templ_name = 'auth', $services = 'facebook|twitter|vk|ok|google|yandex') {

    	$templ_file = '/users/'.$templ_name.'.tpl';
        $TEMPLATE = page::getTemplate($templ_file);

	    if (!is_array($TEMPLATE))
			return page::errorNotFound(__CLASS__.'.'.__FUNCTION__, $templ_file);

	   	if (user::isGuest()) {

            // Формируем список социальных кнопок
            $services = explode('|', $services);
			$list = '';
			foreach($services as $service)
				if (reg::getKey('/users/'.$service.'_bool') && isset($TEMPLATE['social_btn_'.$service]))
				    $list .= page::parse($TEMPLATE['social_btn_'.$service]);

            if (!empty($list)) {
			    page::assign('list', $list);
			    page::fParse('social_buttons', $TEMPLATE['social_buttons']);
            } else
                page::assign('social_buttons');


			return page::parse($TEMPLATE['frame_form']);

	   	} else {

            page::assign('user_id', user::get('id'));
            page::assign('user_name', user::get('name'));
            page::assign('user_surname', user::get('surname'));

            $avatar = user::get('avatara');
            if (!empty($avatar)) {
                page::assign('user_avatara', $avatar);
                page::fParse('avatara_block', $TEMPLATE['avatara']);
            } else {
                page::fParse('avatara_block', $TEMPLATE['avatara_empty']);
            }

            return page::parse($TEMPLATE['frame_account']);

	   	}

	}

	/**
	* @return string
	* @param string $templ_name - Шаблон оформления
	* @desc МАКРОС: Выводит форму напоминания пароля
	*/
 	function recover($templ_name = 'recover') {

    	$templ_file = '/users/'.$templ_name.'.tpl';
        $TEMPLATE = page::getTemplate($templ_file);

	    if (!is_array($TEMPLATE))
			return page::errorNotFound(__CLASS__.'.'.__FUNCTION__, $templ_file);

	   	if (user::isGuest()) {

            page::parseError('recover');

            page::assign('login_or_email', ((isset($_SESSION['SAVING_POST']['login_or_email'])) ? $_SESSION['SAVING_POST']['login_or_email'] : ''));

			return page::parse($TEMPLATE['frame']);

	   	} else return lang::get('USERS_ALREADY_LOGGED');
	}

    /**
	* @return string
	* @param string $templ_name - Шаблон оформления
	* @desc МАКРОС: Выводит форму редактирования акаунта
	*/
	function editForm($templ_name = 'edit') {

        if (!user::isGuest()) {

	     	$templ_file = '/users/'.$templ_name.'.tpl';
	        $TEMPLATE = page::getTemplate($templ_file);

		    if (!is_array($TEMPLATE))
				return page::errorNotFound(__CLASS__.'.'.__FUNCTION__, $templ_file);


	        $user = user::getObject();

            // Парсим все поля
            $fields = $user->getClass()->loadFields();
		    while(list($name, $field) = each($fields))
		    	page::assign('obj.'.$name, $user->__get($name));

            // Выводим аватару пользователя
            if ($user->avatara != '' && isset($TEMPLATE['photo_block'])) {
                page::assign('photo', $user->avatara);
                page::fParse('photo_block', $TEMPLATE['photo_block']);
            } else {
                page::assign('photo', '');
                page::assign('photo_block', '');
            }

            // Сообщение об ошибках
            page::parseError('edit_user');

	        return page::parse($TEMPLATE['frame']);
        }
	}


	/**
	 * @return string
	 * @param string $mailing - id рассылок через пробел (all - все активные расслыки)
	 * @param string $templ_name - Шаблон оформления
	 * @desc МАКРОС: Выводит форму для добавления в рассылку
	 */
	function subscribe($mailing = 'all', $templ_name = 'default') {

		$templ_file = '/users/subscribe/'.$templ_name.'.tpl';
		$TEMPLATE = page::getTemplate($templ_file);

		if (!is_array($TEMPLATE))
			return page::errorNotFound(__CLASS__.'.'.__FUNCTION__, $templ_file);

		$mailing = trim($mailing);

		$arr = array();
		if ($mailing == 'all') {
			$sel = new ormSelect('subscription');
			$sel->where('active', '=', 1);
			$sel->where('lang', '=', languages::curId());
			$sel->where('domain', '=', domains::curId());

			while ($obj = $sel->getObject()) {
				$arr[] = $obj->id;
			}
		} else {
			$arr = explode(' ', $mailing);
		}

		$list = '';

		foreach ($arr as $id) {
			if ($sub = ormObjects::get($id, 'subscription')) {

				$sub_check = false;
				
				if (!user::isGuest()) {
					$user = user::getObject();

					$sel = new ormSelect('subscribe_user');
					$sel->where('name', '=', $user->email);
					$sel->where('parents', '=', $id);
					$sel->limit(1);
					
					if ($sel->getObjectCount() > 0) {
						$sub_check = true;
					}
				}

				page::assign('obj.name', $sub->name);
				page::assign('obj.id', $sub->id);
				
				if ($sub_check) {
					$list .= page::parse($TEMPLATE['list_active']);
				} else {
					$list .= page::parse($TEMPLATE['list']);
				}
			}
		}

		if (!empty($list)) {
			page::assign('list', $list);
			return page::parse($TEMPLATE['frame_list']);
		}

		if (isset($TEMPLATE['empty'])) {
			return page::parse($TEMPLATE['empty']);
		}

		return '';
	}
	
	
    /**
	* @return string
	* @param string $templ_name - Шаблон оформления
	* @desc МАКРОС: Выводит форму изменения пароля
	*/
 	function changePassword($templ_name = 'change_password') {

    	$templ_file = '/users/'.$templ_name.'.tpl';
        $TEMPLATE = page::getTemplate($templ_file);

	    if (!is_array($TEMPLATE))
			return page::errorNotFound(__CLASS__.'.'.__FUNCTION__, $templ_file);

	   	if (!user::isGuest()) {

            if (system::url(2) == 'ok')
                return page::parse($TEMPLATE['frame_ok']);
            else {
                page::parseError('change_password');
                return page::parse($TEMPLATE['frame']);
            }
	   	}
	}


    /**
	* @return string
	* @param string $templ_name - Шаблон оформления
	* @desc МАКРОС: Выводит форму регистрации пользователя
	*/
    function addForm($templ_name = 'add') {

        if (reg::getKey('/users/reg')) {

	     	$templ_file = '/users/'.$templ_name.'.tpl';
	        $TEMPLATE = page::getTemplate($templ_file);

		    if (!is_array($TEMPLATE))
				return page::errorNotFound(__CLASS__.'.'.__FUNCTION__, $templ_file);

            // Парсим все поля
            $fields = ormClasses::get('user')->loadFields();
		    while(list($name, $field) = each($fields))
		    	page::assign('obj.'.$name, '');

            // Вывод сообщения об ошибках
            page::parseError('add_user');

            // Согласие с условиями регистрации
            page::assign('checked', ((isset($_SESSION['SAVING_POST']['confirm'])) ? 'checked' : ''));

            page::assignSavingPost();

	        return page::parse($TEMPLATE['frame']);
        }
	}

    /**
	* @return string
	* @param string $templ_name - Шаблон оформления
	* @desc МАКРОС: Выводит форму второго шага авторизации через соц сети, если требуется указать e-mail или согласиться с правилами.
	*/
    function socialAuthConfirm($templ_name = 'social_auth_confirm') {

        $templ_file = '/users/'.$templ_name.'.tpl';
        $TEMPLATE = page::getTemplate($templ_file);

        if (!is_array($TEMPLATE))
			return page::errorNotFound(__CLASS__.'.'.__FUNCTION__, $templ_file);

        if (user::isGuest() && !empty($_SESSION['SOCIAL_AUTH_USER_INFO'])) {

            page::assign('obj.email', '');
            foreach($_SESSION['SOCIAL_AUTH_USER_INFO'] as $key => $val)
                page::assign('obj.'.$key, $val);

            page::assign('email_block', (reg::getKey('/users/ask_email') && empty($_SESSION['SOCIAL_AUTH_USER_INFO']['email'])) ? page::parse($TEMPLATE['email']) : '');
            page::assign('confirm_block', (reg::getKey('/users/confirm')) ? page::parse($TEMPLATE['confirm']) : '');

            // Вывод сообщения об ошибках
            page::parseError('social_auth_confirm');

            return page::parse($TEMPLATE['frame']);
        }
    }



}

?>