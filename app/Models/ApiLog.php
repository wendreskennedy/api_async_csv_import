<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'method',
        'url',
        'ip',
        'user_agent',
        'request_data',
        'response_data',
        'status_code',

    ];

    /**
     * Atributos para cast automático
     */
    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
    ];

    protected $primaryKey = 'id';

    protected $table = 'api_logs';

    /**
     * Relação com o usuário que fez a requisição (se autenticado)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
