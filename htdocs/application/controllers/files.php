<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
function _generated()
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
class Files extends CI_Controller
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
        $this->login_control();
        set_time_limit(0); // убираем лимит времени выполнения
        if ($_FILES <> null) //если $_files не пуст(он может быть пуст при переходе на страницу загрузки по URL) то продолжаем
        {
            if ($_FILES['upload_file']['error'] <> 4) //если пользователь выбрал через браузер файл для загрузки то продолжаем
            {
                if ($_FILES['upload_file']['error'] == 0) //если ошибок при загрузке нет то продолжаем
                {
                    $max_size = 2048; //число  -  предельный размер файла для загрузки в мегабайтах
                    if ($_FILES['upload_file']['size'] <= ($max_size * 1024 * 1024)) //ограничение в рамере файла
                    {
                        $this->load->helper('array');
                        $this->load->helper('string');
                        $this->load->database();
                        $this->load->helper('url');

                        if ($this->input->post('pass')) //если через post передан пароль
                        {
                            $hash      = _generated();
                            $sha1_pass = sha1(md5(trim($this->input->post('pass')) . $hash));
                        }
                        else //если пароль не передан то шифрованный пароль для передачи в бд пуст
                        {
                            $hash      = "";
                            $sha1_pass = "";
                        }

                        if ($this->input->post('hidden') == "hidden") //значение для бд скрывает файл для поиска, в зависимости от значения чекбокса
                        {
                            $hidden = 1;
                        }
                        else
                        {
                            $hidden = 0;
                        }

                        $explode = explode(".", $_FILES['upload_file']['name']);
                        if (count($explode) > 1) //выделяем расширение с имени файла
                        {
                            $ext         = strtolower(end($explode)); //расширение в нижнем регистре
                            //массивы с содержанием расширений файлов, для определения типов файла
                            $audio_ext   = array(
                                "mp3",
                                "wma",
                                "ogg",
                                "flac",
                                "wav",
                                "amr"
                            );
                            $video_ext   = array(
                                "avi",
                                "mp4",
                                "flv",
                                "mkv",
                                "3gp",
                                "wmv"
                            );
                            $img_ext     = array(
                                "jpg",
                                "jpeg",
                                "gif",
                                "png",
                                "bmp",
                                "tiff"
                            );
                            $doc_ext     = array(
                                "doc",
                                "pdf",
                                "xls",
                                "ppt",
                                "html",
                                "xps",
                                "txt",
                                "rtf",
                                "odt",
                                "docx",
                                "ods",
                                "odp"
                            );
                            $archive_ext = array(
                                "rar",
                                "zip",
                                "7z",
                                "bz2",
                                "gz",
                                "gzip",
                                "tar",
                                "tbz",
                                "tbz2",
                                "tgz"
                            );
                            //последовательный поиск и определение типа файлов

                            if (in_array($ext, $audio_ext))
                            {
                                $type = "audio";
                            }
                            elseif (in_array($ext, $video_ext))
                            {
                                $type = "video";
                            }
                            elseif (in_array($ext, $doc_ext))
                            {
                                $type = "doc";
                            }
                            elseif (in_array($ext, $img_ext))
                            {
                                $type = "img";
                            }
                            elseif (in_array($ext, $archive_ext))
                            {
                                $type = "archive";
                            }
                            else
                            {
                                $type = "";
                            }
                        }
                        if ((count($explode) == 1) or (count($explode) > 1 and $explode[0] == null)) //если файл без расширения например имя файла - "myfile" ИЛИ имя начинается с точки(.htaccess) то расширениe($ext) для занесения в бд пусто
                        {
                            $ext  = "";
                            $type = "";
                        }
                        //данные для записи в бд
                        $data = array(
                            'hidden' => $hidden,
                            'filename' => $_FILES['upload_file']['name'],
                            'description' => htmlspecialchars($this->input->post('description')),
                            'size' => $_FILES['upload_file']['size'],
                            'pass' => $sha1_pass,
                            'hash' => $hash,
                            'type' => $type,
                            'ext' => $ext,
                            'id_user' => $this->session->userdata('id_user')
                        );
                        if ($this->db->insert('files', $data)) //запись в бд
                        {
                            echo $this->db->last_query();
                            $id_file = $this->db->insert_id();
                            mkdir("uploads/$id_file"); //сoхранение загруженого файла на диске(после успешного занесения сведений в бд), папка хранения это -  id файла
                            $full_filename = "uploads/" . $id_file . "/" . $_FILES['upload_file']['name'];
                            if (preg_match('/win/', strtolower(PHP_OS)))
                            {
                                $full_filename = iconv("UTF-8", "Windows-1251", $full_filename);
                            }
                            move_uploaded_file($_FILES['upload_file']['tmp_name'], $full_filename);
                            //формирование ссылки загрузки
                            $link['download_link'] = base_url() . "files/" . $id_file;
                            $this->load->view('files/upload_ok_view', $link);
                        }
                    }
                    else //если размер файла больше заданного
                    {
                        echo "Предельный размер файла для загрузки - $max_size мегабайт";
                        $this->load->view('files/upload_form_view');
                    }
                }
                else
                {
                    echo "Ошибка загрузки файла";
                    $this->load->view('files/upload_form_view');
                }
            }
            else //если пользователь не выбрал файл для загрузки(после нажатия кнопки submit)(error=4)
            {
                echo "<font style=\"color:red\">файл для загрузки не выбран</font>";
                $this->load->view('files/upload_form_view');
            }

        }
        else //если POST files пуст выводим форму загрузки, при переходе на страницу через url
        {
            $this->load->view('files/upload_form_view');
        }

    }

    public function page($id_file = 0, $action = "") //функция отображения страницы файла
    {
        $this->login_control();
        if (!is_numeric($id_file)) //останавливаем скрипт если $id_file не число
        {
            $error_msg[] = "Файла не существует";
            $this->session->set_flashdata('info', json_encode($error_msg));
            header("Location: /files");
            die();
        }
        if ($action == "")
        {
            $query          = $this->db->query("SELECT filename,pass,hash,description,counter_downloads,id_user FROM md_files WHERE id=$id_file");
            $filename_array = $query->row_array();


            if ($filename_array) //если имя файла найдено в бд то:
            {
                $filename      = $filename_array['filename'];
                $full_filename = "uploads/" . $id_file . "/" . $filename; //папка хранения требуемого файла на диске
                if (preg_match('/win/', strtolower(PHP_OS)))
                {
                    $full_filename = iconv("UTF-8", "Windows-1251", $full_filename);
                }
            }
            else // если файла нет
            {
                $error_msg[] = "Такого файла нет";
                $this->session->set_flashdata('info', json_encode($error_msg));
                header("Location: /files");
                die();
            }
            // получим размер файла
            $filesize = filesize($full_filename);
            $this->db->select('login');
            $this->db->where('id_user', $filename_array['id_user']);
            $user      = $this->db->get('users')->row_array();
            //данные для отображения вида "просмотр страницы файла"
            $file_info = array(
                'filename' => $filename_array['filename'],
                'description' => $filename_array['description'],
                'counter' => $filename_array['counter_downloads'],
                'pass' => $filename_array['pass'],
                'filesize' => $filesize,
                'id_file' => $id_file,
                'user' => $user['login'],
                'id' => $id_file,
                'id_user' => $filename_array['id_user']
            );

            $this->load->view('files/page_file_view', $file_info);
        }
        if ($action == "edit")
        {
            if ($this->input->post())
            {
                $query          = $this->db->query("SELECT filename,pass,hash,description,hidden,id_user FROM md_files WHERE id=$id_file");
                $filename_array = $query->row_array();
                if ($this->session->userdata('id_user') == $filename_array['id_user'])
                {
                    if (strlen($this->input->post('description')) > 1000)
                    {
                        $error_msg[] = "Максимальная длина описания 1000 символов";
                    }
                    if ($this->input->post('pass'))
                    {
                        $hash = _generated();
                        $pass = sha1(md5(trim($this->input->post('pass')) . $hash));
                    }
                    else
                    {
                        $hash = $pass = "";
                    }
                    if ($this->input->post('hidden') == "on")
                    {
                        $hidden = true;
                    }
                    if (!isset($error_msg))
                    {
                        $data = array(
                            'description' => htmlspecialchars($this->input->post('description')),
                            'hidden' => $hidden,
                            'hash' => $hash,
                            'pass' => $pass
                        );
                        $this->db->where('id', $id_file);
                        $this->db->update('files', $data);
                        header("Location: /files/$id_file");
                        die();
                    }
                    else
                    {
                        $this->session->set_flashdata('info', json_encode($error_msg));
                        header("Location: /files/$id_file/edit");
                        die();
                    }
                }
                else
                {
                    $error_msg[] = "Этот файл не принадлежит вам";
                    $this->session->set_flashdata('info', json_encode($error_msg));
                    header("Location: /files/$id_file");
                    die();
                }
            }
            else
            {
                $query          = $this->db->query("SELECT filename,pass,hash,description,hidden,id_user FROM md_files WHERE id=$id_file");
                $filename_array = $query->row_array();
                if (count($filename_array) <> 0)
                {
                    if ($this->session->userdata('id_user') == $filename_array['id_user'])
                    {
                        $this->load->view('files/file_edit_view', $filename_array);
                    }
                    else
                    {
                        $error_msg[] = "Этот файл не принадлежит вам";
                        $this->session->set_flashdata('info', json_encode($error_msg));
                        header("Location: /files/$id_file");
                        die();
                    }
                }
                else
                {
                    $error_msg[] = "Такого файла нет";
                    $this->session->set_flashdata('info', json_encode($error_msg));
                    header("Location: /files");
                    die();
                }
            }
        }
        if ($action == "delete")
        {
            $query          = $this->db->query("SELECT id_user,filename FROM md_files WHERE id=$id_file");
            $filename_array = $query->row_array();
            if (count($filename_array) <> 0)
            {
                if ($this->session->userdata('id_user') == $filename_array['id_user'])
                {
                    if ($this->db->delete('files', array(
                        'id' => $id_file
                    )))
                    {
                        $filename = $filename_array['filename'];
                        if (is_file("uploads/$id_file/$filename"))
                        {
                            unlink("uploads/$id_file/$filename");
                            rmdir("uploads/$id_file");
                            $info_msg[] = "Файл удален";
                            $this->session->set_flashdata('info', json_encode($info_msg));
                            header("Location: /files");
                            die();
                        }
                        else
                        {
                            echo "Ошибка доступа к файлу";
                        }
                    }
                    else
                    {
                        echo "Не смог удалить из БД";
                    }
                }

                else
                {
                    $error_msg[] = "Этот файл не принадлежит вам";
                    $this->session->set_flashdata('info', json_encode($error_msg));
                    header("Location: /files/$id_file");
                    die();
                }
            }
            else
            {
                $error_msg[] = "Такого файла нет";
                $this->session->set_flashdata('info', json_encode($error_msg));
                header("Location: /files");
                die();
            }
        }

    }

    public function download($id_file = 0) //функция скачивания файла
    {
        $this->session->set_userdata('redirect', $_SERVER['REQUEST_URI']);
        $this->login_control();
        if (!is_numeric($id_file)) //останавливаем скрипт если $id_file не число
        {
            $error_msg[] = "Файла не существует";
            $this->session->set_flashdata('info', json_encode($error_msg));
            header("Location: /files");
            die();
        }
        $query          = $this->db->query("SELECT filename,pass,hash,counter_downloads FROM md_files WHERE id=$id_file");
        $filename_array = $query->row_array();

        if ($filename_array) //если имя файла найдено в бд то:
        {
            $post_pass = sha1(md5(trim($this->input->post('pass')) . $filename_array['hash']));
            if (($post_pass == $filename_array['pass']) or (!$filename_array['pass'])) //(если  зашифрованный пароль отправленный с формы совпадает с паролем к файлу в бд) ИЛИ (пароль к файлу в бд пуст) то даем скачивать файл
            {
                $filename      = $filename_array['filename'];
                $full_filename = "uploads/" . $id_file . "/" . $filename; //формирование пути хранения требуемого файла
                if (preg_match('/win/', strtolower(PHP_OS)))
                {
                    $filename      = iconv("UTF-8", "Windows-1251", $filename); //устранение проблем со скачиванием файлов именах которых кирилические символы
                    $full_filename = iconv("UTF-8", "Windows-1251", $full_filename);
                }
                $filesize = filesize($full_filename);

                set_time_limit(0); // убираем лимит времени выполнения
                $memory_limit = 1024 * 1024 * 1; //лимит памяти в байтах (последний множитель число мегабайтов)
                // смещение от начала файла
                $range        = 0;
                $f            = fopen($full_filename, 'rb'); // открываем файл на чтение

                if (isset($_SERVER['HTTP_RANGE'])) // поддерживается ли докачка
                {
                    $range = $_SERVER['HTTP_RANGE'];
                    $range = str_replace('bytes=', '', $range);
                    $range = str_replace('-', '', $range);
                    if ($range)
                        fseek($f, $range);
                }

                // если есть смещение
                if ($range)
                {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 206 Partial Content');
                }
                else
                {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
                }

                header('Last-Modified: ' . date('D, d M Y H:i:s T', filemtime($full_filename)));
                header('Content-Length: ' . ($filesize - $range));
                header('Accept-Ranges: bytes');
                header('Content-Range: bytes ' . $range . '-' . ($filesize - 1) . '/' . $filesize);
                header('Content-Disposition: attachment; filename="' . $filename . '"');

                while (!feof($f))
                {
                    echo fread($f, $memory_limit);
                    //sleep(1);
                    flush();
                }

                // закрываем файл
                fclose($f);

                //запись новых значений о количестве скачивании в бд,и времени последнего скачивания
                $updated['counter_downloads'] = $filename_array['counter_downloads'] + 1;
                $updated['last_download']     = date('Y-m-d H:i:s', time());
                $this->db->where('id', $id_file);
                $this->db->update('files', $updated);

            }
            else //если пароль не совпал то показываем страницу
            {
                $error_msg[] = "Пароль не верен";
                $this->session->set_flashdata('info', json_encode($error_msg));
                header("Location: /files/$id_file");
                die();
            }

        }
        else // если файла нет
        {
            //header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
            die("Файла не существует");
        }

    }

    public function search()
    {
        $this->output->enable_profiler(TRUE);
        $this->login_control();
        function set_GET_checkbox($name = '', $value = '', $default = FALSE, $insertstr = 'checked="checked"')
        {
            if (!isset($_GET[$name]))
            {
                if (count($_GET) === 0 AND $default == TRUE)
                {
                    return $insertstr;
                }
                return '';
            }
            $name = $_GET[$name];
            if (is_array($name))
            {
                if (!in_array($value, $name))
                {
                    return '';
                }
            }
            else
            {
                if (($name == '' OR $value == '') OR ($name != $value))
                {
                    return '';
                }
            }
            return $insertstr;
        }

        switch ($this->input->get('sorting'))
        {
            case "date_upload":
                $sorting  = "date_upload";
                $asc_desc = "DESC";
                break;
            case "comments":
                $sorting  = "counter_comments";
                $asc_desc = "DESC";
                break;
            case "downloads":
                $sorting  = "counter_downloads";
                $asc_desc = "DESC";
                break;
            case "abc":
                $sorting  = "filename";
                $asc_desc = "ASC";
                break;
            default:
                $sorting  = "date_upload";
                $asc_desc = "DESC";
        }

        if (($this->input->get('audio') == 1) or !is_array($this->input->get()))
        {
            $types[] = 'audio';
        }

        if ($this->input->get('video') == 1 or !is_array($this->input->get()))
        {
            $types[] = 'video';
        }

        if ($this->input->get('doc') == 1 or !is_array($this->input->get()))
        {
            $types[] = 'doc';
        }

        if ($this->input->get('img') == 1 or !is_array($this->input->get()))
        {
            $types[] = 'img';
        }

        if ($this->input->get('archive') == 1 or !is_array($this->input->get()))
        {
            $types[] = 'archive';
        }

        if ($this->input->get('other') == 1 or !is_array($this->input->get()))
        {
            $types[] = '';
        }

        //$search_form -  массив данных для отображения в виде search_view
        $q = $search_form['q'] = $this->input->get('q');
        if (is_array($this->input->get()) and isset($types))
        {
            if ($this->input->get('user'))
            {
                $this->db->select('id_user,login');
                $this->db->where('login', $this->input->get('user'));
                $user = $this->db->get('users')->row_array();
            }
            $page = $this->input->get('page');
            //выборка файлов не скрытых для поиска
            //соответствие поиского запроса и полей  description  или  filename
            //файлы защищенные паролем не показываем в поиске
            // поиск только выбранных типов файлов
            if ($this->input->get('q'))
            {
                $q    = $this->db->escape_like_str($this->input->get('q'));
                $like = "(description LIKE '%$q%' OR filename LIKE '%$q%')";
                $this->db->where($like);
            }
            if ($this->input->get('user'))
            {
                $login = $this->input->get('user');
                $this->db->where("id_user in (select id_user from md_users where login='$login')");
            }

            if (isset($user['id_user']) and ($user['id_user'] == $this->session->userdata('id_user')))
            {
                $this->db->select('COUNT(*)')->where_in('type', $types);
            }
            else
            {
                $this->db->select('COUNT(*)')->where('hidden', 0)->where('pass', '')->where_in('type', $types);
            }


            $count_row    = $this->db->get('files')->row_array();
            $count_result = $count_row['COUNT(*)']; //подчет суммы результатов поиска

            if ($count_result <> 0)
            {
                if ($this->input->get('q'))
                {
                    $this->db->where($like);
                }
                if ($this->input->get('user'))
                {
                    $login = $this->input->get('user');
                    $this->db->where("id_user in (select id_user from md_users where login='$login')");
                }
                if (isset($user['id_user']) and ($user['id_user'] == $this->session->userdata('id_user')))
                {
                    $this->db->select('id,filename,type,size,date_upload,counter_downloads,counter_comments,id_user')->from('files')->where_in('type', $types)->order_by($sorting, $asc_desc)->limit(10, $page);
                }
                else
                {
                    $this->db->select('id,filename,type,size,date_upload,counter_downloads,counter_comments,id_user')->from('files')->where('hidden', 0)->where('pass', '')->where_in('type', $types)->order_by($sorting, $asc_desc)->limit(10, $page);
                }
                $query                       = $this->db->get();
                $search_form['string_array'] = $query->result_array();
                $url                         = explode('&', $this->input->server('QUERY_STRING')); //разбираем строку запроса  get  на отдельные сегменты(переменная=значение)

                foreach ($url as $key => $var_val)
                {
                    if (preg_match('/^page=/', $var_val)) //удаляем переменную  page  из массива
                    {
                        unset($url[$key]);
                    }
                }
                $url = implode('&', $url); //заново собираем строку запроса без переменных  page

                $this->load->library('pagination');
                $pagination_config['query_string_segment'] = "page";
                $pagination_config['base_url']             = "search?" . $url;
                $pagination_config['total_rows']           = $count_result;
                $pagination_config['per_page']             = '10';
                $pagination_config['page_query_string']    = true;
                $this->pagination->initialize($pagination_config);
                $search_form['pagination_links'] = $this->pagination->create_links();
                $this->load->view('files/search_view', $search_form);
            }
            else
            {
                $this->load->view('files/search_view', $search_form);
            }

        }
        else
        {
            $this->load->view('files/search_view', $search_form);
        }

    }
}
?>