<!DOCTYPE html>
<html>
<head>
	<title>%title%</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="%description%"/>
	<meta name="keywords" content="%keywords%"/>
		
	<link rel="stylesheet" type="text/css" href="%core.unCache(/css_js/style.css)%"/>
	<link rel="stylesheet" type="text/css" href="%core.unCache(/css_js/_minitext.css)%"/>
	<link rel="stylesheet" type="text/css" href="%core.unCache(/css_mpanel/prettyPhoto/css/prettyPhoto.css)%" media="screen" charset="utf-8" />
		
	<script type="text/javascript" src="%core.unCache(/css_js/jquery.js)%"></script>
	<script type="text/javascript" src="%core.unCache(/css_js/jquery.raty.min.js)%"></script>
	<script type="text/javascript" src="%core.unCache(/css_js/users/auth.js)%"></script>
	<script type="text/javascript" src="%core.unCache(/css_js/function.js)%"></script>
	<script type="text/javascript" src="%core.unCache(/css_js/_voting.js)%"></script>
	<script type="text/javascript" src="%core.unCache(/css_js/_subscribe.js)%"></script>
	<script type="text/javascript" src="%core.unCache(/css_js/_minitext.js)%"></script>
	<script type="text/javascript" src="%core.unCache(/css_mpanel/prettyPhoto/jquery.prettyPhoto.js)%" charset="utf-8"></script>
</head>
<body>
	<div id="megawrapper">
	<div class="wrapper">
		<div class="container">
			<div id="header">
				<a class="logo" href="/" title="%title%"><img src="/images/tpl/logo.png" alt="%title%"/></a>
				<p>%text_1183%</p>
				<div id="addresswrap">
				  <b class="newsb1">&nbsp;</b>
				  <b class="newsb2">&nbsp;</b>
					<div id="address">
						Москва, <a href="/adresa-i-kontakty" title="">ул. Петроградская</a><br/>
						т.: (495) 2-373-968
					</div>
				   <b class="newsb2">&nbsp;</b>
				   <b class="newsb1">&nbsp;</b>
				 </div>
				%users.authForm()%
			</div>
		</div>
	</div>
	%structure.menu()%


