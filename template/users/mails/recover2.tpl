<?php

$TEMPLATE['subject'] = <<<END
Успешное восстановление пароля
END;

$TEMPLATE['frame'] = <<<END
Здравствуйте, %name%. <br /><br />

Вы запрашивали восстановление пароля. <br /><br />

Для авторизации необходимо указать:  <br />
Логин: %login%  <br />
Пароль: %passw%  <br /><br />

С уважением, <br />
администрация сайта <a href="http://%site_url%">%site_name%</a>
END;

?>