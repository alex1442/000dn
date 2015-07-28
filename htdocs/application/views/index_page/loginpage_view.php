   		<?php $this->load->view('index_page/index_head_view');
   		$this->load->view('infobox_view');?>
<? if (isset($error_msg)) echo $error_msg?>		
		<form action="/index_page/auth" method="post">
    	<div id="login_remember">
        <input id="login_pass_input" type="text" name="login" value="Email или Логин" />
        <input type="checkbox" name="remember"/>Запомнить
       	</div>
        <div id="pass_enter">
       	<input id="login_pass_input" type="text" name="pass" value="Пароль"/>
        <input id="Enter_button" type="submit" name="Enter" value="Вход" />
       	</div>
       	<div id="remember_restore">
        <a href="">Забыли пароль?</a>
        <a href="/index_page/registration">Зарегистрироватся</a>
        </div>
        </form>
		<?php $this->load->view('index_page/index_footer_view');?>