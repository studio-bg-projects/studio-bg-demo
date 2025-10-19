<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class BaseModel extends Model
{
  // Custom stamp fields
  const CREATED_AT = 'createdAt';
  const UPDATED_AT = 'updatedAt';

  /**
   * The primary key associated with the table.
   *
   * @var string
   */
  protected $primaryKey = 'id';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    '*'
  ];

  /**
   * Get an attribute from the model.
   *
   * @param string $key
   * @return mixed
   */
  public function getAttribute($key): mixed
  {
    if (array_key_exists($key, $this->relations)) {
      return parent::getAttribute($key);
    } else {
      return parent::getAttribute(Str::camel($key));
    }
  }

  /**
   * Set a given attribute on the model.
   *
   * @param string $key
   * @param mixed $value
   * @return mixed
   */
  public function setAttribute($key, $value): mixed
  {
    return parent::setAttribute(Str::camel($key), $value);
  }


  /**
   * Get the table associated with the model.
   *
   * @return string
   */
  public function getTable(): string
  {
    return $this->table ?? lcfirst(Str::pluralStudly(class_basename($this)));
  }
}
