<?php
$this->load->view('/index_page/index_head_view');
$this->load->view('infobox_view');
if (!isset($about)){$about='';}
if (isset($msg_ok)){echo $msg_ok;}
echo "<form method=\"post\" action=\"/index_page/vk_auth\">
<table>
<tr><td>Имя</td><td><input type=\"text\" value=\"$name\" name=\"name\" maxlength=\"40\"></td></tr>
<tr><td>Фамилия</td><td><input type=\"text\" value=\"$surname\" name=\"surname\" maxlength=\"40\"></td></tr>
<tr><td>e-mail: </td><td><input type=\"text\" name=\"email\" value=\"\"></td></tr>
<tr><td>Дата рождения:</td><td>
<select name=\"month\">
  <option value=\"0\">--</option>";
function numtomonth($num)
{
    switch ($num) {
        case 1:
            $month = "Январь";
            break;
        case 2:
            $month = "Февраль";
            break;
        case 3:
            $month = "Март";
            break;
        case 4:
            $month = "Апрель";
            break;
        case 5:
            $month = "Май";
            break;
        case 6:
            $month = "Июнь";
            break;
        case 7:
            $month = "Июль";
            break;
        case 8:
            $month = "Август";
            break;
        case 9:
            $month = "Сентябрь";
            break;
        case 10:
            $month = "Октябрь";
            break;
        case 11:
            $month = "Ноябрь";
            break;
        case 12:
            $month = "Декабрь";
            break;
    }
    return $month;
}
for ($nummonth = 1; $nummonth <= 12; $nummonth++) {
    if ($nummonth == $m) {
        echo "<option value=\"$nummonth\" selected>" . numtomonth($nummonth) . "</option>";
        $nummonth++;
    }
    echo "<option value=\"$nummonth\">" . numtomonth($nummonth) . "</option>";
}
echo "  </select>

  <select name=\"day\">
  <option value=\"0\">--</option>";

for ($day = 1; $day <= 31; $day++) {
    if ($d == $day) {
        echo "<option value=\"$day\" selected>$day</option>\n";
        $day++;
    }
    echo "<option value=\"$day\">$day</option>\n";
}
echo "
</select>
<select name=\"year\">
<option value=\"0000\">--</option>
";

for ($minyear = $currentyear = date('Y'); $minyear + 120 >= $currentyear; $minyear--) {
    if ($y == $minyear) {
        echo "<option value =\"$minyear\" selected>$minyear</option>\n";
        $minyear--;
    }
    echo "<option value =\"$minyear\">$minyear</option>\n";
}
echo "</select></td>
  </tr>
  <tr><td></td><td><label>Мужской<input type=\"radio\"  name=\"gender\" value=\"2\"";
if ($gender == "2") {
    echo "checked=\"checked\"";
}
echo "></label>
<label>Женский:
<input type=\"radio\" name=\"gender\" value=\"1\"";
if ($gender == "1") {
    echo "checked=\"checked\"";
}
echo "></label>";
echo "<label>Не указано:
<input type=\"radio\" name=\"gender\" value=\"0\"";
if ($gender == "0") {
    echo "checked=\"checked\"";
}
echo "></label></td></tr>
<tr><td>Местонахождение:</td><td><input type=\"text\" value=\"$location\" name=\"location\"></td></tr>
<tr><td>О себе</td><td><textarea name=\"about\">$about</textarea></td></tr>
</table>

<input type=\"submit\" value=\"Отправить\">
</form><font color=\"red\">
";

if (is_array($info_msg)){foreach($info_msg as $msg){echo $msg."<br>";}}
echo "</font>";

if (isset($info_msg))
{
$this->load->view('/infobox_view', $info_msg);
}

$this->load->view('/index_page/index_footer_view');

?>
