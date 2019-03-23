<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected $userAuthorized;

    public function setUp()
    {
    	parent::setUp();

    	$this->userAuthorized = factory(User::class)->create();
    }
}
