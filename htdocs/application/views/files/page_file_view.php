<?php $this->load->view('index_page/index_head_view');$this->load->view('infobox_view');?><table align="center" border="1" style="border-width: thin;" width="95%" ><tr><td width="100%"><table border="1"><tr><td>Имя файла</td><td><?=$filename?></td></tr><tr><td>Пользователь загрузивший файл</td><td><?php echo "<a href=\"/user/$id_user\">$user</a>"?></td></tr><tr><td>размер файла</td><td><?php$this->load->helper('number');echo byte_format($filesize);?></td></tr><tr><td>Описание к файлу</td><td><?=$description?></td></tr><tr><td>Количество загузок</td><td><?=$counter?></td></tr></table><?phpif (preg_match("/^[a-z0-9]{40}$/", $pass)){echo "Файл защищен паролем. Введите пароль <form method=\"post\" action=\"/files/download/$id_file\"><input type=\"text\" name=\"pass\"><br><input type=\"submit\" value=\"Скачать\"></form>";}else{echo "<form method=\"post\" action=\"/files/download/$id_file\"><input type=\"submit\" value=\"Скачать\"></form>";}if($id_user==$this->session->userdata('id_user')){echo "<a href=\"/files/$id/edit\">Изменить информацию о файле</a><br><a href=\"/files/$id/delete\">Удалить файл</a>";}?></td></tr></table><?php $this->load->view('index_page/index_footer_view');?>