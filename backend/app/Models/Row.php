<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Row extends Model
{
    use HasFactory;

    protected $guarded = [];
    public $timestamps = false;

    public function castFormatDate(string $date)
    {
        list($d, $m, $y) = explode('.', $date);

        $d = Str::padLeft($d, 2, 0);
        $m = Str::padLeft($m, 2, 0);

        return date('Y-m-d', strtotime("$y-$m-$d"));
    }
}
