<?php

$TEMPLATE['subject'] = <<<END
Подтверждение регистрации
END;

$TEMPLATE['frame'] = <<<END
Здравствуйте, %name%. <br /><br />

Вы зарегистрировались на сайте <a href="http://%site_url%">%site_name%</a>.  Для подтверждения регистрации вам необходимо перейти по ссылке <br />
<a href="%url%">%url%</a> <br /><br />

<b>Внимание! Указанная ссылка действительна только в течение текущих суток!</b> <br /><br />

После подтверждения регистрации вы сможете авторизоваться, используя: <br />
Логин: %login%  <br />
И пароль, который указали при регистрации. <br /><br />

С уважением,  <br />
администрация сайта <a href="http://%site_url%">%site_name%</a>
END;

?>