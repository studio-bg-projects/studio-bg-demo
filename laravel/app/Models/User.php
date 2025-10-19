<?php

namespace App\Models;

use DateTime;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;

/**
 * @property integer $id - Уникален идентификатор на потребителя в системата
 * @property string $email - Имейл адрес за вход и получаване на уведомления
 * @property string $password - Хеширана парола за удостоверяване на потребителя
 * @property string $fullName - Пълно име на потребителя за визуализация в интерфейса
 * @property array $permissions - Списък с конкретни права при неадминистраторски акаунти
 * @property boolean $isAdmin - Флаг дали профилът разполага с администраторски привилегии
 * @property DateTime $createdAt - Датата на създаване на записа, управляван автоматично от Eloquent
 * @property DateTime $updatedAt - Датата на последната актуализация, синхронизиран от Eloquent
 * // Other fields
 * @property string $avatarUrl - Линк към аватар по подразбиране за показване в приложението
 */
class User extends BaseModel implements AuthenticatableContract, AuthorizableContract
{
  use Authenticatable, Authorizable;

  protected $hidden = [
    'password',
  ];

  protected $casts = [
    'isAdmin' => 'boolean',
    'permissions' => 'array',
  ];

  protected $fillable = [
    'email',
    'password',
    'fullName',
    'permissions',
    'isAdmin',
  ];

  public function setIsAdminAttribute($value): void
  {
    $this->attributes['isAdmin'] = $value;

    // Reset permissions for the admins
    if ($value) {
      $this->attributes['permissions'] = json_encode([]);
    }
  }

  public function getPermissionsAttribute($value): array
  {
    if ($this->isAdmin || !$value) {
      return [];
    }

    return (array)json_decode($value);
  }

  public function getAvatarUrlAttribute(): string
  {
    // return "https://www.gravatar.com/avatar/" . md5($this->email) . "?d=monsterid&s=300";
    return asset('/img/icons/user-placeholder.svg');
  }
}
