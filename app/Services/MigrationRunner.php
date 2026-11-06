<?php
namespace App\Services;
use App\Models\{User,TestRun};
use Illuminate\Support\Facades\{DB,Schema};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
class MigrationRunner {
 public function __construct(private TestingWorkspace $workspace,private SchemaInspector $inspector){}
 public function run(User $user,array $selected):TestRun {
  abort_unless(app()->environment(['local','testing']),403,'Pengujian migration hanya tersedia pada environment local/testing.');
  $registry=config('migration_tests.registry');
  foreach($selected as $id){if(!isset($registry[$id]))throw ValidationException::withMessages(['migrations'=>'Migration tidak terdaftar.']);foreach($registry[$id]['depends'] as $dep)if(!in_array($dep,$selected,true))throw ValidationException::withMessages(['migrations'=>'Migration '.$id.' memerlukan '.$dep.'.']);}
  abort_if(TestRun::where('user_id',$user->id)->count()>=100,422,'Maksimal 100 pengujian per pengguna.');
  $uuid=(string)Str::uuid();$directory=$this->workspace->directory('migration-runs');$path=$directory.DIRECTORY_SEPARATOR.$uuid.'.sqlite';$file=fopen($path,'x');if(!$file)throw new \RuntimeException('Database testing baru tidak dapat dibuat.');fclose($file);
  $run=TestRun::create(['user_id'=>$user->id,'sandbox_id'=>$uuid,'selected'=>$selected,'status'=>'running']);
  $original=config('database.default');$originalSchema=Schema::getFacadeRoot();$start=microtime(true);$steps=[];$errors=[];$applied=[];$before=[];$up=[];$after=[];$inspectedBefore=false;$inspectedAfter=false;
  try {
   config(['database.connections.migration_sandbox'=>['driver'=>'sqlite','database'=>$path,'prefix'=>'','foreign_key_constraints'=>true,'busy_timeout'=>5000],'database.default'=>'migration_sandbox']);DB::purge('migration_sandbox');$connection=DB::connection('migration_sandbox');Schema::swap($connection->getSchemaBuilder());$before=$this->inspector->inspect($connection);$inspectedBefore=true;
   foreach($registry as $id=>$meta)if(in_array($id,$selected,true)) {
    $root=realpath(database_path('test-migrations'));$file=realpath($root.DIRECTORY_SEPARATOR.$meta['file']);if(!$file||!str_starts_with($file,$root.DIRECTORY_SEPARATOR))throw new \RuntimeException('Migration di luar direktori tepercaya ditolak.');
    try{$migration=require $file;$migration->up();$applied[]=[$id,$migration];$steps[]=['id'=>$id,'phase'=>'up','status'=>'passed'];}
    catch(\Throwable $e){$errors[]=['migration'=>$id,'phase'=>'up','class'=>get_class($e),'message'=>$e->getMessage()];$steps[]=['id'=>$id,'phase'=>'up','status'=>'failed'];break;}
   }
   $up=$this->inspector->inspect($connection);
   foreach(array_reverse($applied) as [$id,$migration])try{$migration->down();$steps[]=['id'=>$id,'phase'=>'down','status'=>'passed'];}catch(\Throwable $e){$errors[]=['migration'=>$id,'phase'=>'down','class'=>get_class($e),'message'=>$e->getMessage()];$steps[]=['id'=>$id,'phase'=>'down','status'=>'failed'];}
   $after=$this->inspector->inspect($connection);$inspectedAfter=true;
  }catch(\Throwable $e){$errors[]=['migration'=>null,'phase'=>'runner','class'=>get_class($e),'message'=>$e->getMessage()];}
  finally{Schema::swap($originalSchema);DB::purge('migration_sandbox');config(['database.default'=>$original]);}
  $rollback=$inspectedBefore&&$inspectedAfter&&$before===$after;$result=['sandbox_id'=>$uuid,'driver'=>'sqlite','isolated'=>true,'selected'=>$selected,'steps'=>$steps,'before'=>$before,'after_up'=>$up,'after_rollback'=>$after,'changes'=>$this->inspector->changes($before,$up),'rollback_verified'=>$rollback,'errors'=>$errors,'duration_ms'=>round((microtime(true)-$start)*1000)];
  $run->update(['status'=>!$errors&&$rollback?'passed':'failed','result'=>$result]);return $run;
 }
}
