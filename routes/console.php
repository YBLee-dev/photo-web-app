<?php

use App\Photos\Photos\Photo;
use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('make:database {dbname}', function (){
//    DB::connection()->statement('CREATE DATABASE :schema', ['schema' => $this->argument('name')]);

    $dbname = $this->argument('dbname');
    $connection = $this->hasArgument('connection') && $this->argument('connection') ? $this->argument('connection'): DB::connection()->getPDO()->getAttribute(PDO::ATTR_DRIVER_NAME);

    $hasDb = DB::connection($connection)->select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = "."'".$dbname."'");

    if(empty($hasDb)) {
        DB::connection($connection)->select('CREATE DATABASE '. $dbname);
        $this->info("Database '$dbname' created for '$connection' connection");
    }
    else {
        $this->info("Database $dbname already exists for $connection connection");
    }
});


Artisan::command('project:mark-broken-photos', function (){
    /** @var Photo []  $photos */
    $photos = Photo::all();
    $count = $photos->count() - 1;
    $broken = 0;

    foreach ($photos as $key => $photo){
        $isExists = $photo->isRemoteFileExists();

        if(!$isExists){
            $photo->status = 'To delete';
            $photo->save();

            $broken++;
        }

        $text = $photo->id . " : " . $photo->present()->originalUrl();
        $text .= $isExists ? ' - Exists' : ' - Broken';
        $text .= " - $key checked of $count ". round($key/$count * 100, 2) . '%';
        $this->info($text);
    }

    $this->info("Checked $count photos. $broken were broken and marked as 'To delete' (" . round($broken/$count * 100, 2) .'%)');
});

Artisan::command('project:delete-marked-photos', function (){
    $deletedRows = Photo::where('status', 'To delete')->delete();

    $this->info("$deletedRows photos were deleted");
});
