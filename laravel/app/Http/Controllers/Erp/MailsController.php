<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Mail;
use App\Services\MailMakerService;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class MailsController extends Controller
{
  public function index()
  {
    $mailsQuery = Mail::query();
    $mailsQuery->orderBy('id', 'desc');
    $mails = $mailsQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.mails.index', [
      'mails' => $mails,
    ]);
  }

  public function view(int $mailId)
  {
    /* @var $mail Mail */
    $mail = Mail::where('id', $mailId)->firstOrFail();

    return view('erp.mails.view', [
      'mail' => $mail,
    ]);
  }

  public function test(Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();
    $mails = [];

    if ($request->isMethod('post')) {
      try {
        $mailMaker = new MailMakerService();
        $mailMaker->setHandler(function ($params) use (&$mails) {
          $mails[] = $params;
        });

        switch ($request->get('action')) {
          case 'orderNew':
          {
            $mailMaker->orderNew((int)$request->get('orderId'));
            break;
          }
          case 'order':
          {
            $mailMaker->order((int)$request->get('orderId'));
            break;
          }
          case 'orderNewNotify':
          {
            $mailMaker->orderNewNotify((int)$request->get('orderId'));
            break;
          }
          case 'customerWelcome':
          {
            $mailMaker->customerWelcome((int)$request->get('customerId'));
            break;
          }
          case 'customerWelcomeNotify':
          {
            $mailMaker->customerWelcomeNotify((int)$request->get('customerId'));
            break;
          }
          case 'customerApproved':
          {
            $mailMaker->customerApproved((int)$request->get('customerId'));
            break;
          }
          case 'customerCreditLineValue':
          {
            $mailMaker->customerCreditLineValue((int)$request->get('customerId'));
            break;
          }
          case 'customerCreditLineRequestNotify':
          {
            $mailMaker->customerCreditLineRequestNotify((int)$request->get('customerId'));
            break;
          }
          case 'document':
          {
            $mailMaker->document((int)$request->get('documentId'));
            break;
          }
          default:
          {
            $errors->add('action', sprintf('Unknown action: %s', $request->get('action')));
          }
        }
      } catch (\Exception $e) {
        $errors->add('mailMaker', $e->getMessage());
      }
    }

    return view('erp.mails.test', [
      'errors' => $errors,
      'mails' => $mails,
    ]);
  }
}
