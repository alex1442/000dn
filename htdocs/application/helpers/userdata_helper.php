<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('loginToId'))
{
	function loginToId($login)
	{
		$localCI=new CI_Controller;
		$row=$localCI->db->select('id_user')->where('login',$login)->get('users')->row();
		if($row)
		{
			return $row->id_user;
		}
		else
		{
			return 0;
		}
	}
}
if ( ! function_exists('idToLogin'))
{
	function idToLogin($id)
	{
		$localCI=new CI_Controller;
		$row=$localCI->db->select('login')->where('id_user',$id)->get('users')->row();
		if($row)
		{
			return $row->login;
		}
		else
		{
			return 0;
		}
	}
}
