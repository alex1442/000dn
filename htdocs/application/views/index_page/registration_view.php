<?php $this->load->view('index_page/index_head_view');?>
<form method="post" action="/index_page/registration">
<table>
<tr><td>Логин:</td><td><input type="text" name="login" value="<?=$login?>"></td></tr>
<tr><td>Имя: </td><td><input type="text" name="name" value="<?=$name?>"></td></tr>
<tr><td>Фамилия: </td><td><input type="text" name="surname" value="<?=$surname?>"></td></tr>
<tr><td>e-mail: </td><td><input type="text" name="email" value="<?=$email?>"></td></tr>
<tr><td>Пароль: </td><td><input type="password" name="pass"></td></tr>
<tr><td>повторите пароль: </td><td><input type="password" name="pass2"></td></tr>
</table>
<input type="submit"></form>
<font color="red"><?php if (is_array($info_msg)){foreach($info_msg as $msg){echo $msg."<br>";}} ?></font>
<?php $this->load->view('index_page/index_footer_view');?>