<?php

$TEMPLATE['frame'] = <<<END

<div id="alert_msg" style="display:none;">%alert_msg%</div>
<div id="alert_field" style="display:none;">%alert_field%</div>

<div class="registration">
	<form id="editUserForm" action="/users/edit_proc" method="post" enctype="multipart/form-data">


						<div class="marker">
							<label for="name">Ваш логин / E-mail</label>
							<big>%obj.email%</big>
							<div class="clear"></div>
						</div>

		<br/>

						<div class="marker">
							<label for="name">Имя</label>
							<input class="input" type="text" id="name" name="name" value="%obj.name%"/>
							<div class="image"></div>
							<div class="clear"></div>
						</div>

						<div class="marker">
							<label for="surname">Фамилия</label>
							<input class="input" type="text" id="surname" name="surname" value="%obj.surname%"/>
							<div class="image"></div>
							<div class="clear"></div>
						</div>


						<div class="photo_wrap">
							<label for="avatara">Ваше фото</label>
							<input type="hidden" id="avatara2" name="avatara" value="%photo%" />
							<input type="file" id="avatara" name="file_avatara" onChange="$('#avatara2').val(this.value);"/>
						</div>

						%photo_block%
		<br>
		
		<div class="clear"></div>
		
		%users.subscribe()%

		<div class="clear"></div>
		
		<br/>

		<button style="float:left;">Сохранить</button>

		<a href="/users/change-password" class="right_link">Изменить пароль</a><br />
		
		<input name="back_url" type="hidden" value="%current_url_pn%" />
		<input name="check_subscribe" type="hidden" value="1">

	</form>

</div>

<script type="text/javascript" src="/css_js/users/edit.js"></script>

END;

$TEMPLATE['photo_block'] = <<<END
<div class="clear"></div>
<div class="marker" id="photo_preview">
    <img src="%photo%" style="margin-left:150px;float:left;" />

    <a href="/users/edit_proc/del-photo" style="margin-left:20px;padding-top:15px;float:left;">Удалить фотографию</a>
    <div class="clear"></div>
</div>
END;

?>