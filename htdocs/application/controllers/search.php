<?php
class Search extends CI_Controller
{
    public function index($user = 0)
    {
        $this->output->enable_profiler(TRUE);
        if (!($this->session->userdata('id_user')))
        {
            $this->session->set_userdata('redirect', $_SERVER['REQUEST_URI']);
            $info_msg['error_msg'] = "Войдите чтобы просматривать эту страницу";
            $this->load->view('/index_page/loginpage_view', $info_msg);
        }
        else
        {
            if ($this->input->get('q') and $this->input->get('type_search'))
            {
                if ($this->input->get('type_search') == "files")
                {
                    header("Location: /files/search?q=" . $this->input->get('q') . "&audio=1&video=1&img=1&doc=1&archive=1&other=1");
                    die();
                }
                elseif ($this->input->get('type_search') == "audio")
                {
                    echo "В разработке";
                }
                elseif ($this->input->get('type_search') == "video")
                {
                    header("Location: /video/search?q=" . $this->input->get('q'));
                    die();
                }
                elseif ($this->input->get('type_search') == "mail")
                {
                    header("Location: /mail/search?q=" . $this->input->get('q') . "&folder=all");
                    die();
                }
                else
                {
                    show_error("<b>NOT FOUND 404</b>", 404);
                }
            }
        }
    }
}
?>