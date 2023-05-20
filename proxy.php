<?php
error_reporting(E_ERROR | E_PARSE);
$file=fopen("JOBS_IN", "a");
$job_count = 0;
if(filesize("JOBS_IN") > 0) {
  $job_count = count(explode(",", fread(fopen("JOBS_IN", "r"),filesize("JOBS_IN"))));
}
$random_hash = bin2hex(random_bytes(18));
$data_in = ($job_count-1>0 ? "\n" : "").$random_hash.":".base64_encode(explode("?", $_SERVER['REQUEST_URI'])[1]).",";
fwrite($file, $data_in);
for (;;) {
   sleep(1);
   //job finished?
   clearstatcache();
   $response=fopen("JOBS_OUT", "r");
   $contents="";
   if(filesize("JOBS_OUT") > 0) {
     $contents = fread($response,filesize("JOBS_OUT"));
   }
   if(strpos($contents, $random_hash) !== false) {
     $data = trim(explode(",", explode($random_hash, $contents)[1])[0], ":");
     $fileWriteIn = fopen("JOBS_IN", "w");
     fwrite($fileWriteIn,explode($data_in, $contents)[0].explode($data_in, $contents)[1]);
     $fileWriteOut = fopen("JOBS_OUT", "w");
     fwrite($fileWriteOut,explode($random_hash.":".$data, $response)[0].explode($random_hash.":".$data, $response)[1]);
     //it finished, echo output :)
     echo(base64_decode($data));
     break;
   }
}
?>
