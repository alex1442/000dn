<?php
if ($this->session->flashdata('info'))
{
$messages=json_decode($this->session->flashdata('info'));
}
if(isset($messages)){
foreach ($messages as $message)
{
echo $message."<br>";
}}
?>