<?php

namespace Tests\Feature;

use App\User;
use App\Shift;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
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
        $response = $this->json('POST', 'api/auth/login', $this->loginArray);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'token_type' => 'bearer',
            ]);
    }

    public function testLoginShouldCreateShift()
    {
        $this->loginArray = [
            'email' => $this->userAuthorized->email,
            'password' => '123456'
        ];
        
        $this->json('POST', 'api/auth/login', $this->loginArray);

        $this->assertEquals(1 , $this->userAuthorized->shift()->count());
    }

    public function testLoginShouldNotCreateMoreThanOneShifts()
    {
        $this->loginArray = [
            'email' => $this->userAuthorized->email,
            'password' => '123456'
        ];

        $this->json('POST', 'api/auth/login', $this->loginArray);

        $this->json('POST', 'api/auth/login', $this->loginArray);

        $this->assertEquals(1 , $this->userAuthorized->shift()->count());
    }

    public function testLogoutShouldCreateLog()
    {
        $shift = new Shift([
            'start'   => date("Y-m-d H:i:s"),
            'user_id' => $this->userAuthorized->id,
        ]);

        $this->userAuthorized->shift()->save($shift);

        $this->actingAs($this->userAuthorized, 'api');

        $this->json('POST', 'api/auth/logout');

        $log = $this->userAuthorized->logs()->first();

        $this->assertEquals(
            $shift->start, $log->start
        );
    }
}
