<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Team;


class TeamTest extends TestCase
{

    public function setUp() {
        parent::setUp();

        $this->trashedTeam = factory(Team::class)->create([
            'deleted_at'  =>  date("Y-m-d H:i:s"),
        ]);

        $this->teamRecord = factory(Team::class)->create();
    }

    public function testIndexWithPermissions()
    {
        $this->actingAs($this->userAuthorized, 'api');

        $response = $this->json('GET', 'api/teams');
        $response->assertStatus(200)
            ->assertJsonFragment([
                'current_page'   => 1,
                'total'          => Team::withTrashed()->count()
            ]);
    }

    public function testIndexWithoutPermissions()
    {
        $this->actingAs($this->userUnauthorized, 'api');

        $response = $this->json('GET', 'api/teams');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'current_page'   => 1,
                'total'          => Team::count()
            ]);
    }

    public function testIndexAsGuest()
    {
        $response = $this->json('GET', 'api/teams');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'current_page'   => 1,
                'total'          => Team::count()
            ]);
    }

    public function testStoreWithPermissions()
    {
        $this->actingAs($this->userAuthorized, 'api');

        $team = factory(Team::class)->make()->toArray();

        $response = $this->json('POST', 'api/teams', $team);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => $team['name']
            ]);
    }

    public function testStoreWithoutPermissions()
    {
        $this->actingAs($this->userUnauthorized, 'api');

        $team = factory(Team::class)->make()->toArray();

        $response = $this->json('POST', 'api/teams', $team);

        $response->assertStatus(403)
            ->assertJsonFragment([
                'Forbidden',
            ]);
    }

    public function testStoreAsGuest()
    {
        $team = factory(Team::class)->make()->toArray();

        $response = $this->json('POST', 'api/teams', $team);

        $response->assertStatus(401)
            ->assertJsonFragment([
                'Unauthorized',
            ]);
    }

    public function testShowWithPermissions()
    {
        $this->ActingAs($this->userAuthorized, 'api');

        $response = $this->json('GET', 'api/teams/' . $this->trashedTeam->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => $this->trashedTeam['name']
            ]);
    }

    public function testShowWithoutPermissions()
    {
        $this->ActingAs($this->userUnauthorized, 'api');

        $response = $this->json('GET', 'api/teams/' . $this->trashedTeam->id);

        $response->assertStatus(403)
            ->assertJsonFragment([
                'Forbidden',
            ]); 

        $response = $this->json('GET', 'api/teams/' . $this->teamRecord->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => $this->teamRecord['name']
            ]);
    }

    public function testShowAsGuest()
    {
        $response = $this->json('GET', 'api/teams/' . $this->trashedTeam->id);

        $response->assertStatus(403)
            ->assertJsonFragment([
                'Forbidden',
            ]); 

        $response = $this->json('GET', 'api/teams/' . $this->teamRecord->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => $this->teamRecord['name']
            ]);
    }

    public function testUpdateWithPermissions()
    {
        $this->ActingAs($this->userAuthorized, 'api');

        $team = factory(Team::class)->create();

        $team->name = "updating name";

        $response = $this->json('PUT', 'api/teams/'. $team->id, $team->toArray());

        $response->assertStatus(201);

        $updatedTeam = Team::find($team->id);

        $this->assertEquals($team->name, $updatedTeam->name);
    }

    public function testUpdateWithoutPermissions()
    {
        $this->ActingAs($this->userUnauthorized, 'api');

        $team = factory(Team::class)->create();

        $team->name = "updating name";

        $response = $this->json('PUT', 'api/teams/'. $team->id, $team->toArray());

        $response->assertStatus(403)
            ->assertJsonFragment([
                'Forbidden'
            ]);  
    }

    public function testUpdateAsGuest()
    {
        $team = factory(Team::class)->create();

        $team->name = "updating name";

        $response = $this->json('PUT', 'api/teams/'. $team->id, $team->toArray());

        $response->assertStatus(401)
            ->assertJsonFragment([
                'Unauthorized'
            ]);
    }

    public function testDeleteWithPermissions()
    {
        $this->actingAs($this->userAuthorized, 'api');

        $team = factory(Team::class)->create();

        $response = $this->json('DELETE', 'api/teams/' . $team->id);

        $response->assertStatus(204);

        $createdPost = Team::withTrashed()->find($team->id);

        $this->assertNotNull($createdPost->deleted_at);
    }

    public function testDeleteWithoutPermissions()
    {
        $this->actingAs($this->userUnauthorized, 'api');

        $team = factory(Team::class)->create();

        $response = $this->json('DELETE', 'api/teams/' . $team->id);

        $response->assertStatus(403)
            ->assertJsonFragment([
                'Forbidden'
            ]);
    }

    public function testDeleteAsGuest()
    {
        $team = factory(Team::class)->create();

        $response = $this->json('DELETE', 'api/teams/' . $team->id);

        $response->assertStatus(401)
            ->assertJsonFragment([
                'Unauthorized'
            ]);
    }

}
