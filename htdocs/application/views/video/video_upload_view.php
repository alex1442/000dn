<?php
$this->load->view('/index_page/index_head_view');
$this->load->view('infobox_view');
echo '<form action="/video/upload" method="post" accept-charset="utf8" enctype="multipart/form-data">
<table>
<tr><td>выберете файл для загрузки</td><td><input type="file" name="upload_video" value=""/></td></tr>
<tr><td>Введите заголовок видео</td><td><input name="video_title" value=""/></td></tr>
<tr><td>Коментарий</td><td><textarea name="description" title="Введите описание к видео"></textarea></td></tr>
<tr><td>Не отображать в поиске</td><td><input type="checkbox" name="hidden"></td></tr>
<tr><td>Теги</td><td><textarea name="tags" title="Вводите теги через пробел"></textarea></td></tr>
</table><input type="submit">
</form>';
$this->load->view('/index_page/index_footer_view');
?>
