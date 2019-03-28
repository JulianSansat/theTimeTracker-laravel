<?php

namespace Tests\Unit;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\User;

class UserTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->trashedUser = factory(User::class)->create([
            'deleted_at' => date("Y-m-d H:i:s")
        ]);

        $this->usersWithTrashed = User::withTrashed()->count();
        $this->users = User::count();
    }

    public function testIndexWithPermissions()
    {
        $this->actingAs($this->userAuthorized, 'api');

        $response = $this->json('GET', 'api/users');
        $response->assertStatus(200)
            ->assertJsonFragment([
                'current_page'   => 1,
                'total'          => $this->usersWithTrashed
            ]);
    }

    public function testIndexWithoutPermissions()
    {
        $this->actingAs($this->userUnauthorized, 'api');

        $response = $this->json('GET', 'api/users');

        $response->assertStatus(403)
            ->assertJsonFragment([
            'Forbidden'
        ]);

    }

    public function testIndexAsGuest()
    {
        $response = $this->json('GET', 'api/users');

        $response->assertStatus(401)
            ->assertJsonFragment([
                'Unauthorized'
            ]);
    }

    public function testShowWithPermissions()
    {
        $this->actingAs($this->userAuthorized, 'api');

        $response = $this->json('GET', 'api/users/' . $this->trashedUser->id);
        $response->assertStatus(200)
            ->assertJsonFragment([
                'first_name'  =>  $this->trashedUser->first_name
            ]);
    }

    public function testShowWithoutPermissions()
    {
        $this->actingAs($this->userUnauthorized, 'api');

        $firstUser = User::first();

        $response = $this->json('GET', 'api/users/' . $firstUser->id);

        $responseTrashedRecord = $this->json('GET', 'api/users/' . $this->trashedUser->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'first_name'  =>  $firstUser->first_name
            ]);

        $responseTrashedRecord->assertStatus(403)
            ->assertJsonFragment([
                'Forbidden'
            ]);
    }

    public function testShowAsGuest()
    {
        $firstUser = User::first();

        $response = $this->json('GET', 'api/users/' . $firstUser->id);

        $response->assertStatus(401)
            ->assertJsonFragment([
                'Unauthorized',
            ]);
    }

    public function testStoreWithPermissions()
    {
        $this->actingAs($this->userAuthorized, 'api');

        $userArray = factory(User::class)
            ->states('with_plain_password')
            ->make()
            ->toArray();

        $userArray['password'] = '123456';
        $userArray['password_confirmation'] = '123456';
        
        $response = $this->json('POST', 'api/users', $userArray);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'first_name' => $userArray['first_name']
            ]);
    }

    public function testStoreWithoutPermissions()
    {
        $this->actingAs($this->userUnauthorized, 'api');

        $userArray = factory(User::class)
            ->states('with_plain_password')
            ->make()
            ->toArray();

        $userArray['password'] = '123456';
        $userArray['password_confirmation'] = '123456';

        $response = $this->json('POST', 'api/users', $userArray);

        $response->assertStatus(403)
            ->assertJsonFragment([
                'Forbidden'
            ]);
    }

    public function testStoreAsGuest()
    {
        $userArray = factory(User::class)
            ->states('with_plain_password')
            ->make()
            ->toArray();

        $userArray['password'] = '123456';
        $userArray['password_confirmation'] = '123456';

        $response = $this->json('POST', 'api/users', $userArray);

        $response->assertStatus(401)
            ->assertJsonFragment([
                'Unauthorized',
            ]);
    }

    public function testUpdateWithPermissions()
    {
        $this->actingAs($this->userAuthorized, 'api');

        $userArray = factory(User::class)
            ->states('with_plain_password')
            ->make()
            ->toArray();

        $userArray['password'] = '123456';

        $user = factory(User::class)->create($userArray);

        $userArray['password_confirmation'] = '123456';

        $userArray['first_name'] = "updated name";

        $response = $this->json('PUT', 'api/users/'. $user->id, $userArray);

        $response->assertStatus(201);

        $updatedUser = User::find($user->id);

        $this->assertEquals($updatedUser->first_name, $userArray['first_name']);
    }

    public function testUpdateWithoutPermissions()
    {
        $this->actingAs($this->userUnauthorized, 'api');

        $user = factory(User::class)->create();

        $user->first_name = "updated name";

        $response = $this->json('PUT', 'api/users/'. $user->id, $user->toArray());

        $response->assertStatus(403)
            ->assertJsonFragment([
                'Forbidden'
            ]);
    }

    public function testUpdateAsGuest()
    {
        $user = factory(User::class)->create();

        $user->first_name = "updated name";

        $response = $this->json('PUT', 'api/users/'. $user->id, $user->toArray());

        $response->assertStatus(401)
            ->assertJsonFragment([
                'Unauthorized',
            ]);
    }

    public function testDestroyWithPermissions()
    {
        $this->actingAs($this->userAuthorized, 'api');

        $user = factory(User::class)->create();

        $response = $this->json('DELETE', 'api/users/' . $user->id);

        $response->assertStatus(204);

        $createdUser = User::withTrashed()->find($user->id);

        $this->assertNotNull($createdUser->deleted_at);
    }

    public function testDestroyWithoutPermissions()
    {
        $this->ActingAs($this->userUnauthorized, 'api');

        $user = factory(User::class)->create();

        $response = $this->json('DELETE', 'api/users/' . $user->id);

        $response->assertStatus(403)
            ->assertJsonFragment([
                'Forbidden'
            ]);
        
        $createdUser = User::find($user->id);

        $this->assertNull($createdUser->deleted_at);
    }

    public function testDestroyAsGuest()
    {
        $user = factory(User::class)->create();

        $response = $this->json('DELETE', 'api/users/' . $user->id);

        $response->assertStatus(401)
            ->assertJsonFragment([
                'Unauthorized',
            ]);
        
        $createdUser = User::find($user->id);

        $this->assertNull($createdUser->deleted_at);
    }
}