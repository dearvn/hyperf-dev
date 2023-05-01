<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/mail.
 *
 * @link     https://github.com/hyperf-ext/mail
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/mail/blob/master/LICENSE
 */
namespace App\Mail;

use HyperfExt\Contract\ShouldQueue;
use HyperfExt\Mail\Mailable;

class VersionUpdate extends Mailable implements ShouldQueue
{
    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     */
    public function build()
    {
        //Mailbox activation template
        $html1 = <<<ht
    <p>Hi，<em style="font-weight: 700;">Hello</em>，Please click the link below to activate your account number</p>
    <a href="https://blog.zongscan.com?activate=">Act immediately</a>
ht;
        return $this->subject('ZONGSCAN-Account registration activation link')->htmlBody($html1);
    }
}
