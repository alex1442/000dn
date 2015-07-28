<?php
$this->load->view('/index_page/index_head_view');
$this->load->view('infobox_view');
echo "<form method=\"post\" action=\"/mail?redirect=".urlencode($_SERVER['REQUEST_URI'])."\">";
$this->load->view('mail/mail_headmenu_view');
echo "<table border=\"1\">";
$this->load->helper('userdata');
foreach($inbox as $message)
{
echo "<tr><td><input name=ids[] type=\"checkbox\" value=\"$message[id]\"></td>
<td>FOTO</td>
<td>От: <a href=\"/user/$message[user_from]\">".idToLogin($message['user_from'])."</a><br>$message[date]</td>
<td width=\"350\">";
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
}
echo "</td></tr></table></form>";



$this->load->view('/index_page/index_footer_view');
?>