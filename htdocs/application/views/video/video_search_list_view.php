<?php
$this->load->view('index_page/index_head_view');
$this->load->view("video/video_search_view");
if(isset($videos['error']))
{
	echo $videos['error'];
	}
	else
	{
		echo "<table border=1>";
	foreach ($videos as $video)
	{
$duration_obj= new DateTime();
$duration_obj->setTime(0,0,round($video['duration']));
$duration_str=$duration_obj->format('H:i:s');
    	$video_data=array(
		'id'=>$video['id'],
		'login'=>$video['login'],
		'filename'=>$video['filename'],
		'duration'=>$duration_str,
		'description'=>$video['description'],
		'date_upload'=>$video['date_upload'],
		'view_count'=>$video['view_count'],
		'id_user'=>$video['id_user']
		);
		$this->load->view('video/video_preview_view',$video_data);
	}
	echo "</table><br>";
	}
	if(isset($pagination_links))
	{
	echo $pagination_links;
	}
$this->load->view('index_page/index_footer_view');
?>