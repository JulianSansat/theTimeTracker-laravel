<?php

namespace Tests\Unit;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class UserTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

    }

    public function testlogIn()
    {
    	$this->loginArray = [
    		'email' => $this->userAuthorized->email,
    		'password' => '123456'
    	];
    	$response = $response = $this->json('POST', 'api/auth/login', $this->loginArray);

    	$response->assertStatus(200)
            ->assertJsonFragment([
                'token_type' => 'bearer',
            ]);
    }
}
