<?php
class ControllerModuleNewsletter extends Controller
{
    private $error = array();   
    public function index()
    {
        $this->load->language('module/newsletter');
        $this->document->setTitle($this->language->get('Newsletter Subscribers'));
        $this->load->language('catalog/category');
        $this->load->model('common/newsletter');
        $this->model_common_newsletter->createNewslettersubscribe();
        $this->getList();
    }
    
    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'module/newsletter')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }
    
    public function install()
    {
        $this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->user->getId(), 'access', 'module/newsletter');
        $this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'module/newsletter');        
    }
    
    public function NewsletterDelete()
    {
        
        $this->load->model('common/newsletter');
        $this->load->language('module/newsletter');
        $this->model_common_newsletter->NewsletterDelete($this->request->get['newsletter_email']);
        $this->session->data['success'] = $this->language->get('text_delete');
        $this->response->redirect($this->url->link('module/newsletter', 'token=' . $this->session->data['token'], 'SSL'));
        
    }
    
    
    public function NewsletterComplete()
    {
        
        $this->load->model('common/newsletter');
        $this->load->language('module/newsletter');
        
        $result     = $this->model_common_newsletter->NewsletterComplete();
        $fullPath   = 'email.csv';
        $fp         = fopen($fullPath, 'w');
        $csv_file   = array();
        $csv_file[] = 'Email';
        fputcsv($fp, $csv_file);
        
        foreach ($result as $result) {
            fputcsv($fp, $result);
        }
        fclose($fp);
        
        
        if (headers_sent())
            die('Headers Sent');
        
        // Required for some browsers
        if (ini_get('zlib.output_compression'))
            ini_set('zlib.output_compression', 'Off');
        
        // File Exists?
        if (file_exists($fullPath)) {
            
            // Parse Info / Get Extension
            $fsize      = filesize($fullPath);
            $path_parts = pathinfo($fullPath);
            $ext        = strtolower($path_parts["extension"]);
            
            // Determine Content Type
            switch ($ext) {
                case "pdf":
                    $ctype = "application/pdf";
                    break;
                case "exe":
                    $ctype = "application/octet-stream";
                    break;
                case "zip":
                    $ctype = "application/zip";
                    break;
                case "doc":
                    $ctype = "application/msword";
                    break;
                case "xls":
                    $ctype = "application/vnd.ms-excel";
                    break;
                case "ppt":
                    $ctype = "application/vnd.ms-powerpoint";
                    break;
                case "gif":
                    $ctype = "image/gif";
                    break;
                case "png":
                    $ctype = "image/png";
                    break;
                case "jpeg":
                case "jpg":
                    $ctype = "image/jpg";
                    break;
                default:
                    $ctype = "application/force-download";
            }
            
            header("Pragma: public"); // required
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false); // required for certain browsers
            header("Content-Type: $ctype");
            header("Content-Disposition: attachment; filename=\"" . basename($fullPath) . "\";");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . $fsize);
            ob_clean();
            flush();
            readfile($fullPath);
            
        } else {
            die('File Not Found');
            
        }
        
        
        $this->response->redirect($this->url->link('module/newsletter', 'token=' . $this->session->data['token'], 'SSL'));
        
    }
    
    public function NewsletterAdd()
    {
        
        $this->load->language('module/newsletter');
        $data['newsletter_title'] = $this->language->get('newsletter_title');
        $this->getForm();
        
    }
    
    protected function getForm()
    {
        
        $this->load->language('catalog/category');
        $data['heading_title'] = $this->language->get('heading_title');
        $this->load->language('module/newsletter');
        $data['newsletter_title'] = $this->language->get('newsletter_title');
        $data['subscribe']        = $this->language->get('subscribe');
        $data['email']            = $this->language->get('email');
        $data['text_form']        = $this->language->get('text_form');
        $data['text_default']     = $this->language->get('text_default');
        $data['text_enabled']     = $this->language->get('text_enabled');
        $data['text_disabled']    = $this->language->get('text_disabled');
        $data['text_unallocated'] = $this->language->get('text_unallocated');
        
        $data['entry_title']       = $this->language->get('entry_title');
        $data['entry_description'] = $this->language->get('entry_description');
        $data['entry_status']      = $this->language->get('entry_status');
        $data['entry_image']       = $this->language->get('entry_image');
        $data['entry_module']      = $this->language->get('entry_module');
        
        $data['button_save']   = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        
        $data['tab_general'] = $this->language->get('tab_general');
        $data['tab_data']    = $this->language->get('tab_data');
        
        
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
        
        if (isset($this->error['title'])) {
            $data['error_title'] = $this->error['title'];
        } else {
            $data['error_title'] = array();
        }
        
        if (isset($this->error['description'])) {
            $data['error_description'] = $this->error['description'];
        } else {
            $data['error_description'] = array();
        }
        
        if (isset($this->error['module'])) {
            $data['error_module'] = $this->error['module'];
        } else {
            $data['error_module'] = '';
        }
        
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        
        $data['breadcrumbs'] = array();
        
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
        );
        
        
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('module/newsletter', 'token=' . $this->session->data['token'], 'SSL')
        );
        
        $data['cancel'] = $this->url->link('module/newsletter', 'token=' . $this->session->data['token'], 'SSL');
        $data['save']   = $this->url->link('module/newsletter/newssubscribe', 'token=' . $this->session->data['token'], 'SSL');
        $data['action'] = $this->url->link('module/newsletter/newssubscribe', 'token=' . $this->session->data['token'], 'SSL');
        
        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');
        
        $this->response->setOutput($this->load->view('module/newsletter/form.tpl', $data));
    }
    
    public function newssubscribe()
    {
        
        $this->load->model('common/newsletter');
        $this->load->language('module/newsletter');
        $data['text_success_already'] = $this->language->get('text_success_already');
        $data['text_success_add']     = $this->language->get('text_success_add');
        $this->document->setTitle($this->language->get('Newsletter Subscribers'));
        $data['newsletter_title'] = $this->language->get('newsletter_title');
        $datamail                 = array();
        $datamail['mail']         = $this->model_common_newsletter->NewsletterSubscribes($this->request->post);
        if ($datamail['mail'] == "Email Already Exist") {
            $this->error['warning'] = $this->language->get('text_success_already');
        } else {
            $this->session->data['success'] = $this->language->get('text_success_add');
        }
        
        $this->getForm();
        
    }
    protected function getList()
    {
        
        $this->load->language('module/newsletter');
        
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'name';
        }
        
        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }
        
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }
        
        $url = '';
        
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }
        
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        
        $data['newsletter_title']  = $this->language->get('newsletter_title');
        $data['text_list']         = $this->language->get('text_list');
        $data['text_no_results']   = $this->language->get('text_no_results');
        $data['text_confirm']      = $this->language->get('text_confirm');
        $data['column_name']       = $this->language->get('column_name');
        $data['column_sort_order'] = $this->language->get('column_sort_order');
        $data['column_action']     = $this->language->get('column_action');
        $data['button_add']        = $this->language->get('button_add');
        $data['button_edit']       = $this->language->get('button_edit');
        $data['button_delete']     = $this->language->get('button_delete');
        $data['button_rebuild']    = $this->language->get('button_rebuild');
        $data['button_import']     = $this->language->get('button_import');
        $data['completemail']      = $this->url->link('module/newsletter/NewsletterComplete', 'token=' . $this->session->data['token'], 'SSL');
        $data['addemail']          = $this->url->link('module/newsletter/NewsletterAdd', 'token=' . $this->session->data['token'], 'SSL');
        $data['newrestore']        = $this->url->link('module/newsletter/NewsletterImport', 'token=' . $this->session->data['token'], 'SSL');
        
        $data['newsletter_title'] = $this->language->get('newsletter_title');
        $data['text_edit']        = $this->language->get('text_edit');
        $data['text_enabled']     = $this->language->get('text_enabled');
        $data['text_disabled']    = $this->language->get('text_disabled');
        $data['entry_status']     = $this->language->get('entry_status');
        $data['button_save']      = $this->language->get('button_save');
        $data['button_cancel']    = $this->language->get('button_cancel');
        $data['action']           = $this->url->link('module/newsletter', 'token=' . $this->session->data['token'], 'SSL');
        $data['cancel']           = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
        $this->load->model('setting/setting');
        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('newsletter', $this->request->post);
            
            $this->session->data['success'] = $this->language->get('text_success');
            
            $this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
        }
        
        
        if (isset($this->request->post['category_status'])) {
            $data['newsletter_status'] = $this->request->post['newsletter_status'];
        } else {
            $data['newsletter_status'] = $this->config->get('newsletter_status');
        }
        
        
        $data['breadcrumbs']   = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('newsletter_title'),
            'href' => $this->url->link('module/newsletter', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );
        
        $filter_data = array(
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );
        
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
        
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        
        
        $this->load->model('common/newsletter');
        $newsletter_total = $this->model_common_newsletter->getTotalNewsLetter();
        $result           = $this->model_common_newsletter->getNewsLetter($filter_data);
        $data['newsltr']  = array();
        
        foreach ($result as $result) {
            $data['newsltr'][] = array(
                'newsletter_id' => $result['newsletter_id'],
                'newsletter_email' => $result['newsletter_email'],
                'delete' => $this->url->link('module/newsletter/NewsletterDelete', 'token=' . $this->session->data['token'] . '&newsletter_email=' . $result['newsletter_email'], 'SSL')
                
            );
        }
        
        $pagination        = new Pagination();
        $pagination->total = $newsletter_total;
        $pagination->page  = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url   = $this->url->link('module/newsletter', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
        
        $data['pagination'] = $pagination->render();
        $data['results']    = sprintf($this->language->get('text_pagination'), ($newsletter_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($newsletter_total - $this->config->get('config_limit_admin'))) ? $newsletter_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $newsletter_total, ceil($newsletter_total / $this->config->get('config_limit_admin')));
        
        $data['sort']  = $sort;
        $data['order'] = $order;
        
        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');
        
        $this->response->setOutput($this->load->view('module/newsletter.tpl', $data));
    }
    
    
    public function NewsletterImport()
    {
        $this->load->language('module/newsletter');
        
        $this->document->setTitle($this->language->get('heading_title'));
        
        $this->load->model('common/newsletter');
        $uploadmail = array();
        $i          = 0;
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            if (is_uploaded_file($this->request->files['import']['tmp_name'])) {
                $handle = fopen($_FILES['import']['tmp_name'], "r");
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $uploadmail['email'] = $data[0];
                    
                    if ($i != 0) {
                        $this->model_common_newsletter->NewsletterSubscribes($uploadmail);
                    }
                    $i++;
                }
                
                $this->session->data['success'] = $this->language->get('text_success');
                $this->response->redirect($this->url->link('module/newsletter', 'token=' . $this->session->data['token'], 'SSL'));
            } else {
                $this->error['warning'] = $this->language->get('error_empty');
            }
            
        }
        
        $data['heading_title'] = $this->language->get('heading_title');
        
        $data['text_select_all']   = $this->language->get('text_select_all');
        $data['text_unselect_all'] = $this->language->get('text_unselect_all');
        
        $data['entry_restore'] = $this->language->get('entry_restore');
        $data['entry_backup']  = $this->language->get('entry_backup');
        
        $data['button_backup']  = $this->language->get('button_backup');
        $data['button_restore'] = $this->language->get('button_restore');
        
        if (isset($this->session->data['error'])) {
            $data['error_warning'] = $this->session->data['error'];
            
            unset($this->session->data['error']);
        } elseif (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
        
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        
        $data['breadcrumbs'] = array();
        
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('module/newsletter', 'token=' . $this->session->data['token'], 'SSL')
        );
        
        $data['cancel']        = $this->url->link('module/newsletter', 'token=' . $this->session->data['token'], 'SSL');
        $data['action']        = $this->url->link('module/newsletter/NewsletterImport', 'token=' . $this->session->data['token'], 'SSL');
        $data['button_save']   = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
      
        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');
        
        $this->response->setOutput($this->load->view('module/newsletter/backupmail.tpl', $data));
    }
    
    
}
