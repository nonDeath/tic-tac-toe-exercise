<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MatchControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $matches = [];

    public function setUp()
    {
        parent::setUp();
        $this->matches = factory(\App\Match::class, 1)->create();
    }

    /**
     * Test for the endpoint /api/match when the database has data to retrieve
     *
     * @return void
     */
    public function testCanGetMatches()
    {
        $response = $this->json('GET', '/api/match');

        $response
            ->assertStatus(200)
            ->assertJsonStructure(
                [
                    '*' => [
                    'id',
                    'name'
                    ]
                ]
            );
    }

    /**
     * Test the match deletion endpoint /api/match/{id}
     */
    public function testCanDeteteAMatch()
    {
        $match = $this->matches->first();

        $response = $this->json('DELETE', "api/match/{$match->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('matches', ['id' => $match->id]);
    }

    /**
     * Test the match creation endpoint /api/match
     */
    public function testCanCreateAMatch()
    {
        $response = $this->json('POST', "api/match");

        $response->assertStatus(200);

        $match = \App\Match::orderBy('id', 'DESC')->first();

        $this->assertDatabaseHas(
            'matches',
            [
                'name' => 'Match '. $match->id,
            ]
        );
    }

    public function testCanMoveInAMatch()
    {
        $this->json('POST', "api/match");

        $match = \App\Match::orderBy('id', 'DESC')->first();

        $next = $match->next;
        $position = rand(0, 8);
        $board = $match->board;
        $board[$position] = "\"{$next}\"";

        $response = $this->json('PUT', "api/match/{$match->id}", ['position' => $position]);

        $response->assertStatus(200)
            ->assertJsonStructure(
                [
                    "id",
                    "name",
                    "next",
                    "winner",
                    "board",
                    "created_at",
                    "updated_at",
                ]
            );

        $this->assertDatabaseHas(
            'matches',
            [
                'id' => $match->id,
                'next' => ($next == 2)? 1: 2,
                'board' => '['. implode(',', $board) .']',
            ]
        );
    }
}
