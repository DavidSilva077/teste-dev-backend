<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportedData extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'value'];
}
