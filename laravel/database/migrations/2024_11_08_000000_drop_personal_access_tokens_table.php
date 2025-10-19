<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class DropPersonalAccessTokensTable extends Migration
{
  public function up()
  {
    Schema::dropIfExists('personal_access_tokens');
  }

  public function down()
  {
  }
}
