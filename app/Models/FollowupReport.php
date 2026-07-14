<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowupReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'follow_up_id',
        'usuario_id',
        'tipo',
        'arquivo'
    ];

    public function followup(){
        return $this->belongsTo(FollowUp::class);
    }

    public function usuario(){
        return $this->belongsTo(User::class);
    }
}
