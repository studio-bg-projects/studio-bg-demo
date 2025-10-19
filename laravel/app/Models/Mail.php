<?php

namespace App\Models;

use DateTime;

/**
 * @property integer $id - Уникален идентификатор на имейл записа в системата
 * @property string $to - Имейл адресът на получателя на съобщението
 * @property string $subject - Тема на имейла, видима за получателя
 * @property string $content - Основно текстово съдържание на съобщението
 * @property string $lang - Езикът, на който е подготвен имейлът
 * @property null|DateTime $sentDate - Дата и час на реално изпращане или null ако е в изчакване
 * @property boolean $addHtmlWrapper - Флаг дали съдържанието трябва да се обвие със стандартен HTML шаблон
 * @property string $hash - Уникален хеш за идентифициране и предотвратяване на дублиране
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 */
class Mail extends BaseModel
{
  protected $casts = [
    'sentDate' => 'datetime',
    'addHtmlWrapper' => 'boolean',
  ];

  protected $fillable = [
    'to',
    'subject',
    'content',
    'lang',
    'addHtmlWrapper',
    'hash',
  ];
}
