<?php

namespace Tests\Unit;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Usergroup;

class UserGroupTest extends TestCase
{
    protected $trashedGroup, $groupsWithTrashed, $groups;

    public function setUp()
    {
        parent::setUp();

        $this->trashedGroup = factory(Usergroup::class)->create([
            'deleted_at' => date("Y-m-d H:i:s")
        ]);

        $this->groupsWithTrashed = Usergroup::withTrashed()->count();
        $this->groups = Usergroup::count();
    }

    public function testIndexWithPermissions()
    {
        $this->actingAs($this->userAuthorized, 'api');

        $response = $this->json('GET', 'api/usergroups');
        $response->assertStatus(200)
            ->assertJsonFragment([
                'current_page'   => 1,
                'total'          => $this->groupsWithTrashed
            ]);
    }

    public function testIndexWithoutPermissions()
    {
        $this->actingAs($this->userUnauthorized, 'api');

        $response = $this->json('GET', 'api/usergroups');

        $response->assertStatus(403)
            ->assertJsonFragment([
            'Forbidden'
        ]);

    }

    public function testIndexAsGuest()
    {
        $response = $this->json('GET', 'api/usergroups');

        $response->assertStatus(401)
            ->assertJsonFragment([
                'Unauthorized'
            ]);
    }

    public function testShowWithPermissions()
    {
        $this->actingAs($this->userAuthorized, 'api');

        $response = $this->json('GET', 'api/usergroups/' . $this->trashedGroup->id);
        $response->assertStatus(200)
            ->assertJsonFragment([
                'name'  =>  $this->trashedGroup->name
            ]);
    }

    public function testShowWithoutPermissions()
    {
        $this->actingAs($this->userUnauthorized, 'api');

        $firstGroup = Usergroup::first();

        $response = $this->json('GET', 'api/usergroups/' . $firstGroup->id);

        $responseTrashedRecord = $this->json('GET', 'api/usergroups/' . $this->trashedGroup->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name'  =>  $firstGroup->name
            ]);

        $responseTrashedRecord->assertStatus(403)
            ->assertJsonFragment([
                'Forbidden'
            ]);
    }

    public function testShowAsGuest()
    {
        $firstGroup = Usergroup::first();

        $response = $this->json('GET', 'api/usergroups/' . $firstGroup->id);

        $response->assertStatus(401)
            ->assertJsonFragment([
                'Unauthorized',
            ]);
    }

    public function testStoreWithPermissions()
    {
        $this->actingAs($this->userAuthorized, 'api');

        $usergroup = factory(Usergroup::class)->make();
        
        $response = $this->json('POST', 'api/usergroups', $usergroup->toArray());

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => $usergroup->name
            ]);
    }

    public function testStoreWithoutPermissions()
    {
        $this->actingAs($this->userUnauthorized, 'api');

        $usergroup = factory(Usergroup::class)->make();

        $response = $this->json('POST', 'api/usergroups', $usergroup->toArray());

        $response->assertStatus(403)
            ->assertJsonFragment([
                'Forbidden'
            ]);
    }

    public function testStoreAsGuest()
    {
        $usergroup = factory(Usergroup::class)->make();

        $response = $this->json('POST', 'api/usergroups', $usergroup->toArray());

        $response->assertStatus(401)
            ->assertJsonFragment([
                'Unauthorized',
            ]);
    }

    public function testUpdateWithPermissions()
    {
        $this->actingAs($this->userAuthorized, 'api');

        $usergroup = factory(Usergroup::class)->create();

        $usergroup->name = "updated name";

        $response = $this->json('PUT', 'api/usergroups/'. $usergroup->id, $usergroup->toArray());

        $response->assertStatus(201);

        $updatedUserGroup = Usergroup::find($usergroup->id);

        $this->assertEquals($updatedUserGroup->name, $usergroup->name);
    }

    public function testUpdateWithoutPermissions()
    {
        $this->actingAs($this->userUnauthorized, 'api');

        $usergroup = factory(Usergroup::class)->create();

        $usergroup->name = "updated name";

        $response = $this->json('PUT', 'api/usergroups/'. $usergroup->id, $usergroup->toArray());

        $response->assertStatus(403)
            ->assertJsonFragment([
                'Forbidden'
            ]);
    }

    public function testUpdateAsGuest()
    {
        $usergroup = factory(Usergroup::class)->create();

        $usergroup->name = "updated name";

        $response = $this->json('PUT', 'api/usergroups/'. $usergroup->id, $usergroup->toArray());

        $response->assertStatus(401)
            ->assertJsonFragment([
                'Unauthorized',
            ]);
    }

    public function testDestroyWithPermissions()
    {
        $this->actingAs($this->userAuthorized, 'api');

        $usergroup = factory(Usergroup::class)->create();

        $response = $this->json('DELETE', 'api/usergroups/' . $usergroup->id);

        $response->assertStatus(204);

        $createdUsergroup = Usergroup::withTrashed()->find($usergroup->id);

        $this->assertNotNull($createdUsergroup->deleted_at);
    }

    public function testDestroyWithoutPermissions()
    {
        $this->ActingAs($this->userUnauthorized, 'api');

        $usergroup = factory(Usergroup::class)->create();

        $response = $this->json('DELETE', 'api/usergroups/' . $usergroup->id);

        $response->assertStatus(403)
            ->assertJsonFragment([
                'Forbidden'
            ]);
        
        $createdUsergroup = Usergroup::find($usergroup->id);

        $this->assertNull($createdUsergroup->deleted_at);
    }

    public function testDestroyAsGuest()
    {
        $usergroup = factory(Usergroup::class)->create();

        $response = $this->json('DELETE', 'api/usergroups/' . $usergroup->id);

        $response->assertStatus(401)
            ->assertJsonFragment([
                'Unauthorized',
            ]);
        
        $createdUsergroup = Usergroup::find($usergroup->id);

        $this->assertNull($createdUsergroup->deleted_at);
    }
}