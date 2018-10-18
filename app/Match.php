<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    const PLAYER_1 = 1;
    const PLAYER_2 = 2;
    const INITIAL_BOARD = [
        0, 0, 0,
        0, 0, 0,
        0, 0, 0,
    ];

    protected $fillable = ['name', 'next', 'winner', 'board'];

    protected $casts = [
        'board' => 'array',
    ];

    /**
     * Creates a new match
     */
    public function createNew()
    {
        $match = $this
            ->create([
                'name' => 'Match',
                'next' => rand(self::PLAYER_1, self::PLAYER_2), // let start in random order between player 1 or player 2
                'winner' => 0,
                'board' => self::INITIAL_BOARD,
            ]);

        $match->update(['name' => $match->name. ' '. $match->id]);
    }

    /**
     * Do a match movement to the position given for the current player
     *
     * @param $position
     */
    public function move($position)
    {
        $board = $this->board;
        $board[$position] = $this->next;
        $this->board = $board;

        if ($this->checkForWin()) {
            $this->winner = $this->next;
        }

        $this->next = ($this->next == self::PLAYER_1)? self::PLAYER_2: self::PLAYER_1;

        $this->save();
    }

    /**
     * Checks for a win condition in the board
     * @param $board
     * @return bool
     */
    public function checkForWin()
    {
        $winConditions = [
            // rows
            [0,1,2],
            [3,4,5],
            [6,7,8],
            // cols
            [0,3,6],
            [1,4,7],
            [2,5,8],
            // diagonals
            [0,4,8],
            [2,4,6]
        ];

        foreach ($winConditions as $wc) {
            if ($this->areEquals([$this->board[$wc[0]], $this->board[$wc[1]], $this->board[$wc[2]]])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifies if a movement is a win
     * @param $data
     * @return bool
     */
    private function areEquals($data)
    {
        for ($i = 1; $i < count($data); $i++) {
            if ($data[$i] == 0 || $data[$i] != $data[$i - 1]) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check for a match finished
     * Are finished if a winner is setup or if not are more movements
     *
     * @param $match
     * @return bool
     */
    public function isFinished()
    {
        if ($this->winner != 0) {
            return true;
        }

        return count(array_filter(
                $this->board,
                function ($el) {
                    return ($el == 0);
                }
            )) == 0;
    }
}
