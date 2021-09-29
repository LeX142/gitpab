<?php

namespace App\Http\Controllers;

class ImportController extends Controller
{
    public function all()
    {
        shell_exec('nohup php /var/www/html/artisan import:all > /dev/null 2>&1 &');
        return response()->redirectTo('/home')->with('Import started');
    }
}
