<?php
class Video extends CI_Controller
{
    private function login_control()
    {
        if (!($this->session->userdata('id_user')))
        {
            $this->session->set_userdata('redirect', $_SERVER['REQUEST_URI']);
            $this->session->set_flashdata('info', json_encode(array(
                "Войдите чтобы просматривать эту страницу"
            )));
            header("Location: /index_page/auth");
            die();
        }
    }
    public function index()
    {
        $this->output->enable_profiler(TRUE);

        if (!$this->input->get('page'))
        {
            $page = 0;
        }
        elseif (is_int($this->input->get('page') / 1) and ($this->input->get('page') / 1 > 0))
        {
            $page = ($this->input->get('page') - 1) * 10;
        }
        else
        {
            show_error('Bad request', 400);
        }

        $this->db->where('progress', "done");
        $this->db->where('hidden', 0);
        $this->db->select('COUNT(*) as count_videos');
        $count_row = $this->db->get('video')->row_array();
        $this->load->library('pagination');

        $config['base_url']             = '/video?';
        $config['total_rows']           = $count_row['count_videos'];
        $config['per_page']             = 10;
        $config['page_query_string']    = TRUE;
        $config['query_string_segment'] = 'page';
        $config['use_page_numbers']     = TRUE;
        $this->pagination->initialize($config);
        $data['pagination_links'] = $this->pagination->create_links();

        $this->db->where('progress', "done");
        $this->db->where('hidden', 0);
        $this->db->limit(10, $page);

        $query          = $this->db->get('video');
        $data['videos'] = $query->result_array();
        $ids_users=array();
        foreach($data['videos'] as $video)
        {
		if(!in_array($video['id_user'],$ids_users))
        {
			$ids_users[]=$video['id_user'];
		}
		}
	 $pairs=$this->db->select('login,id_user')->where_in('id_user',$ids_users)->get('users')->result_array();

		foreach($pairs as $pair)
        {
			$key_value[$pair['id_user']]=$pair['login'];
		}

		foreach($data['videos'] as &$video)
        {
			$video['login']=$key_value[$video['id_user']];
		}
        $this->load->view("video/video_list_view", $data);
    }

    public function watch($id)
    {
        $this->output->enable_profiler(TRUE);
        $this->db->where('id', $id);
        $this->db->select('id,id_user,date_upload,view_count,video_title,duration,description,view_count');
		$result=$this->db->get('video')->row_array();
		if (!$result)
        {
            show_error('Not Found', 404);
            die();
        }
            $tags = $this->db->query("select value from md_tags where id in (select id_tag from md_tagmap where id_item=$id)")->result_array();

            foreach ($tags as $tag)
            {
                $result['tags'][] = $tag['value'];
            }
        $this->db->where('id', $id);
		$this->db->update('video', array("view_count" => ++$result['view_count']));
		$result['login']=$this->db->query("select login from md_users where id_user=".$result['id_user'])->row()->login;
        $this->load->view("video/video_watch_view", $result);
    }

    public function edit($id)
    {
        $this->output->enable_profiler(TRUE);

        $this->login_control();

        $this->db->select('id_user,description,video_title,hidden');
        $this->db->where('id', $id);
        $result = $this->db->get('video')->row_array();

        if (count($result) == 0)
        {
            show_error("<b>Не найдено. 404</b>", 404);
            die();
        }
        if ($result['id_user'] <> ($this->session->userdata('id_user')))
        {
            show_error("<b>Forbidden 403</b>", 403);
            die();
        }
        /*Получим список тегов в БД*/
        $tags_in_bd = array();
        $sql        = $this->db->query("select value from md_tags where id in (select id_tag from md_tagmap where id_item=$id)");

        $tags_result = $sql->result_array();
        foreach ($tags_result as $value)
        {
            $tags_in_bd[] = $value['value'];
        }

        if ($this->input->post())
        {
            if ($this->input->post('description'))
            {
                $description = $this->input->post('description');
            }
            else
            {
                $description = $result['description'];
            }
            if ($this->input->post('hidden') == "on")
            {
                $hidden = "1";
            }
            elseif ($this->input->post('hidden') === "0")
            {
                $hidden = "0";
            }
            else
            {
                $hidden = $result["hidden"];
            }
            if ($this->input->post('video_title'))
            {
                $video_title = $this->input->post('video_title');
            }
            else
            {
                $video_title = $result['video_title'];
            }
            $data = array(
                'description' => htmlspecialchars($description),
                'hidden' => $hidden,
                'video_title' => htmlspecialchars($video_title)
            );
            $this->db->where('id', $id);
            $this->db->update('video', $data);


            if ($this->input->post('tags') !== null)
            {
                $string_tag     = preg_replace("/[<>^*()=+\|[\]\?!&]/", '', strtolower($this->input->post('tags')));
                $tags_in_post   = preg_split("/[\r\n|\r|\n|\s]+/u", trim($string_tag));
                //print_r($tags_in_post);
                $tags_to_add    = array();
                $tags_to_delete = array();
                foreach ($tags_in_post as $tag_in_post)
                {
                    if (!in_array($tag_in_post, $tags_in_bd))
                    {
                        //$tags_to_add[] = mysql_escape_string(htmlspecialchars($tag_in_post));
                        //Проверим существование тега в бд не прикрепленной к записи
                        $this->db->select('id');
                        $this->db->where('value', $tag_in_post);
                        $search_tag = $this->db->get('tags')->row_array();
                        if (count($search_tag) == 0)
                        {
                            $insert_tag['value'] = $tag_in_post;
                            $this->db->insert("tags", $insert_tag);
                            $id_tag = $this->db->insert_id();
                        }
                        else
                        {
                            $id_tag = $search_tag['id'];
                        }

                        $insert_tagmap = array(
                            'id_tag' => $id_tag,
                            'id_item' => $id,
                            'type_item' => 'video'
                        );
                        $this->db->insert("tagmap", $insert_tagmap);
                    }
                }
                foreach ($tags_in_bd as $tag_in_bd)
                {
                    if (!in_array($tag_in_bd, $tags_in_post))
                    {
                        $tags_to_delete[] = $this->db->escape($tag_in_bd);
                    }
                }
                if (count($tags_to_delete) <> 0)
                {
                    $this->db->query("delete from md_tagmap where id_item = $id and id_tag in (select id from md_tags where value in (" . implode(",", $tags_to_delete) . "))");

                }
                //print_r($tags_to_add);
                // print_r($tags_to_delete);

            }
            $info_msg[] = "Описание изменено";
            $this->session->set_flashdata('info', json_encode($info_msg));
            header("Location: /video/edit/$id");
        }

        if ($result['hidden'] == 0)
        {
            $hidden_checkbox = '';
        }
        else
        {
            $hidden_checkbox = 'checked';
        }

        $tags_to_string = '';
        foreach ($tags_in_bd as $tag)
        {
            $tags_to_string = $tags_to_string . $tag . " ";
        }
        $data = array(
            'description' => $result['description'],
            'video_title' => $result['video_title'],
            'id' => $id,
            'hidden' => $hidden_checkbox,
            'tags' => trim($tags_to_string)
        );
        $this->load->view('video/video_edit_view', $data);

    }

    public function upload()
    {
        $this->login_control();
        if (isset($_FILES['upload_video']) and ($_FILES['upload_video']['error']) == 0)
        {
            $com = "/usr/bin/mediainfo  --Output=file:///home/alex1442/media_basic.txt '" . $_FILES['upload_video']['tmp_name'] . "' >" . $_FILES['upload_video']['tmp_name'] . "_mediainfo.txt";
            exec($com);
            $video_info = file_get_contents($_FILES['upload_video']['tmp_name'] . "_mediainfo.txt");
            unlink($_FILES['upload_video']['tmp_name'] . "_mediainfo.txt");
            $video_info_str_array = preg_split("/[\r\n]/", $video_info);
            foreach ($video_info_str_array as $row)
            {
                if (preg_match("/^Duration:/", $row))
                {
                    $duration_array = explode(':', $row);
                    $duration       = $duration_array[1] / 1000; //Длительность в секундах
                }
                if (preg_match("/^Width:/", $row))
                {
                    $width_array = explode(':', $row);
                    $width       = $width_array[1];
                }
            }
            if (($width > 0) and ($duration > 0))
            {
                $this_video = true;
            }
            else
            {
                $this_video = false;
            }

            if ($this_video)
            {
                $input = $_FILES['upload_video']['name'];
                if ($this->input->post('description'))
                {
                    $description = $this->input->post('description');
                }
                else
                {
                    $description = '';
                }
                if ($this->input->post('hidden') == "on")
                {
                    $hidden = "1";
                }
                else
                {
                    $hidden = "0";
                }
                if ($this->input->post('video_title'))
                {
                    $video_title = $this->input->post('video_title');
                }
                else
                {
                    $video_title = $input;
                }

                $data = array(
                    'id_user' => $this->session->userdata('id_user'),
                    'filename' => $input,
                    'duration' => $duration,
                    'description' => htmlspecialchars($description),
                    'hidden' => $hidden,
                    'video_title' => htmlspecialchars($video_title)
                );
                if ($this->db->insert("video", $data))
                {
                    $id_video = $this->db->insert_id();
                    if ($this->input->post('tags'))
                    {
                        $string_tag = trim(strtolower($this->input->post('tags')));
                        $string_tag = preg_replace("/[<>^*()=+\|[\]\?!&\/\\]/", '', strtolower($this->input->post('tags')));
                        $tags       = preg_split("/[\r\n|\r|\n|\s]+/u", $string_tag);
                        foreach ($tags as $tag)
                        {
                            $this->db->select('id');
                            $this->db->where("lower(value)", mysql_escape_string($tag));
                            $result = $this->db->get("tags")->row_array();
                            if (count($result) == 0)
                            {
                                $insert_tag['value'] = mysql_escape_string($tag);
                                $this->db->insert("tags", $insert_tag);
                                $id_tag        = mysql_insert_id();
                                $insert_tagmap = array(
                                    'id_tag' => $id_tag,
                                    'id_item' => $id_video,
                                    'type_item' => 'video'
                                );
                                $this->db->insert("tagmap", $insert_tagmap);
                            }
                            else
                            {
                                $insert_tagmap = array(
                                    'id_tag' => $result['id'],
                                    'id_item' => $id_video,
                                    'type_item' => 'video'
                                );
                                $this->db->insert("tagmap", $insert_tagmap);
                            }
                        }
                    }
                    mkdir("videos/$id_video/");
                    move_uploaded_file($_FILES['upload_video']['tmp_name'], "videos/$id_video/$input");

                    $info_msg[] = "Видео поставлено в очередь";
                    $this->session->set_flashdata('info', json_encode($info_msg));
                }
                else
                {
                    $error_msg[] = "Ошибка добавления";
                    $this->session->set_flashdata('info', json_encode($error_msg));
                }
                header("Location: /video/upload");
                die();
            }
            else
            {
                $error_msg[] = "Загруженный файл не является видеофайлом";
                $this->session->set_flashdata('info', json_encode($error_msg));
                header("Location: /video/upload");
                die();
            }
        }
        $this->load->view('video/video_upload_view');

    }

    public function delete($id)
    {
        $this->output->enable_profiler(TRUE);

        $this->login_control();

        $this->db->select('id_user,description,video_title,hidden');
        $this->db->where('id', $id);
        $result = $this->db->get('video')->row_array();
        if (count($result) == 0)
        {
            show_error("<b>Не найдено. 404</b>", 404);
            die();
        }
        if ($result['id_user'] <> ($this->session->userdata('id_user')))
        {
            show_error("<b>Forbidden 403</b>", 403);
            die();
        }
        $this->load->helper('file');
        $this->db->where('id', $id);
        $this->db->delete('video');
        $this->db->where('id_item', $id);
        $this->db->delete('tagmap');
        delete_files("videos/$id", true);
        rmdir("videos/$id");
        $this->db->query("delete from `md_tags` where id not in (select `id_tag` from `md_tagmap`)"); //удалим теги без записей
        header("Location: /video/");

    }

    public function tag($tag = '')
    {
        $this->output->enable_profiler();
        $tag = $this->db->escape(urldecode($tag));
        $sql = $this->db->query("select * from md_video where id in (select id_item from md_tagmap where id_tag in(select id from md_tags where value=$tag))");
        if ($sql->num_rows() <> 0)
        {
            $result_ids = $sql->result_array();
            foreach ($result_ids as $id)
            {
                $data['videos'][] = $id;
            }
        }
        else
        {
            $data['videos']['error'] = "Нет видеофайлов помеченных тегом " . htmlspecialchars(urldecode($tag));
        }

        $this->load->view("video/video_list_view", $data);
    }

    public function search()
    {
        $this->output->enable_profiler(TRUE);
        $this->login_control();
        if (!$this->input->get('page'))
        {
            $page = 0;
        }
        elseif (is_int($this->input->get('page') / 1) and ($this->input->get('page') / 1 > 0))
        {
            $page = ($this->input->get('page') - 1) * 10;
        }
        else
        {
            show_error('Bad request', 400);
        }

        $url = explode('&', $this->input->server('QUERY_STRING')); //разбираем строку запроса  get  на отдельные сегменты(переменная=значение)

        foreach ($url as $key => $var_val)
        {
            if (preg_match('/^page=/', $var_val)) //удаляем переменную  page  из массива
            {
                unset($url[$key]);
            }
        }
        $url = implode('&', $url); //заново собираем строку запроса без переменных  page


        if ($this->input->get('q'))
        {
            $q     = $this->db->escape_like_str($this->input->get('q'));
            $sql[] = "((`video_title` like '%$q%') or (`filename` like '%$q%') or (`description` like '%$q%') or (`id` in (select `id_item` from `md_tagmap` where `id_tag` in (select `id` from `md_tags` where (`value` like '%$q%')))))";
        }
        $sql[] = "(`progress`='done') and (`hidden`=0)";
        if ($this->input->get('user'))
        {
            $user  = $this->db->escape($this->input->get('user'));
            $row=$this->db->select('id_user')->where('login',$this->input->get('user'))->get('users')->row();

             if ($row)
            {
			$id_user=$row->id_user;
            $sql[] = "(`id_user`=$id_user)";
			}
			else
			{
				$data['videos']['error'] = "Нет результатов";
			}
		}

			if(!isset($data['videos']['error']))
			{
        $sql            = "from md_video where (" . implode(' and ', $sql) . ")";
        $data['videos'] = $this->db->query("select SQL_CALC_FOUND_ROWS * " . $sql . " limit $page, 10")->result_array();

        $count_row      = $this->db->query("select FOUND_ROWS() as count_videos")->row_array();
        $this->load->library('pagination');
        $config['base_url']             = '/video/search?' . $url;
        $config['total_rows']           = $count_row['count_videos'];
        $config['per_page']             = 10;
        $config['page_query_string']    = TRUE;
        $config['query_string_segment'] = 'page';
        $config['use_page_numbers']     = TRUE;
        $this->pagination->initialize($config);
        $data['pagination_links'] = $this->pagination->create_links();

        if (0 == $count_row['count_videos'])
        {
            $data['videos']['error'] = "Нет результатов";
        }
        else
        {

		$ids_users=array();
        foreach($data['videos'] as $video)
        {
		if(!in_array($video['id_user'],$ids_users))
        {
			$ids_users[]=$video['id_user'];
		}
		}
	 $pairs=$this->db->select('login,id_user')->where_in('id_user',$ids_users)->get('users')->result_array();

		foreach($pairs as $pair)
        {
			$key_value[$pair['id_user']]=$pair['login'];
		}

		foreach($data['videos'] as &$video)
        {
			$video['login']=$key_value[$video['id_user']];
		}
	}
}
        $this->load->view("video/video_search_list_view", $data);

    }
}
?>