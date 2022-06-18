<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ColorTranslation extends Model
{   
	protected $fillable = [
        'id', 'name', 'code'
    ];
}
