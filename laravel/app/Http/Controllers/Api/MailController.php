<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mail;
use App\Services\MailMakerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class MailController extends Controller
{
  public function create(Request $request)
  {
    $errors = new MessageBag();

    $mail = new Mail();
    $mail->fill($request->all());

    $validator = Validator::make($request->all(), [
      'to' => ['required', 'email', 'max:255'],
      'subject' => ['required', 'string', 'max:255'],
      'content' => ['required', 'string'],
      'lang' => ['nullable', 'string', 'max:10'],
      'addHtmlWrapper' => ['nullable', 'boolean'],
    ]);

    $errors->merge($validator->errors());

    if ($errors->isEmpty()) {
      $service = new MailMakerService();
      $result = $service->api($mail->toArray());
      $mail = $result ? $result : $mail;
    }

    return [
      'mail' => $mail,
      'errors' => $errors,
    ];
  }
}
