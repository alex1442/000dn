<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="/css/style.css">
<link rel="stylesheet" type="text/css" href="/css/selector.css" />
<link rel="stylesheet" type="text/css" href="/css/ex.css" />
<script type="text/javascript" src="/js/cusel.js"></script>
<script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="/js/jScrollPane.js"></script>
<script type="text/javascript"></script>
<script type="text/javascript">


jQuery(document).ready(function(){
var params = {
		changedEl: ".lineForm select",
		visRows: 5,
		scrollArrows: true
	}
	cuSel(params);
	var params = {
		changedEl: "#city",
		scrollArrows: false
	}
	cuSel(params);

});

</script>
<script type="text/javascript" src="//vk.com/js/api/openapi.js?75"></script>
<script type="text/javascript" src="http://vkontakte.ru/js/api/share.js?9" charset="windows-1251"></script>
<script type="text/javascript">
  VK.init({apiId: 3334691});
</script>
<script type="text/javascript">
<!--
document.write(VK.Share.button({
  url: 'http://yatsyk.ru/',
  title:'kj',
  description: 'k',
  image: 'http://yatsyk.ru/upload/blogs/995976adc42cb281d99cfe5cc1de53d9.jpg',
  noparse: true
}));
-->
</script>
<title>Untitled Document</title>
</head>

<body>

<div onclick="VK.Auth.login()">ВАйти вкантакте</div>
<div onclick="VK.Auth.login()"><a href="https://oauth.vk.com/authorize?client_id=3334691&redirect_uri=http://000.dn/index_page/vk_auth/&display=popup">Расширенно вкантакте</a></div>
<script type="text/javascript">
VK.Api.call('users.get',{uids: 56442331,fields:"sex,bdate,country,city"},function(r) {
  if(r.response) {
    console.log(r.response[0]);
VK.API.call('getCities ',{3334691,});
  }
});
</script>
<div class="header">
	<div id="header_conteiner">
	<div id="logo"><img src="/images/logo.png" vspace="29" /></div>
	<div id="login_conteiner">

	<?php
	$this->load->view('/index_page/login_box_view');
	?>
    </div>
  </div>
</div>
<div id="central">
		<div class="div_menu">
			<span id="links">
			<a class="menu_links" href="/" style="margin-left:33px;">Главная</a>
        	<a class="menu_links" href="/files" style="margin-left:13px;">Файлы</a>
        	<a class="menu_links" href="" style="margin-left:22px;">Музыка</a>
        	<a class="menu_links" href="/video" style="margin-left:23px;">Видео</a>
			</span>
			<form action="/search" method="get" name="uu">
            <div id="search_form">
        	<input class="seacrh_type" type="text" name="q" value="<?php if(isset($_GET['q'])){echo htmlspecialchars($_GET['q']);}?>">
            </div>
            <div id="search_sort">
			<div class="main">
				<div class="lineForm">
				<select class="sel80" id="country" name="type_search" tabindex="2">
				<option value="files" <?php if(preg_match("/^\/files/",$_SERVER['REQUEST_URI'])){echo "selected";}?>>Файлы</option>
				<option value="audio" <?php if(preg_match("/^\/audio/",$_SERVER['REQUEST_URI'])){echo "selected";}?>>Музыка</option>
				<option value="mail" <?php if(preg_match("/^\/mail/",$_SERVER['REQUEST_URI'])){echo "selected";}?>>Почта</option>
				<option value="video" <?php if(preg_match("/^\/video/",$_SERVER['REQUEST_URI'])){echo "selected";}?>>Видео</option>
				</select>
				</div>
			</div>
                <div id="search_button">
				<input vspace="6" type="image" src="/images/search_button.png">
  				</div>
			</div>
			</form>

</div>
<div id="content">