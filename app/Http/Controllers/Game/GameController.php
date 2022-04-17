<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Models\Game\game;
use App\Models\Game\record;
use App\Models\Game\round;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GameController extends Controller
{
    public function index()
    { 
        return view('/game/new_game');
    }

    public function joinMe()
    {
        return view('/game/join');
    }

    public function join(Request $request)
    {
        $code = explode("-", $request->code);
        $game = game::find($code[0]);
        abort_unless($game, 404);
        return redirect()->route('play', [
            'game' => $game,
            'token' => $game->token,
            'round' => $code[1] ?? null,
        ]);
    }

    public function statistics()
    {
        $game = game::all();
        return view('game.statistics', compact('game'));
    }

    private static function generateToken(): string
    {
        return (string) Str::uuid();
    }

    function new (Request $request) {

        DB::beginTransaction();
        $validated = $request->validate([
            'first_player' => 'required|max:255|string',
            'second_player' => 'required|max:255|string',
        ]);

        $game = new game();
        $game->first_player = strtoupper($request->first_player);
        $game->second_player = strtoupper($request->second_player);
        $game->token = $this->generateToken();
        $game->save();

        $roundGame = new round();
        $roundGame->game_id = $game->id;
        $roundGame->save();

        if ($game) {
            DB::commit();
            return redirect()->route('play', [
                'game' => $game,
                'token' => $game->token,
                'round' => $game->gameRoundLatest->id ?? null,
            ]);
        } else {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
            ]);
        }
    }

    public function play(Request $request)
    {
        $game = game::find($request->query('game'));
        $token = $request->query('token');
        $round = $request->query('round');
        abort_unless($token && $round, 404);

        $gameByToken = game::where('token', $token)->first();
        abort_unless($game->id === $gameByToken->id, 404);

        $gameRound = round::find($round);
        abort_unless($gameRound && $gameRound->game_id === $game->id, 404);

        $firstPlayerType = game::FIRST_PLAYER_TYPE;
        $secondPlayerType = game::SECOND_PLAYER_TYPE;

        $prepareData = $this->getData($game->id, $round);
        $gameCountHistories = record::where('game_id', $game->id)->where('round_id', $round)->count();
        $isFullGameField = $this->isFullGameField($gameCountHistories);

        return view('game.play', compact(
            'game',
            'gameCountHistories',
            'prepareData',
            'round',
            'isFullGameField',
            'firstPlayerType',
            'secondPlayerType'
        ));
    }

    private function isFullGameField(int $countHistories): bool
    {
        return $countHistories === (3 * 3);
    }

    private function getData($game, $round)
    {
        $gameHistories = array();
        $playerHistories = array();
        $playerType = null;
        $histories = record::where('game_id', $game)->where('round_id', $round)->orderBy('id', 'ASC')->get();

        foreach ($histories as $history) {
            $playerType = $history->player_type;
            $gameHistories[$history->game_row][$history->game_column] = $playerType;
            $playerHistories[$playerType][$history->game_row][$history->game_column] = true;
        }
        $horizontalSuccess = $this->getHorizontalSuccess($gameHistories, $playerHistories);
        $verticalSuccess = $this->getVerticalSuccess($gameHistories, $playerHistories);
        $diagonalRightSuccess = $this->getDiagonalRightSuccess($gameHistories, $playerHistories);
        $diagonalLeftSuccess = $this->getDiagonalLeftSuccess($gameHistories, $playerHistories);
        $gameOver = $this->isGameOver($horizontalSuccess, $verticalSuccess, $diagonalRightSuccess, $diagonalLeftSuccess);
        $playerWinner = $this->getPlayerWinner($gameHistories, $horizontalSuccess, $verticalSuccess, $diagonalRightSuccess, $diagonalLeftSuccess);

        return compact(
            'gameHistories',
            'playerType',
            'horizontalSuccess',
            'verticalSuccess',
            'diagonalRightSuccess',
            'diagonalLeftSuccess',
            'gameOver',
            'playerWinner'
        );
    }

    private function getHorizontalSuccess($gameHistories, $playerHistories): array
    {
        $data = array();

        for ($row = 1; $row <= 3; $row++) {
            $data[$row] = true;
            $firstCell = null;
            for ($col = 1; $col <= 3; $col++) {
                if ($firstCell === null) {
                    $firstCell = $gameHistories[$row][$col] ?? false;
                }
                $cell = $playerHistories[$firstCell][$row][$col] ?? false;
                $data[$row] = $data[$row] && $cell;
            }
        }

        return $data;
    }

    private function getVerticalSuccess($gameHistories, $playerHistories): array
    {
        $data = [];

        for ($col = 1; $col <= 3; $col++) {
            $data[$col] = true;
            $firstCell = null;
            for ($row = 1; $row <= 3; $row++) {
                if ($firstCell === null) {
                    $firstCell = $gameHistories[$row][$col] ?? false;
                }
                $cell = $playerHistories[$firstCell][$row][$col] ?? false;
                $data[$col] = $data[$col] && $cell;
            }
        }

        return $data;
    }

    private function getDiagonalRightSuccess($gameHistories, $playerHistories): array
    {
        $data = [];
        $diagonalRight = 0;
        $firstCell = null;

        for ($row = 1; $row <= 3; $row++) {
            for ($col = 1; $col <= 3; $col++) {
                if ($row === $col) {
                    if ($firstCell === null) {
                        $firstCell = $gameHistories[$row][$col] ?? false;
                    }
                    if (!isset($data[$row][$col])) {
                        $data[$row][$col] = true;
                    }
                    $cell = $playerHistories[$firstCell][$row][$col] ?? false;
                    if ($cell) {
                        $diagonalRight++;
                    }
                    $data[$row][$col] = $data[$row][$col] && $cell;
                }
            }
        }

        if ($diagonalRight < 3) {
            $data = [];
        }

        return $data;
    }

    private function getDiagonalLeftSuccess($gameHistories, $playerHistories): array
    {
        $data = [];
        $diagonalLeft = 0;
        $lastCell = null;

        for ($row = 1; $row <= 3; $row++) {
            for ($col = 1; $col <= 3; $col++) {
                if (($col === 3 && $row === 1) || ($col === (3 - $row + 1))) {
                    if ($lastCell === null) {
                        $lastCell = $gameHistories[$row][$col] ?? false;
                    }
                    if (!isset($data[$row][$col])) {
                        $data[$row][$col] = true;
                    }
                    $cell = $playerHistories[$lastCell][$row][$col] ?? false;
                    if ($cell) {
                        $diagonalLeft++;
                    }
                    $data[$row][$col] = $data[$row][$col] && $cell;
                }
            }
        }

        if ($diagonalLeft < 3) {
            $data = [];
        }

        return $data;
    }

    private function isGameOver(array $horizontalSuccess, array $verticalSuccess, array $diagonalRightSuccess, array $diagonalLeftSuccess): bool
    {
        $data = false;

        for ($row = 1; $row <= 3; $row++) {
            for ($col = 1; $col <= 3; $col++) {
                if ($horizontalSuccess[$row] ?? null) {
                    $data = true;
                } elseif ($verticalSuccess[$col] ?? null) {
                    $data = true;
                } elseif ($diagonalRightSuccess[$row][$col] ?? null) {
                    $data = true;
                } elseif ($diagonalLeftSuccess[$row][$col] ?? null) {
                    $data = true;
                }
            }
        }

        return $data;
    }

    private function getPlayerWinner(array $gameHistories, array $horizontalSuccess, array $verticalSuccess, array $diagonalRightSuccess, array $diagonalLeftSuccess): ?int
    {
        for ($row = 1; $row <= 3; $row++) {
            for ($col = 1; $col <= 3; $col++) {
                if ($horizontalSuccess[$row] ?? null) {
                    return $gameHistories[$row][$col];

                } elseif ($verticalSuccess[$col] ?? null) {
                    return $gameHistories[$row][$col];

                } elseif ($diagonalRightSuccess[$row][$col] ?? null) {
                    return $gameHistories[$row][$col];

                } elseif ($diagonalLeftSuccess[$row][$col] ?? null) {
                    return $gameHistories[$row][$col];
                }
            }
        }

        return null;
    }

    public function record(Request $request)
    {
        $record = new record();
        $record->game_id = $request->game_id;
        $record->round_id = $request->game_round_id;
        $record->player_type = $request->player_type;
        $record->game_row = $request->game_row;
        $record->game_column = $request->game_column;
        $record->save();

        return redirect()->route('play', [
            'game' => $record->game,
            'token' => $record->game->token,
            'round' => $record->round_id,
        ]);
    }

    public function round(Request $request)
    {
        $round = new round();
        $round->game_id = $request->game_id;
        $round->save();

        return redirect()->route('play', [
            'game' => $round->game,
            'token' => $round->game->token,
            'round' => $round->id,
        ]);
    }
}
