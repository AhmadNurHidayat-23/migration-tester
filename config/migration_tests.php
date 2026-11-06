<?php
return ['registry'=>[
 'projects'=>['file'=>'001_create_test_projects.php','description'=>'Membuat tabel test_projects.','depends'=>[]],
 'issues'=>['file'=>'002_create_test_issues.php','description'=>'Membuat test_issues dengan foreign key ke test_projects.','depends'=>['projects']],
 'failure'=>['file'=>'003_duplicate_column.php','description'=>'Kasus negatif: menambahkan kolom title yang sudah ada. Diperkirakan gagal.','depends'=>['projects']],
]];
