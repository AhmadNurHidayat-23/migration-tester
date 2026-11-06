<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void{Schema::create('test_issues',function(Blueprint $t){$t->id();$t->foreignId('test_project_id')->constrained('test_projects');$t->string('title');$t->boolean('fixed')->default(false);});}
 public function down():void{Schema::dropIfExists('test_issues');}
};
