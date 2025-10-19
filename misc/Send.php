<?php

namespace App\Backgrounds\Controllers;

class Send
{
  private static $killSignals = [
    \SIGTERM,
    \SIGINT,
    \SIGQUIT,
    \SIGHUP
  ];

  private static $killMe = false;

  public static function index()
  {
    // Register signal handler
    declare(ticks=1);
    foreach (self::$killSignals as $signal) {
      pcntl_signal($signal, __CLASS__ . '::setKill');
    }

    // Do it
    while (true) {
      self::killMe();

      usleep(rand(500000, 1000000)); // 0.5sec, 1sec

      self::doit();
    }
  }

  public static function setKill()
  {
    d('SET KILL');
    self::$killMe = true;
  }

  private static function killMe()
  {
    if (self::$killMe) {
      d('KILL ME');
      exit();
    }
  }

  public static function doit()
  {
    d('SEND MAILS');

    foreach (\App\Main\Models\Queue::getList(10) as $row) {
      self::killMe();

      $config = self::getConfig($row['configKey']);

      if (!$config) {
        $sentErrors = 'There is no config record for key ' . $row['key'];
        $isSent = false;
      } else {
        $status = self::send($config, $row['email'], $row['subject'], $row['content']);

        if ($status === true) {
          $isSent = true;
          $sentErrors = '';
        } else {
          $isSent = false;
          $sentErrors = $status;
        }
      }

      \App\Main\Models\Queue::set($row['id'], [
        'sentTimestamp' => date('Y-m-d H:i:s'),
        'isSent' => $isSent,
        'sentErrors' => $sentErrors
      ]);

      if ($sentErrors) {
        d($sentErrors);
      }
    }
  }

  private static function send(array $config, $email, $subject, $content)
  {
    if (!empty($config['mailGun'])) {
      return self::sendViaMailGun($config, $email, $subject, $content);
    } elseif (!empty($config['smtp'])) {
      return self::sendViaSMTP($config, $email, $subject, $content);
    }
  }

  private static function sendViaMailGun(array $config, $email, $subject, $content)
  {
    try {
      $client = new \Http\Adapter\Guzzle6\Client();
      $mailGun = new \Mailgun\Mailgun($config['mailGun']['key'], $client);

      $result = $mailGun->sendMessage($config['mailGun']['domain'], [
        'from' => $config['fromName'] . ' <' . $config['fromEmail'] . '>',
        'to' => $email,
        'subject' => $subject,
        'text' => strip_tags($content),
        'html' => $content
      ]);

      if ($result->http_response_code !== 200) {
        return json_encode($result, true);
      } else {
        return true;
      }
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  private static function sendViaSMTP(array $config, $email, $subject, $content)
  {
    $mail = new \PHPMailer();

    $mail->CharSet = 'UTF-8';
    $mail->XMailer = 'Monkey Media';
    $mail->isHTML(true);

    $mail->From = $config['fromEmail'];
    $mail->FromName = $config['fromName'];

    $mail->addAddress($email);

    $mail->isSMTP();
    $mail->SMTPAuth = true;

    if ($config['smtp']['secure']) {
      $mail->SMTPSecure = $config['smtp']['secure']; //tls
    }

    $mail->Host = $config['smtp']['host'];
    $mail->Port = $config['smtp']['port'];
    $mail->Username = $config['smtp']['user'];
    $mail->Password = $config['smtp']['pass'];

    $mail->Subject = $subject;
    $mail->Body = $content;
    $mail->AltBody = strip_tags($content);

    $isSent = $mail->send();

    return $isSent ? true : $mail->ErrorInfo;
  }

  private static function getConfig($configKey)
  {
    foreach (\Frame\Config::get('boxes') as $box) {
      if ($box['key'] == $configKey) {
        return $box;
      }
    }

    return false;
  }
}
