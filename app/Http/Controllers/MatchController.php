<?php

namespace App\Http\Controllers;

use App\Http\Requests\MoveMatchRequest;
use App\Match;

class MatchController extends Controller
{

    public function index()
    {
        return view('index');
    }

    /**
     * Returns a list of matches
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function matches()
    {
        return response()->json($this->fetchMatches());
    }

    /**
     * Returns the state of a single match
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function match($id)
    {
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
    public function move(MoveMatchRequest $request, $id)
    {
        $match = Match::findOrFail($id);

        if ($match->isFinished($match)) {
            return response()->json($match);
        }

        $match->move($request->position);

        return response()->json($match);
    }

    /**
     * Creates a new match and returns the new list of matches
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Match $match)
    {
        $match->createNew();

        return response()->json($this->fetchMatches());
    }

    /**
     * Deletes the match and returns the new list of matches
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        Match::destroy($id);
        return response()->json($this->fetchMatches());
    }

    /**
     * Creates a fake array of matches
     *
     * @return \Illuminate\Support\Collection
     */
    private function fetchMatches()
    {
        return Match::all();
    }
}
