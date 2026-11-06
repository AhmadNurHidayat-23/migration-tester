<?php
namespace App\Http\Controllers;
use App\Models\TestRun;
use App\Services\MigrationRunner;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class MigrationController extends Controller {
 public function index(Request $request){return view('migrations',['registry'=>config('migration_tests.registry'),'runs'=>TestRun::where('user_id',$request->user()->id)->latest()->paginate(10),'last'=>TestRun::where('user_id',$request->user()->id)->latest()->first()]);}
 public function store(Request $request,MigrationRunner $runner){$data=$request->validate(['migrations'=>'required|array|min:1|max:3','migrations.*'=>['required','string','distinct',Rule::in(array_keys(config('migration_tests.registry')))],'migration_file'=>'prohibited','path'=>'prohibited','connection'=>'prohibited']);$run=$runner->run($request->user(),$data['migrations']);return redirect('/runs/'.$run->id)->with('status','Pengujian dan percobaan rollback selesai: '.$run->status.'.');}
 public function show(Request $request,TestRun $run){abort_unless($run->user_id===$request->user()->id,403);return view('run',['run'=>$run]);}
 public function export(Request $request,TestRun $run){abort_unless($run->user_id===$request->user()->id,403);return response()->json($run->result,200,[],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)->header('Content-Disposition','attachment; filename="migration-run-'.$run->id.'.json"');}
}
