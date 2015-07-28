<?php $this->load->view('index_page/index_head_view');?>
<table align="center" border="1" style="border-width: thin;" width="95%" ><tr><td width="100%">Загрузка завершена<br>
Ссылка на загрузку :<textarea style="width:400px; height:40px;"><?php echo $download_link;?></textarea>
<br>
<a href="/files">Загрузить еще</a>
</td></tr></table>
<?php $this->load->view('index_page/index_footer_view');?>