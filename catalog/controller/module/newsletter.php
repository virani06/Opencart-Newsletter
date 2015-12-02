<?php
class ControllerModuleNewsletter extends Controller
{
    public function index()
    {
        $this->load->language('module/newsletter');
        $this->load->model('module/newsletter');
        
        $this->model_module_newsletter->createNewslettersubscribe();
        $data['newsletter_title'] = $this->language->get('newsletter_title');
		$data['newsletter_placeholder'] = $this->language->get('newsletter_placeholder');
        $data['email']            = $this->language->get('email');
        $data['subscribe']        = $this->language->get('subscribe');
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/newsletter.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/module/newsletter.tpl', $data);
        } else {
            return $this->load->view('default/template/module/newsletter.tpl', $data);
        }
    }
    
    public function jsonnewsletter()
    {
		$this->load->language('module/newsletter');
        $this->load->model('module/newsletter');
        $json                  = array();
        $json['messagestatus'] = $this->model_module_newsletter->NewsletterSubscribes($this->request->post);
        $data['text_success_already']            = $this->language->get('text_success_already');
        $data['text_success_add']        = $this->language->get('text_success_add');
        if ($json['messagestatus'] == 'Email Already Exist') {
            $json['fail'] = $data['text_success_already'];
        } else {
            $json['success'] = $data['text_success_add'];
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        
    }
    
}
