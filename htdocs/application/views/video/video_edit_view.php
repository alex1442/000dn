<?php
$this->load->view('/index_page/index_head_view');
$this->load->view('infobox_view');
echo "<form action=\"/video/edit/$id\" method=\"post\" accept-charset=\"utf8\" enctype=\"multipart/form-data\">
<table>
<tr><td>Введите заголовок видео</td><td><input name=\"video_title\" value=\"$video_title\"/></td></tr>
<tr><td>Коментарий</td><td><textarea name=\"description\" title=\"Введите описание к видео\">$description</textarea></td></tr>
<tr><td>Не отображать в поиске</td><td><input type=\"hidden\" name=\"hidden\" value=\"0\"><input type=\"checkbox\" name=\"hidden\" $hidden></td></tr>
<tr><td>Теги</td><td><input type=\"hidden\" name=\"tags\" value=\"\"><textarea name=\"tags\" title=\"Вводите теги через пробел\">". $tags."</textarea></td></tr>
</table><input type=\"submit\">
</form>";
$this->load->view('/index_page/index_footer_view');
?>
