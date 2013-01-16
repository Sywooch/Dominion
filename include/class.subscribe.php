<?php
class Suscribe{
  private $email_from;
  private $subscribe_limit;
  
  private $send_email_from;
  private $send_email_from_name;
  
  private $Subscribe;
  
  function __construct(){
    Zend_Loader::loadClass('models_Subscribe');
    
    $this->Subscribe = new models_Subscribe();
    
    $this->email_from = $this->Subscribe->get_settings('subscribe_email_from');
    $this->subscribe_limit = $this->Subscribe->get_settings('subscribe_limit');   
    
    $patrern='/(.*)<(.*)>/Uis';
    preg_match_all($patrern, $this->email_from, $arr);
    
    $this->send_email_from     = trim($arr[2][0]);
    $this->send_email_from_name = trim($arr[1][0]);
  }
  
  public function run(){
    $subscribe_tasks = $this->Subscribe->get_subscribe_tasks();
    if(!empty($subscribe_tasks)){
      foreach($subscribe_tasks as $view){
        $message = strtolower($view['TEXT']);
        $subject = $view['NAME'];
        
        $user_count = 0;
        $subscribe_users = $this->Subscribe->get_subscribe_users($view['MESSAGES_ID'], $this->subscribe_limit);
        if(!empty($subscribe_users)){
          foreach($subscribe_users as $sbu){
            
            if(!empty($sbu['EMAIL'])){
              if(!empty($sbu['NAME']))
                $_message = str_replace('##name##', $sbu['NAME'], $message);
              else
                $_message = str_replace('##name##', '', $message);
                
              $result = $this->sendMail($sbu['EMAIL'], $_message, $subject);
              if($result==1){      
                $this->Subscribe->update_user($view['MESSAGES_ID'], $sbu['USER_ID']);
                $user_count++;
                echo $user_count." = Was sended to ".$sbu['EMAIL']." Result: {$result}<br />\r\n";
              }
            }
          }      
        }
        
        if(empty($user_count)){
          $this->Subscribe->update_messages($view['MESSAGES_ID']);
        }
      }      
    }
    
  }
  
  
  
  public function sendMail($to,$message,$subject,$attach='',$name=''){
    Zend_Loader::loadClass('Zend_Mail');
    
    $patrern = '/(.*)<?([a-zA-Z0-9\-\_]+\@[a-zA-Z0-9\-\_]+(\.[a-zA-Z0-9]+?)+?)>?/U';
    preg_match_all($patrern, $this->email_from, $arr);
    
    $mailerFrom = empty($arr[2][0])? '':trim($arr[2][0]);
    $mailerFromName = empty($arr[1][0])? '':trim($arr[1][0]);
    
    $mailer = new Zend_Mail('utf-8');
    
    $mailer->setFrom($mailerFrom,$mailerFromName);
    $mailer->setSubject($subject);
    $mailer->addTo($to);
    $mailer->setBodyHtml($message,'UTF-8');
    
    if(!empty($attach)){      
      $logo = new Zend_Mime_part(file_get_contents($attach));
      $logo->type = 'application/octet-stream';  
      $logo->disposition = Zend_Mime::DISPOSITION_INLINE;  
      $logo->encoding = Zend_Mime::ENCODING_BASE64;  
      $logo->filename = $name;
      $logo->id = '123456789';  
        
      $at = $mailer->addAttachment($logo);
    } 
    $mailer->send();
    
    return 1;
  }
}
?>