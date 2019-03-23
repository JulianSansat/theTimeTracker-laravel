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

    }
    public function testRegistration()
    {
        $userArray = factory(User::class)
            ->states('with_plain_password')
            ->make()
            ->toArray();

        $userArray['password'] = '123456';
        $userArray['password_confirmation'] = '123456';

        $response = $this->json('POST', 'api/auth/register', $userArray);

        $response->assertStatus(201);
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
