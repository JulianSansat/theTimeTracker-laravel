<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Usergroup;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected $userAuthorized, $userUnauthorized, $userGuest, $adminGroup, $withoutPermissionsGroup;

    public function setUp()
    {
        parent::setUp();

        $this->adminGroup = factory(Usergroup::class)->states('admin')
            ->create();

        $this->withoutPermissionsGroup = factory(Usergroup::class)->states('without_permissions')
            ->create();

        $this->userAuthorized = factory(User::class)->create([
            'usergroup_id' => $this->adminGroup->id,
        ]);

        $this->userUnauthorized = factory(User::class)->create([
            'usergroup_id' => $this->withoutPermissionsGroup->id,
        ]);
    }
}
