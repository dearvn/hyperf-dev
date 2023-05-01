<?php

declare(strict_types=1);

namespace App\Foundation\Utils;

use App\Exception\Handler\BusinessException;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Email tool
 * Class Mail
 * @package App\Foundation\Utils
 * @Author YiYuan-Lin
 * @Date: 2021/8/17
 */
class Mail
{
    /**
     * Email class instance
     * @var
     */
    static private $mail;

    /**
     * Mail class object
     * @var
     */
    static public $obj  = null;

    /**
    *Initialize the mail tool class
    *@param array $config
    *config['char_set'] string mail encoding
    *config['smtp_debug'] bool Whether to output in debug mode
    *config['is_html'] bool whether HTML output
    *config['host'] string SMTP server
    *config['port'] int server port
    *config['smtp_auth'] bool allow SMTP authentication
    *config['username'] string SMTP username
    *config['password'] string SMTP password
    *config['smtp_secure'] tls/ssl allow TLS or ssl protocol
    *@return Mail|null
    */
    public static function init(array $config = [])
    {
        self::$mail = new PHPMailer(true);
        self::__setConfig($config);
        if (!is_object(self::$obj)) self::$obj = new self;
        return self::$obj;
    }

    /**
     * Setting parameters
     * @param array $config
     */
    private static function __setConfig(array $config)
    {
        //Use SMTP
        self::$mail->isSMTP();
        //Set mail encoding
        self::$mail->CharSet = $config['char_set'] ?? "UTF-8";
        //Debug mode output
        self::$mail->SMTPDebug = $config['smtp_debug'] ?? 0;
        //Whether to output HTML
        self::$mail->isHTML($config['is_html'] ?? true);
        //SMTP server
        self::$mail->Host = $config['host'] ?? env('MAIL_SMTP_HOST', 'smtp.163.com');
        //Server port 25 or 465 depends on the mailbox server support
        self::$mail->Port = $config['port'] ?? env('MAIL_SMTP_PORT', 465);
        //Allow SMTP certification
        self::$mail->SMTPAuth = $config['smtp_auth'] ?? true;
        //SMTP user name
        self::$mail->Username = $config['username'] ?? env('MAIL_SMTP_USERNAME', 'SMTP用户名');
        //Some mailboxes of the SMTP password are authorized codes (eg 163 mailboxes)
        self::$mail->Password = $config['password'] ?? env('MAIL_SMTP_PASSWORD', '密码或者授权码');
        //Allow TLS or SSL protocol
        self::$mail->SMTPSecure = $config['smtp_secure'] ?? env('MAIL_SMTP_ENCRYPTION', 'ssl');
    }

    /**
     * Set the sender
     * @param string $email
     * @param string $name
     * @return mixed
     */
    public function setFromAddress(string $email, string $name) : self
    {
        self::$mail->setFrom($email, $name);
        return $this;
    }

    /**
     * Set the recipient information
     * @param string $email
     * @param string $name
     * @return self
     */
    public function setAddress(string $email, string $name) : self
    {
        self::$mail->addAddress($email, $name);
        return $this;
    }

    /**
     * Set the recipient information (multi -recipient)
     * @param array $emailInfo
     * @return self
     */
    public function setMoreAddress(array $emailInfo) : self
    {
        foreach ($emailInfo as $item) {
            self::$mail->addAddress($item['email'], $item['name']);
        }
        return $this;
    }

    /**
     * Copy
     * @param string $email
     * @return self
     */
    public function addCC(string $email) : self
    {
        self::$mail->addCC($email);
        return $this;
    }

    /**
     * Secret delivery
     * @param string $email
     * @return self
     */
    public function addBCC(string $email) : self
    {
        self::$mail->addBCC($email);
        return $this;
    }

    /**
     * Add attachments
     * @param string $url
     * @param string $fileName
     * @return self
     */
    public function addAttachment(string $url, string $fileName = '') : self
    {
        self::$mail->addAttachment($url, $fileName);
        return $this;
    }

    /**
     * Set the email title
     * @param string $title
     * @return self
     */
    public function setSubject(string $title) : self
    {
        self::$mail->Subject = $title;
        return $this;
    }

    /**
     * Set mail content
     * @param string $body
     * @return self
     */
    public function setBody(string $body) : self
    {
        self::$mail->Body = $body;
        return $this;
    }

    /**
     * send email
     * @return bool
     */
    public function send()
    {
        try {
            if (self::$mail->send()) return true;
            return false;
        }catch (\Exception $e) {
            Throw new BusinessException(400, $e->getMessage());
        }
    }
}
