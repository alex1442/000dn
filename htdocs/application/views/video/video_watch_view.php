<?php
$this->load->view('index_page/index_head_view');
echo "<center>
<h1>$video_title</h1>
<video  id=\"video\" width=\"640\"  controls=\"controls\"><source src=\"/videos/$id/video.webm\" type='video/webm; codecs=\"vp8, vorbis\"'></video>
</center>";
if(isset($tags))
{
	echo "Теги видео:";
	foreach($tags as $tag)
{
	echo "<a href=\"/video/tag/". urlencode($tag) ."\">". $tag."</a>\n";
}
}
echo "<br>Видео загрузил: <a href=\"/user/$id_user\">$login</a><br>
Количество просмотров: $view_count";
if($id_user===$this->session->userdata('id_user'))
{
echo "<br><a href=\"/video/delete/$id\">Удалить видео</a><br> <a href=\"/video/edit/$id\">Редактировать видео</a>";
}
?>
<script type="text/javascript">
var video = document.getElementById("video");
</script>
<br>
<button onclick="video.mozRequestFullScreen();">Во весь экран</button>
<button onclick="video.webkitEnterFullScreen();">Во весь экран</button>
<?php echo $description;
$this->load->view('index_page/index_footer_view');
?>


