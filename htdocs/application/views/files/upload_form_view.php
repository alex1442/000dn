<?php $this->load->view('index_page/index_head_view');
$this->load->view('infobox_view');?>
<table align="center" border="1" style="border-width: thin;" width="95%" ><tr><td width="100%">
<?php $this->load->helper('form'); echo form_open_multipart('/files');?>
<table><td>выберете файл для загрузки</td><td><?php echo form_upload('upload_file');?></td></tr>
<tr><td>комментарий к файлу до 200 символов(необязательно)</td>
<td>
<?php
$textarea_config=
	array(
           'name'        => 'description',
           'rows'	     => '10',
		   'cols'        => '50'
          );
 echo form_textarea($textarea_config);
?>
</td></tr>
<tr><td>Пароль для скачивания(необязательно)</td><td> <?php echo form_input('pass'); ?> </td></tr>
</table>
<?php echo "Не отображать файл в поиске" . form_checkbox('hidden', 'hidden', false) . "<br>";?>
<?php echo form_submit('mysubmit', 'Загрузить') . form_close();?>
</td></tr></table>
<?php $this->load->view('index_page/index_footer_view');?>
