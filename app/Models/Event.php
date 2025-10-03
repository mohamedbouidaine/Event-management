<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    // الأعمدة التي يمكن ملؤها
    protected $fillable = ['title', 'description', 'date', 'location', 'image'];



    // تحويل العمود date إلى كائن Carbon تلقائياً
    protected $casts = [
        'date' => 'datetime',
    ];

    // علاقة المشاركين
    public function participants() {
        return $this->hasMany(Participant::class);
    }
}
