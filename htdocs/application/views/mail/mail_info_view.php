<?php
$this->db->where('read',0);
$this->db->where('folder','inbox');
$this->db->where('user_to',$this->session->userdata('id_user'));
$this->db->select('count(*) as newmessages');
$count=$this->db->get('messages');
$count=$count->row();
if($count->newmessages==0)
{
echo "<a href=\"/mail\">Новых сообщений нет</a>";
}
else
{
echo "<a href=\"/mail\">$count->newmessages новых сообщений</a>";
}
?>