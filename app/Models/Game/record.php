<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Model;

class record extends Model
{
    public function game()
    {
        return $this->belongsTo(game::class, 'game_id');
    }
}
