<?php

namespace App\Models;

use DateTime;

/**
 * @property integer $id
 * @property array $files
 * @property object $request
 * @property object $response
 * @property string $systemMessage
 * @property string $responseFormat
 * @property integer $progressStatus
 * @property DateTime $createdAt
 * @property DateTime $updatedAt
 *
 * @property object $responseContent
 */
class GptRequest extends BaseModel
{
  protected $fillable = [
    'files',
    'request',
    'response',
    'systemMessage',
    'responseFormat',
  ];

  protected $casts = [
    'files' => 'files',
    'request' => 'object',
    'response' => 'object',
  ];

  protected $appends = [
    'responseContent'
  ];

  public function getResponseContentAttribute()
  {
    $responseContent = (object)[];

    if (isset($this->response->output)) {
      foreach ($this->response->output as $output) {
        if (empty($output->content)) {
          continue;
        }

        foreach ($output->content as $content) {
          if (empty($content->text)) {
            continue;
          }

          $decode = json_decode($content->text);

          if (is_object($decode)) {
            $responseContent = [...(array)$responseContent, ...(array)json_decode($content->text)];
            $responseContent = (object)$responseContent;
          }
        }
      }
    }

    return $responseContent;
  }
}
