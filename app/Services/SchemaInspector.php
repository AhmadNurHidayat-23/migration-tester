<?php
namespace App\Services;
use Illuminate\Database\Connection;
class SchemaInspector {
 public function inspect(Connection $connection):array {
  $pdo=$connection->getPdo();$tables=$pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name")->fetchAll(\PDO::FETCH_COLUMN);$schema=[];
  foreach($tables as $table){$quoted='"'.str_replace('"','""',$table).'"';$columns=$pdo->query('PRAGMA table_info('.$quoted.')')->fetchAll(\PDO::FETCH_ASSOC);$schema[$table]=['columns'=>array_map(fn($c)=>['name'=>$c['name'],'type'=>$c['type'],'nullable'=>!((bool)$c['notnull']),'primary'=>(bool)$c['pk']],$columns),'foreign_keys'=>$pdo->query('PRAGMA foreign_key_list('.$quoted.')')->fetchAll(\PDO::FETCH_ASSOC)];}
  return $schema;
 }
 public function changes(array $before,array $after):array {
  $out=[];foreach(array_diff(array_keys($after),array_keys($before)) as $name)$out[]='Tambah tabel: '.$name;foreach(array_diff(array_keys($before),array_keys($after)) as $name)$out[]='Hapus tabel: '.$name;
  foreach(array_intersect(array_keys($before),array_keys($after)) as $name)if($before[$name]!==$after[$name])$out[]='Ubah struktur: '.$name;return $out;
 }
}
