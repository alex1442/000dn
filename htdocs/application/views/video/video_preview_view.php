
<tr><td width ="200" height="120" style="background:#fff url(../videos/<?php echo $id; ?>/screenshot.png) no-repeat center center; color:#ffffff;background-size: contain;" >
<center><a href="/video/<?php echo $id; ?>"><img src="/images/play.png" alt=""></a></center></td>
<td><?php echo $filename ?>
<br>Дата загрузки &nbsp<?php echo $date_upload ?>
<br><?php echo $description ?>Просмотров &nbsp <?php echo $view_count;?>
<br>Загружено пользователем: <a href="/user/<?php echo $id_user; ?>" ><?php echo $login; ?></a>
<br>Длительность: <?php echo $duration?>
</td>
<?php

?>