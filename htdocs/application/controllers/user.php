<?php
function generated()
{
    // Символы, которые будут использоваться в хеше
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!?@#$%&*[]{}();:,<>~+=-_/|\\'; // набор символов

    // Количество символов в хеше.
    $max = 4;

    // Определяем количество символов в $chars
    $size = StrLen($chars) - 1;

    // Определяем пустую переменную, в которую и будем записывать символы.
    $hash = null;

    // Создаём пароль.
    while ($max--)
    {
        $hash .= $chars[rand(0, $size)];
    }
    return $hash;
}

class User extends CI_Controller
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

    public function index($id_user = null)
    {
        $this->output->enable_profiler(TRUE);
        $this->login_control();

			$id_user     = $this->db->escape($id_user);
            $userdata = $this->db->query("SELECT id_user,login,name,surname,birthday,gender,about,location,(YEAR( CURRENT_DATE ) - YEAR(  `birthday` )) - ( RIGHT( CURRENT_DATE, 5 ) < RIGHT(  `birthday` , 5 ) ) AS  `age` FROM md_users where id_user = $id_user")->row_array();
            if (isset($userdata['id_user']))
            {
                switch ($userdata['gender'])
                {
                    case "n/a":
                        $userdata['gender'] = "Не указано";
                        break;
                    case "m":
                        $userdata['gender'] = "Мужской";
                        break;
                    case "w":
                        $userdata['gender'] = "Женский";
                        break;
                }

                if ($userdata['birthday'] == "0000-00-00")
                {
                    $userdata['birthday'] = "Не указано";
                    $userdata['age']      = "";
                }
                else
                {
                    if (strlen($userdata['age']) == 4)
                    {
                        $userdata['age'] = "";
                    }
                    else
                    {
                        $userdata['age'] = "(Возраст " . $userdata['age'] . ")";
                    }
                }

                if ($userdata['location'] == "")
                {
                    $userdata['location'] = "Не указано";
                }
				$this->load->view('user/user_profile_view', $userdata);
            }
            else
            {
			show_404();
			}
    }
    public function editmyprofile()
    {
        $this->output->enable_profiler(TRUE);
        $this->login_control();

        if (!$this->input->post())
        {
            $this->db->select('name,surname,birthday,gender,about,location');
            $this->db->where('id_user', $this->session->userdata('id_user'));
            $userdata      = $this->db->get('users')->row_array();
            $mdy           = explode('-', $userdata['birthday']);
            $userdata['y'] = $mdy[0];
            $userdata['m'] = $mdy[1];
            $userdata['d'] = $mdy[2];
            $this->load->view('/user/editmyprofile_view', $userdata);
        }
        else
        {
            if ($this->input->post('name'))
            {
                if (strlen($this->input->post('name')) > 40)
                {
                    $error_msg[] = "Длина имени более 40 символов";
                }
                if (!preg_match("/^[а-яА-Яa-zA-Z-\']{1,40}$/u", $this->input->post('name')))
                {
                    $error_msg[] = "Недопустимые символы в имени";
                }
            }
            else
            {
                $error_msg[] = "Введите имя";
            }
            if ($this->input->post('surname'))
            {
                if (strlen($this->input->post('surname')) > 40)
                {
                    $error_msg[] = "Длина фамилии более 40 символов";
                }
                if (!preg_match("/^[а-яА-Яa-zA-Z-\']{1,40}$/u", $this->input->post('surname')))
                {
                    $error_msg[] = "Недопустимые символы в фамилии";
                }
            }
            else
            {
                $error_msg[] = "Введите фамилию";
            }
            if ($this->input->post('day') and $this->input->post('month') and $this->input->post('year'))
            {
                if ($this->input->post('year') <> "0000" and $this->input->post('month') <> "0" and $this->input->post('day') <> "0")
                {
                    $postdate    = mktime(0, 0, 0, $this->input->post('month'), $this->input->post('day'), $this->input->post('year'));
                    $currentdate = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                    if ((checkdate($this->input->post('month'), $this->input->post('day'), $this->input->post('year')) == false) or ($postdate > $currentdate))
                    {
                        $error_msg[] = "Неверная дата1";
                    }
                    if (checkdate($this->input->post('month'), $this->input->post('day'), "2012") == false and checkdate($this->input->post('month'), $this->input->post('day'), "2011") == false)
                    {
                        $error_msg[] = "Неверная дата2";
                    }
                }

                if (($this->input->post('month') == 0 xor $this->input->post('day') == 0) or ($this->input->post('month') == 0 and $this->input->post('day') == 0 and ($this->input->post('year') <> 0000)))
                {
                    $error_msg[] = "Для указания даты введите хотябы месяц и день";
                }
            }
            else
            {
                $error_msg[] = "Неверная дата";
            }
            if ($this->input->post('gender') <> 'm' and $this->input->post('gender') <> 'w' and $this->input->post('gender') <> 'n/a' and !$this->input->post('gender'))
            {
                $error_msg[] = "Укажите пол";
            }
            if (strlen($this->input->post('location')) > 1000)
            {
                $error_msg[] = "Максимальное значение поля \"Местонахождение\" 1000 символов";
            }
            if (strlen($this->input->post('about')) > 1000)
            {
                $error_msg[] = "Максимальное значение поля \"О себе\" 1000 символов";
            }
            if ($this->input->post('pass') or $this->input->post('pass2') or $this->input->post('oldpass'))
            {
                if ($this->input->post('pass') !== $this->input->post('pass2'))
                {
                    $error_msg[] = "Пароль и подтверждение не совпадают";
                }
                else
                {
                    if (strlen($this->input->post('pass')) < 6)
                    {
                        $error_msg[] = "Длина пароля от шести символов";
                    }
                    $this->db->where('id_user', $this->session->userdata('id_user'));
                    $query     = $this->db->select('password,hash');
                    $query     = $this->db->get('users');
                    $pass_data = $query->row_array();
                    if (sha1(md5(trim($this->input->post('oldpass')) . $pass_data['hash'])) !== $pass_data['password'])
                    {
                        $error_msg[] = "Старый пароль не правильный";
                    }
                    else
                    {
                        $hash    = generated();
                        $newpass = array(
                            'hash' => $hash,
                            'password' => sha1(md5(trim($this->input->post('oldpass')) . $hash))
                        );

                        $this->db->where('login', $this->session->userdata('username'));
                        $this->db->update('users', $newpass);
                    }

                }
            }

            if (!isset($error_msg))
            {
                if ($this->input->post('year') == 0000 and $this->input->post('month') <> 0 and $this->input->post('day') <> 0)
                {
                    $date = "0000-" . $this->input->post('month') . "-" . $this->input->post('day');
                }
                if ($this->input->post('year') == 0000 and $this->input->post('month') == 0 and $this->input->post('day') == 0)
                {
                    $date = "0000-00-00";
                }
                if ($this->input->post('year') <> 0000 and $this->input->post('month') <> 0 and $this->input->post('day') <> 0)
                {
                    $date = $this->input->post('year') . "-" . $this->input->post('month') . "-" . $this->input->post('day');
                }
                $data = array(
                    'name' => htmlspecialchars($this->input->post('name')),
                    'surname' => htmlspecialchars($this->input->post('surname')),
                    'birthday' => $date,
                    'gender' => $this->input->post('gender'),
                    'location' => htmlspecialchars($this->input->post('location')),
                    'about' => htmlspecialchars($this->input->post('about'))
                );
                $this->db->where('id_user', $this->session->userdata('id_user'));
                $this->db->update('users', $data);
                $msg_ok[] = "Профиль изменен";
                $this->session->set_flashdata('info', json_encode($msg_ok));
                header("Location: /editmyprofile");
                die();
            }
            else
            {
                $this->session->set_flashdata('info', json_encode($error_msg));
                header("Location: /editmyprofile");
                die();
            }
        }

    }
}
?>