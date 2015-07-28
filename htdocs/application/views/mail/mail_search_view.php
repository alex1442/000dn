<?php
$this->load->view('/index_page/index_head_view');
$this->load->view('infobox_view');
echo "Поиск сообщений: <form action=\"/mail/search\">
Ключевое слово: <input name=\"q\" value=\"";
if ($this->input->get('q'))
{
	echo htmlspecialchars($this->input->get('q'));
}
else
{
	echo '';
}
echo "\"><br>
Папки: <label>Все <input type=\"radio\" name=\"folder\" value=\"all\" ";
if (($this->input->get('folder') == "all") or (!$this->input->get('folder')))
{
	echo "checked";
}
else
{
	echo '';
}
echo "></label>
<label>Принятые <input type=\"radio\" name=\"folder\" value=\"inbox\" ";
if ($this->input->get('folder') == "inbox")
{
	echo "checked";
}
else
{
	echo '';
}
echo "></label>
<label>Посланные <input type=\"radio\" name=\"folder\" value=\"outbox\" ";
if ($this->input->get('folder') == "outbox")
{
	echo "checked";
}
else
{
	echo '';
}
echo "></label>
<table>
<tr><td>От кого:</td><td><input name=\"from\" value=\"";
if ($this->input->get('from'))
{
	echo htmlspecialchars($this->input->get('from'));
}
else
{
	echo '';
}
echo "\"></td></tr>
<tr><td>Кому:</td><td><input name=\"to\" value=\"";
if ($this->input->get('to'))
{
	echo htmlspecialchars($this->input->get('to'));
}
else
{
	echo '';
}
echo "\"></td></tr>
</table><input type=\"submit\" value=\"Искать\">
</form>";
if(isset($result))
{
if(count($result)==0)
{
echo "Результатов нет";
}
else
{
echo "<form method=\"post\" action=\"/mail?redirect=".urlencode($_SERVER['REQUEST_URI'])."\">";
$this->load->view('mail/mail_headmenu_view');
echo "<table border=\"1\">";
foreach($result as $message)
{
echo "<tr><td><input name=ids[] type=\"checkbox\" value=\"$message[id]\"></td>
<td>FOTO</td>
<td>От: <a href=\"/user/$message[user_from]\">$message[user_from]</a><br>Кому: <a href=\"/user/$message[user_to]\">$message[user_to]</a><br>$message[date]</td>";
echo "<td>";
if($message['folder']=="inbox")
{
echo "<a href=\"/mail\">Входящие</a>";
}
elseif($message['folder']=="outbox")
{
echo "<a href=\"/mail/sent\">Исходящие</a>";
}
echo "</td>";
echo "<td width=\"350\">";
if($message['read']==0)
{
echo "<b>";
}
echo "<a href=\"/mail/read/$message[id]\">$message[subject]<br>$message[text]</a>";
if($message['read']==0)
{
echo "</b>";
}
echo "</td>
<td><a href=\"/mail/remove/$message[id]?redirect=".urlencode($_SERVER['REQUEST_URI'])."\">Удалить</a><br>";
if($message['read']==0)
{
echo "<a href=\"/mail/markread/$message[id]?redirect=".urlencode($_SERVER['REQUEST_URI'])."\">Прочитано</a>";
}
echo "</td></tr>\n";
}
echo "</table></form>";
}
}
$this->load->view('/index_page/index_footer_view');
?>