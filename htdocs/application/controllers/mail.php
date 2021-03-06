<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mail extends CI_Controller
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

    public function index($action = "inbox", $id = 0)
    {
        $this->output->enable_profiler(TRUE);
        $this->login_control();
        if ($this->input->post('text') or $this->input->post('subject'))
        {
            if (strlen($this->input->post('subject')) > 200)
            {
                $error_msg[] = "Максимальная длина темы 200 символов";
            }
            if (strlen($this->input->post('text')) > 10000)
            {
                $error_msg[] = "Максимальная длина сообщения 10000 символов";
            }
            if (isset($error_msg))
            {
                $this->session->set_flashdata('info', json_encode($error_msg));
                header("Location: /user/$user");
                die();
            }
            else
            {
                $data = array(
                    'user_from' => $this->session->userdata('id_user'),
                    'user_to' => $this->input->post('to'),
                    'subject' => htmlspecialchars($this->input->post('subject')),
                    'read' => 1,
                    'text' => htmlspecialchars($this->input->post('text'))
                );
                $this->db->insert('messages', $data);
                $data = array(
                    'user_from' => $this->session->userdata('id_user'),
                    'user_to' => $this->input->post('to'),
                    'subject' => htmlspecialchars($this->input->post('subject')),
                    'text' => htmlspecialchars($this->input->post('text')),
                    'folder' => 'inbox'
                );
                $this->db->insert('messages', $data);
                $info_msg[] = "Сообщение отправлено";
                $this->session->set_flashdata('info', json_encode($info_msg));
                header("Location: " . $this->input->get('redirect'));
                die();
            }
        }
        if ($this->input->post('delete') or $this->input->post('markread'))
        {
            if (!is_array($this->input->post('ids')))
            {
                $error_msg[] = "Отметьте галочками сообщения для работы с ними";
                $this->session->set_flashdata('info', json_encode($error_msg));
                header("Location: " . $_GET['redirect']);
                die();
            }
            $where = '(`user_to`=".$this->session->userdata(`id_user`)." AND `folder`=`inbox`) OR (`user_from`=".$this->session->userdata(`id_user`)". AND `folder`=`outbox`)';
            $this->db->where_in('id', $this->input->post('ids'));
            if ($this->input->post('markread'))
            {
                $this->db->update('messages', array(
                    'read' => 1
                ));
                $msg_ok[] = "Выбранные сообщения помечены как прочитанные";
                $this->session->set_flashdata('info', json_encode($msg_ok));
                header("Location: $_GET[redirect]");
                die();
            }
            if ($this->input->post('delete'))
            {
                $this->db->delete('messages');
                $info_msg[] = "Выбранные сообщения удалены";
                $this->session->set_flashdata('info', json_encode($info_msg));
                header("Location: /mail");
                die();
            }
        }
        else
        {
            if ($action == "sent")
            {
                $this->db->order_by("id", "DESC");
                $this->db->where('user_from', $this->session->userdata('id_user'));
                $this->db->where('folder', 'outbox');
                $data['inbox'] = $this->db->get('messages')->result_array();
                $this->load->view("mail/mail_page_view", $data);
            }
            if ($action == "inbox")
            {
                $this->db->order_by("id", "DESC");
                $this->db->where('user_to', $this->session->userdata('id_user'));
                $this->db->where('folder', 'inbox');
                $data['inbox'] = $this->db->get('messages')->result_array();
                $this->load->view("mail/mail_page_view", $data);
            }
        }
    }

    public function read($id = 0)
    {
        $this->output->enable_profiler(TRUE);
        $this->login_control();
        $this->db->where('id', $id);
        $this->load->helper('userdata');
        $data = $this->db->get('messages')->row_array();
        if (count($data) == 0)
        {
            show_404();
        }
        else
        {
            if ((($data['user_from'] == $this->session->userdata('id_user')) and ($data['folder'] == "outbox")) or (($data['user_to'] == $this->session->userdata('id_user')) and ($data['folder'] == "inbox")))
            {
                $this->db->where('id', $id);
                $this->db->update('messages', array('read' => 1));
                $data['loginUserFrom']=$this->session->userdata('login');
                $data['loginUserTo']=idToLogin($data['user_to']);
                $this->load->view('mail/mail_read_view', $data);
            }
            else
            {
                show_error("<b>Forbidden 403</b>", 403);
            }
        }

    }

    public function remove($id = 0)
    {
        $this->output->enable_profiler(TRUE);
        $this->login_control();
        $this->db->where('id', $id);
        $data = $this->db->get('messages')->row_array();
        if (count($data) == 0)
        {
            show_404();
        }
        else
        {
            if ((($data['user_from'] == $this->session->userdata('id_user')) and ($data['folder'] == "outbox")) or (($data['user_to'] == $this->session->userdata('id_user')) and ($data['folder'] == "inbox")))
            {
                $this->db->delete('messages', array(
                    'id' => $id
                ));
                $info_msg[] = "Сообщение удалено";
                $this->session->set_flashdata('info', json_encode($info_msg));
                if ($this->input->get('redirect'))
                {
                    header("Location: " . $_GET['redirect']);
                    die();
                }
                header("Location: /mail");
                die();
            }
            else
            {
                show_error("<b>Forbidden 403</b>", 403);
            }
        }

    }
    public function markread($id = 0)
    {
        $this->output->enable_profiler(TRUE);
        $this->login_control();
        $this->db->where('id', $id);
        $this->db->where('folder', 'inbox');
        $data = $this->db->get('messages')->row_array();
        if (count($data) == 0)
        {
            show_404();
        }
        else
        {
            if ($data['user_to'] == $this->session->userdata('id_user'))
            {
                $this->db->where('id', $id);
                $this->db->update('messages', array(
                    'read' => 1
                ));
                $error_msg[] = "Помечено прочитанным";
                $this->session->set_flashdata('info', json_encode($error_msg));
                if ($this->input->get('redirect'))
                {
                    header("Location: " . $_GET['redirect']);
                    die();
                }
                header("Location: /mail");
                die();
            }
            else
            {
                show_error("<b>Forbidden 403</b>", 403);
            }
        }

    }

    public function search()
    {
        $this->output->enable_profiler(true);
        $this->login_control();
        $this->load->helper('userdata');
        if ($this->input->get())
        {
            if (isset($_GET['q']) and isset($_GET['folder']))
            {
                if ($_GET['q'] == null)
                {
                    $error_msg[] = "Пустой поисковой запрос";
                    $this->session->set_flashdata('info', json_encode($error_msg));
                    header("Location: /mail/search");
                    die();
                }
                $q = $this->db->escape_like_str(htmlspecialchars($this->input->get('q')));
                $this->db->where("(`subject` LIKE '%" . $q . "%' OR `text` LIKE '%" . $q . "%')");
                if ($this->input->get('folder') == "all")
                {
                    $this->db->where("((`user_from` = '" . $this->session->userdata('id_user') . "' AND `folder`='outbox') OR (`user_to` = '" . $this->session->userdata('id_user') . "') AND `folder`='inbox')");
                }
                elseif ($this->input->get('folder') == "inbox")
                {
                    $this->db->where('user_to', $this->session->userdata('id_user'));
                }
                elseif ($this->input->get('folder') == "outbox")
                {
                    $this->db->where('user_from', $this->session->userdata('id_user'));
                }
                else
                {
                    show_error('<b>400 Bad request</b>', 400);
                }
                if ($this->input->get('from'))
                {

                    $this->db->where('user_from',loginToId($this->input->get('from')));
                }
                if ($this->input->get('to'))
                {
                    $this->db->where('user_to', loginToId($this->input->get('to')));
                }
                $query          = $this->db->get('messages')->result_array();
                $data['result'] = $query;
                $this->load->view('mail/mail_search_view', $data);
            }
            else
            {
                show_error('<b>400 Bad request</b>', 400);
            }
        }
        else
        {
            $this->load->view('mail/mail_search_view');
        }

    }
}
?>