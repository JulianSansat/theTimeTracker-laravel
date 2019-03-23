<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Log;


class LogTest extends TestCase
{

    public function setUp() {
        parent::setUp();

        $this->trashedLog = factory(Log::class)->create([
            'deleted_at'  =>  date("Y-m-d H:i:s"),
        ]);

        $this->logRecord = factory(Log::class)->create();
    }

    public function testIndexWithPermissions()
    {
        $this->actingAs($this->userAuthorized, 'api');

        $response = $this->json('GET', 'api/logs');
        $response->assertStatus(200)
            ->assertJsonFragment([
                'current_page'   => 1,
                'total'          => Log::withTrashed()->count()
            ]);
    }

    public function testIndexWithoutPermissions()
    {
        $this->actingAs($this->userUnauthorized, 'api');

        $response = $this->json('GET', 'api/logs');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'current_page'   => 1,
                'total'          => Log::count()
            ]);
    }

    public function testIndexAsGuest()
    {
        $response = $this->json('GET', 'api/logs');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'current_page'   => 1,
                'total'          => Log::count()
            ]);
    }

    public function testStoreWithPermissions()
    {
        $this->actingAs($this->userAuthorized, 'api');

        $log = factory(Log::class)->make()->toArray();

        $response = $this->json('POST', 'api/logs', $log);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'start' => $log['start']
            ]);
    }

    public function testStoreWithoutPermissions()
    {
        $this->actingAs($this->userUnauthorized, 'api');

        $log = factory(Log::class)->make()->toArray();

        $response = $this->json('POST', 'api/logs', $log);

        $response->assertStatus(403)
            ->assertJsonFragment([
                'Forbidden',
            ]);
    }

    public function testStoreAsGuest()
    {
        $log = factory(Log::class)->make()->toArray();

        $response = $this->json('POST', 'api/logs', $log);

        $response->assertStatus(401)
            ->assertJsonFragment([
                'Unauthorized',
            ]);
    }

    public function testShowWithPermissions()
    {
        $this->ActingAs($this->userAuthorized, 'api');

        $response = $this->json('GET', 'api/logs/' . $this->trashedLog->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $this->trashedLog['id']
            ]);
    }

    public function testShowWithoutPermissions()
    {
        $this->ActingAs($this->userUnauthorized, 'api');

        $response = $this->json('GET', 'api/logs/' . $this->trashedLog->id);

        $response->assertStatus(403)
            ->assertJsonFragment([
                'Forbidden',
            ]); 

        $response = $this->json('GET', 'api/logs/' . $this->logRecord->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $this->logRecord['id']
            ]);
    }

    public function testShowAsGuest()
    {
        $response = $this->json('GET', 'api/logs/' . $this->trashedLog->id);

        $response->assertStatus(403)
            ->assertJsonFragment([
                'Forbidden',
            ]); 

        $response = $this->json('GET', 'api/logs/' . $this->logRecord->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $this->logRecord['id']
            ]);
    }

    public function testDeleteWithPermissions()
    {
        $this->actingAs($this->userAuthorized, 'api');

        $log = factory(Log::class)->create();

        $response = $this->json('DELETE', 'api/logs/' . $log->id);

        $response->assertStatus(204);

        $createdPost = Log::withTrashed()->find($log->id);

        $this->assertNotNull($createdPost->deleted_at);
    }

    public function testDeleteWithoutPermissions()
    {
        $this->actingAs($this->userUnauthorized, 'api');

        $log = factory(Log::class)->create();

        $response = $this->json('DELETE', 'api/logs/' . $log->id);

        $response->assertStatus(403)
            ->assertJsonFragment([
                'Forbidden'
            ]);
    }

    public function testDeleteAsGuest()
    {
        $log = factory(Log::class)->create();

        $response = $this->json('DELETE', 'api/logs/' . $log->id);

        $response->assertStatus(401)
            ->assertJsonFragment([
                'Unauthorized'
            ]);
    }

}
