<?php $this->load->view('index_page/index_head_view');?>
<table align="center" border="1" style="border-width: thin;" width="95%" ><tr><td width="100%">
<?php

$this->load->helper('number');

echo "<form method=\"get\" action=\"/files/search\">Поиск файлов <input type=\"text\" name=\"q\" value=\"".htmlentities($q)."\"><br>
<b>Тип файлов</b>. 
Музыка:<input type=\"checkbox\" name=\"audio\" value=\"1\" ".set_GET_checkbox('audio', '1',true)."> |
 Видео <input type=\"checkbox\" name=\"video\" value=\"1\" ".set_GET_checkbox('video', '1',true).">  |
 Картинки <input type=\"checkbox\" name=\"img\" value=\"1\" ".set_GET_checkbox('img', '1',true).">  |
 Документы <input type=\"checkbox\" name=\"doc\" value=\"1\" ".set_GET_checkbox('doc', '1',true).">  |
 Архивы <input type=\"checkbox\" name=\"archive\" value=\"1\" ".set_GET_checkbox('archive', '1',true).">  |
 Другое <input type=\"checkbox\" name=\"other\" value=\"1\" ".set_GET_checkbox('other', '1',true).">  | <br>";
echo "Сортировать по 
 <select name=\"sorting\">
 <option value=\"date_upload\" " . set_GET_checkbox('sorting', 'date_upload', false, 'selected') . ">Дате добавления</option>
 <option value=\"comments\" " . set_GET_checkbox('sorting', 'comments', false, 'selected') . ">Числу комментариев</option>
 <option value=\"downloads\" " . set_GET_checkbox('sorting', 'downloads', false, 'selected') . ">Числу загрузок</option>
 <option value=\"abc\" " . set_GET_checkbox('sorting', 'abc', false, 'selected') . ">Алфавиту</option>
 </select><br>
 Файлы пользователя <input type=\"text\" name=\"user\" value=\""; 
 if($this->input->get('user'))
 {
 echo $this->input->get('user');
 }
 else
 {
 echo '';
 }
 echo "\">
 <input type=\"submit\" value=\"Поиск\"></form>";

if (isset($string_array) and count($string_array)<>0) 
{
    echo "<table border=\"1\">";
    echo "<tr><td><b>Имя файла</b></td><td><b>Размер</b></td><td>Комментариев</td><td>Загрузок</td><td>Дата загрузки</td></tr>";
    foreach ($string_array as $string)
	{
        echo "<tr><td><a href=\"/files/$string[id]\">$string[filename]</a></td><td>" . byte_format($string["size"]) . "</td><td>$string[counter_comments]</td><td>$string[counter_downloads]</td><td>$string[date_upload]</td></tr>";
    }
    echo "</table>";
}
else
{
echo "Результатов нет <br>";
}

if (isset($pagination_links))
{
    echo $pagination_links . "<br>";
}
?>
</td></tr></table>
<?php $this->load->view('index_page/index_footer_view');
?>