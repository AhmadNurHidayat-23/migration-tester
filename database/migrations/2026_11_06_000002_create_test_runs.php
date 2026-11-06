<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void{Schema::create('test_runs',function(Blueprint $t){$t->id();$t->foreignId('user_id')->constrained();$t->uuid('sandbox_id')->unique();$t->json('selected');$t->string('status');$t->json('result')->nullable();$t->timestamps();});}
 public function down():void{Schema::dropIfExists('test_runs');}
};
