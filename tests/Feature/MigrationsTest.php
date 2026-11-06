<?php
namespace Tests\Feature;
use App\Models\{User,TestRun};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
class MigrationsTest extends TestCase {
 use RefreshDatabase;
 public function test_migrations_run_and_rollback_in_isolated_database():void{$user=User::factory()->create();$this->actingAs($user)->post('/runs',['migrations'=>['projects','issues']])->assertRedirect();$run=TestRun::firstOrFail();$this->assertSame('passed',$run->status);$this->assertTrue($run->result['rollback_verified']);$this->assertArrayHasKey('test_issues',$run->result['after_up']);$this->assertSame([],$run->result['after_rollback']);$this->assertDatabaseHas('users',['id'=>$user->id]);$this->assertFalse(DB::connection()->getSchemaBuilder()->hasTable('test_projects'));$this->get('/runs/'.$run->id.'/report')->assertOk()->assertJsonPath('isolated',true);}
 public function test_real_sql_error_is_reported_and_applied_migrations_are_rolled_back():void{$this->actingAs(User::factory()->create())->post('/runs',['migrations'=>['projects','failure']])->assertRedirect();$run=TestRun::firstOrFail();$this->assertSame('failed',$run->status);$this->assertNotEmpty($run->result['errors']);$this->assertStringContainsString('duplicate',$run->result['errors'][0]['message']);$this->assertTrue($run->result['rollback_verified']);}
 public function test_each_run_has_a_distinct_sandbox():void{$this->actingAs(User::factory()->create());$this->post('/runs',['migrations'=>['projects']]);$this->post('/runs',['migrations'=>['projects']]);$this->assertSame(2,TestRun::distinct()->count('sandbox_id'));}
 public function test_untrusted_file_path_and_missing_dependency_are_rejected():void{$this->actingAs(User::factory()->create())->post('/runs',['migrations'=>['../evil.php'],'path'=>'C:/other/database.sqlite'])->assertSessionHasErrors();$this->post('/runs',['migrations'=>['issues']])->assertSessionHasErrors('migrations');$this->assertDatabaseCount('test_runs',0);}
 public function test_history_is_private_and_production_is_blocked():void{
  $a=User::factory()->create();$this->actingAs($a)->post('/runs',['migrations'=>['projects']]);$run=TestRun::firstOrFail();
  $this->get('/migrations')->assertOk();$this->get('/runs/'.$run->id)->assertOk();
  $this->actingAs(User::factory()->create())->get('/runs/'.$run->id)->assertForbidden();
  $this->app['env']='production';$token=\Illuminate\Support\Str::random(40);
  $this->withSession(['_token'=>$token])->post('/runs',['migrations'=>['projects'],'_token'=>$token])->assertForbidden();
 }
}
