<?php
  class App_Mail{
    
    /*
    * @params to - send email
    *         message - mail message
    *         subject - mail subject
    *         attach - link file source
    *         name - file name
    *         attach_type - file type
    */
    static function send($params){
      Zend_Loader::loadClass('Zend_Mail');
      
      $mailer = new Zend_Mail('utf-8');
      
      $mailer->setFrom($params['mailerFrom'], $params['mailerFromName']);
      $mailer->setSubject($params['subject']);
      $mailer->addTo($params['to']);
      $mailer->setBodyHtml($params['message'],'UTF-8',Zend_Mime::ENCODING_BASE64);
      
      if(!empty($params['attach'])){        
        $logo = new Zend_Mime_part(file_get_contents($params['attach']));
        $logo->type = $params['attach_type'];  
        $logo->disposition = Zend_Mime::DISPOSITION_INLINE;  
        $logo->encoding = Zend_Mime::ENCODING_BASE64;  
        $logo->filename = $params['name'];
          
        $at = $mailer->addAttachment($logo);
      } 
      try{
        $mailer->send();  
      }
      catch(Exception $ex){
        echo "Ошибка отправки электронного письма на ящик ".$params['to'];
        exit; 
      }
    }
  }
?>
