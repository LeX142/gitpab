<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormRequest;
use Illuminate\Support\Facades\Artisan;

class ImportController extends Controller
{
    public function all()
    {
        shell_exec('nohup php /var/www/html/artisan import:all > /dev/null 2>&1 &');
        return response()->redirectTo('/home')->with('Import started');
    }
}
