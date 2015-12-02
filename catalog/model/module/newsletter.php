<?php
class ModelModuleNewsletter extends Model
{
    public function createNewslettersubscribe()
    {
        
        $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "newsletter_subscribe'");
        $rows =$query->num_rows;
		if ($rows == 0) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "newsletter_subscribe`(`newsletter_id` int(11) NOT NULL AUTO_INCREMENT,`newsletter_email` varchar(255) NOT NULL,PRIMARY KEY (`newsletter_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");
        }
        
        
    }
    
    public function NewsletterSubscribes($data)
    {
        
        $res = $this->db->query("select * from " . DB_PREFIX . "newsletter_subscribe where newsletter_email='" . $data['email'] . "'");
        
        if ($res->num_rows == 1) {
            return "Email Already Exist";
        } else {
            $this->db->query("INSERT INTO " . DB_PREFIX . "newsletter_subscribe(newsletter_email) values ('" . $data['email'] . "')");
            return "Subscription Success";
        }
        
    }
    
    
    public function NewsletterDelete($data)
    {
        
        $this->db->query("DELETE FROM " . DB_PREFIX . "newsletter_subscribe WHERE newsletter_email = '" . $data['email'] . "'");
        
    }
    
}