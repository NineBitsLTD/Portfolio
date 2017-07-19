<?php

namespace Config;
/**
 * Перед использованием аккаунта гугл
 * 
 * Разрешить ненадежным приложениям использовать гугл
 * https://myaccount.google.com/u/0/lesssecureapps?pli=1
 * 
 * 
 */
class Mailer
{
    public $Host = 'smtp.gmail.com';
    public $SmtpAuth = true;
    public $Username = 'phpacademy.test2017';
    public $Password = 'test2017';
    public $Port = '465';
    public $SmtpSecure = 'ssl';
    public $Charset = 'utf-8';
    public $From = 'phpacademy.test2017@gmail.com';
    public $FromName = 'Test project - Portfolio';

    public function Send($to, $toName, $subject, $body, $attachment=null, $from=null, $fromName=null){
        $mail = new \PHPMailer();
        //$mail->SMTPDebug=2;
        $mail->isSMTP();
        $mail->Host = $this->Host;
        $mail->SMTPAuth = $this->SmtpAuth;
        $mail->Username = $this->Username;
        $mail->Password = $this->Password;
        $mail->Port = $this->Port;
        $mail->SMTPSecure = $this->SmtpSecure;
        $mail->CharSet = $this->Charset;
        $mail->From = $this->From;
        $mail->FromName = $this->FromName;
        
        $mail->addAddress ($to, $toName);
        if(isset($attachment)){
            if(is_array($attachment)){
                foreach ($attachment as $key => $value) {
                    $mail->addAttachment($value, ((int)$key!=$key)?$key:'');
                }
            } else {
                $mail->addAttachment($attachment);
            }
        }
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        if(!$mail->send()) return $mail->ErrorInfo;
        else return true;
    }
}

