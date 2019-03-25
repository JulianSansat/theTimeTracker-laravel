<?php

namespace Tests\Feature;

use App\Log;
use App\Team;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogFiltersTest extends TestCase
{

    public function testFilterByUser()
    {
        $log = factory(Log::class)->create([
            'user_id' => $this->userAuthorized->id
        ]);

        $log = factory(Log::class)->create([
            'user_id' => $this->userAuthorized->id + 1
        ]);

        $response = $this->json('GET', 'api/logs?user_id=1');
        $response->assertStatus(200)
            ->assertJsonFragment([
                'total' => 1
            ]);
    }

    public function testFilterByTeam(){
        $firstTeam = factory(Team::class)->create();

        factory(Team::class, 5)->create();

        $this->userAuthorized->teams()->attach($firstTeam);

        $log = factory(Log::class)->create([
            'user_id' => $this->userAuthorized->id
        ]);

        $response = $this->json('GET', 'api/logs?team_id='.$firstTeam->id );
        $response->assertStatus(200)
            ->assertJsonFragment([
                'total' => 1
            ]);
    }

    public function testFilterByDate(){
        $specificDateLog = factory(Log::class)->create();

        $createDate = new \DateTime($specificDateLog->start);

        $dateToSearch = $createDate->format('Y-m-d');

        factory(Log::class, 5)->create([
            'start' => date('Y-m-d H:i:s',strtotime('+2 days',strtotime(date("Y-m-d H:i:s"))))
        ]);

        factory(Log::class, 2)->create([
            'start' => date('Y-m-d H:i:s',strtotime('-2 days',strtotime(date("Y-m-d H:i:s"))))
        ]);

        $response = $this->json('GET', 'api/logs?date='.$dateToSearch );

        $response->assertStatus(200)
            ->assertJsonFragment([
                'total' => 1
            ]);
    }

    public function testFilterByDateInterval(){
        $specificDateLog = factory(Log::class)->create();

        $createDate = new \DateTime($specificDateLog->start);

        $startDate = $createDate->format('Y-m-d');

        $nextDate = date('Y-m-d H:i:s',strtotime('+2 days',strtotime($specificDateLog->start)));

        $nextDateFormatted = new \DateTime($nextDate);

        $endDate = $nextDateFormatted->format('Y-m-d');

        factory(Log::class, 2)->create([
            'start' => $nextDate
        ]);

        factory(Log::class, 2)->create([
            'start' => date('Y-m-d H:i:s',strtotime('+4 days',strtotime($nextDate)))
        ]);

        $response = $this->json('GET', 'api/logs?start_date='.$startDate.'&end_date='.$endDate.' 23:59:59' );

        $response->assertStatus(200)
            ->assertJsonFragment([
                'total' => 3
            ]);
    }
}
