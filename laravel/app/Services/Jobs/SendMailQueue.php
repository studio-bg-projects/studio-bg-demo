<?php

namespace App\Services\Jobs;

use App\Models\Mail;

class SendMailQueue extends BaseSyncJob
{
  public function run(): void
  {
    /* @var $mails Mail[] */
    $mails = Mail::whereNull('sentDate')->get();
    foreach ($mails as $mail) {
      $content = $mail->content;

      if ($mail->addHtmlWrapper) {
        $content = (string)view('layouts.mail', [
          'lang' => $mail->lang,
          'content' => $content,
        ]);
      }

      $this->send($mail->to, $mail->subject, $content);
      $this->out(sprintf('Sent mail to: %s with subject: %s', $mail->to, $mail->subject));

      $mail->sentDate = new \DateTime();
      $mail->save();
    }

    $this->out('All good :)');
  }

  protected function send($to, $subject, $content, $priority = 5)
  {
    $url = env('MAIL_API_SERVICE');

    $parameters = [
      'email' => $to,
      'subject' => $subject,
      'content' => $content,
      'priority' => $priority
    ];

    $ch = curl_init();
    curl_setopt($ch, \CURLOPT_URL, $url);
    curl_setopt($ch, \CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, \CURLOPT_POSTFIELDS, http_build_query($parameters));
    curl_setopt($ch, \CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, \CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, \CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
    $rs = curl_exec($ch);
    curl_close($ch);

    return json_decode($rs, true);
  }
}
