<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Model;

class game extends Model
{
    public const FIRST_PLAYER_TYPE = 1;
    public const SECOND_PLAYER_TYPE = 2;

    public function gameRoundLatest()
    {
        return $this->hasOne(round::class, 'game_id', 'id')->latest();
    }

    public static function getPlayerTypes(?int $type = null)
    {
        $types = [
            self::FIRST_PLAYER_TYPE => 'x',
            self::SECOND_PLAYER_TYPE => 'o',
        ];

        if ($type !== null) {
            return $types[$type] ?? null;
        }

        return $types;
    }
}
