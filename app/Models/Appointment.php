<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Appointment extends Model
{
    protected $fillable = [
        'client_id',
        'user_id',
        'service_id',
        'date',
        'time',
        'notes',
        'before_photo',
        'after_photo',
        'products_used',
        'price',
        'payment_method', // novo campo
        'plan',           // novo campo
        'signature',      // novo campo
        'status',        
    ];

    protected $casts = [
        'products_used' => 'array',
        'products_ids' => 'array', // novo campo para IDs dos produtos
        'date' => 'date',
        'time' => 'string',
        'price' => 'float',
    ];

    // Adicione esta linha para incluir o atributo no JSON:
    protected $appends = ['calendar_date', 'date_br'];


    // Relacionamentos
    public function client()
    {
        return $this->belongsTo(\App\Models\Client::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function service()
    {
        return $this->belongsTo(\App\Models\Service::class);
    }

    public function products()
    {
        return $this->belongsToMany(\App\Models\Product::class, null, 'appointment_id', 'product_id')
            ->withTimestamps();
    }

    // Adicione este accessor para formato brasileiro, se precisar:
    public function getDateBrAttribute()
    {
        return $this->date ? Carbon::parse($this->attributes['date'])->format('d/m/Y') : null;
    }

    // O accessor do calendário pode ficar assim:
    public function getCalendarDateAttribute()
    {
        $date = $this->date ? Carbon::parse($this->attributes['date'])->format('Y-m-d') : null;
        $time = $this->time ? substr($this->time, 0, 5) : null;
        return $date && $time ? "{$date}T{$time}" : $date;
    }

    public function getBeforePhotoAttribute($value)
    {
        if (!$value) {
            return null;
        }
        // Gera a URL completa
        return url($value);
    }

    public function getAfterPhotoAttribute($value)
    {
        if (!$value) {
            return null;
        }
        // Gera a URL completa
        return url($value);
    }

    // Status em português para listagem
    public function getStatusAttribute($value)
    {
        $map = [
            'scheduled'    => 'Agendado',
            'completed'    => 'Concluído',
            'canceled'     => 'Cancelado',
            'in_progress'  => 'Em andamento',
            'absent'       => 'Faltou',
        ];
        return $map[$value] ?? ucfirst($value);
    }
}