<?php

namespace App\Http\Controllers;

use App\Match;
use Illuminate\Support\Facades\Input;

class MatchController extends Controller {

    public function index() {
        return view('index');
    }

    /**
     * Returns a list of matches
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function matches() {
        return response()->json($this->fetchMatches());
    }

    /**
     * Returns the state of a single match
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function match($id) {
        return response()->json(
            Match::findOrFail($id)
        );
    }

    /**
     * Makes a move in a match
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function move($id) {
        $match = Match::findOrFail($id);

        if ($this->isFinished($match)) {
            return response()->json($match);
        }

        $position = Input::get('position');

        $board = $match->board;
        $board[$position] = $match->next;
        $match->board = $board;

        if ($this->checkForWin($board)) {
            $match->winner = $match->next;
        }

        $match->next = ($match->next == 1)? 2: 1;

        $match->save();

        return response()->json($match);
    }

    /**
     * Creates a new match and returns the new list of matches
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create() {
        $match = Match::create([
            'name' => 'Match',
            'next' => rand(1, 2), // let start in random order between player 1 or player 2
            'winner' => 0,
            'board' => [
                0, 0, 0,
                0, 0, 0,
                0, 0, 0,
            ]
        ]);

        $match->name = $match->name. ' '. $match->id;
        $match->save();

        return response()->json($this->fetchMatches());
    }

    /**
     * Deletes the match and returns the new list of matches
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id) {
        Match::destroy($id);
        return response()->json($this->fetchMatches());
    }

    /**
     * Creates a fake array of matches
     *
     * @return \Illuminate\Support\Collection
     */
    private function fetchMatches() {
        return Match::all();
    }

    /**
     * Checks for a win condition in the board
     * @param $board
     * @return bool
     */
    private function checkForWin($board) {
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
            if ($this->areEquals([$board[$wc[0]], $board[$wc[1]], $board[$wc[2]]])) {
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
    private function areEquals($data) {
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
    private function isFinished($match) {
        if ($match->winner != 0) {
            return true;
        }

        return count(array_filter(
            $match->board,
            function ($el) {
                return ($el == 0);
            }
        )) == 0;
    }
}