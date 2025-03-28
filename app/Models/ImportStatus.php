<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_path',
        'status',
        'description',
    ];

    protected $primaryKey = 'id';

    protected $table = 'import_status';
}
