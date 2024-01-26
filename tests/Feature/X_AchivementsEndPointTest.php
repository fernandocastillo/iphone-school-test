<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Lesson;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class X_AchivementsEndPointTest extends TestCase
{
    use RefreshDatabase;
    private User $user;
    private $structure = [
        'unlocked_achievements',
        'next_available_achievements',
        'current_badge',
        'next_badge',
        'remaining_to_unlock_next_badge'
    ];

    public function setUp(): void{
        parent::setUp();        
        $this->user = User::factory()->create();
        Lesson::factory(50)->create();
    }

    public function test_api_response_as_begginer_with_zero_achivements(): void
    {
        $this
            ->getJson("/users/{$this->user->id}/achievements")
            ->assertStatus(200)
            ->assertJsonStructure($this->structure)
            ->assertJsonFragment([
                'unlocked_achievements'=>[],
                'next_available_achievements' => [
                    'First Lesson Watched',
                    'First Comment Written'
                ],
                'current_badge' => 'Beginner',
                'next_badge' => 'Intermediate',
                'remaining_to_unlock_next_badge' => 4
            ]);
    }


    public function test_api_response_as_beginner_with_three_achivements(): void
    {        

        /**
         * 1 Lessons = 1 achivements
         */

         $lessons = Lesson::inRandomOrder()->limit(1)->get();

         foreach($lessons as $lesson){
             $this->user->lessons()->attach([$lesson->id=>['watched'=>true]]);
         }
 
         /**
          * 3 Comments = 2 achivements
          */
 
         Comment::factory(3)->create(['user_id'=>$this->user->id]);

        $this
            ->getJson("/users/{$this->user->id}/achievements")
            ->assertStatus(200)
            ->assertJsonStructure($this->structure)
            ->assertJsonFragment([
                'unlocked_achievements'=>[
                    'First Lesson Watched',                    
                    'First Comment Written',
                    '3 Comments Written'
                ],
                'next_available_achievements' => [
                    '5 Lessons Watched',
                    '5 Comments Written'
                ],
                'current_badge' => 'Beginner',
                'next_badge' => 'Intermediate',
                'remaining_to_unlock_next_badge' => 1
            ]);                
    }

    
    public function test_api_response_as_intermediate_with_four_achivements(): void
    {        

        /**
         * 5 Lessons = 2 achivements
         */

         $lessons = Lesson::inRandomOrder()->limit(5)->get();

         foreach($lessons as $lesson){
             $this->user->lessons()->attach([$lesson->id=>['watched'=>true]]);
         }
 
         /**
          * 3 Comments = 2 achivements
          */
 
         Comment::factory(3)->create(['user_id'=>$this->user->id]);

        $this
            ->getJson("/users/{$this->user->id}/achievements")
            ->assertStatus(200)
            ->assertJsonStructure($this->structure)
            ->assertJsonFragment([
                'unlocked_achievements'=>[
                    'First Lesson Watched',
                    '5 Lessons Watched',
                    'First Comment Written',
                    '3 Comments Written'
                ],
                'next_available_achievements' => [
                    '10 Lessons Watched',
                    '5 Comments Written'
                ],
                'current_badge' => 'Intermediate',
                'next_badge' => 'Advanced',
                'remaining_to_unlock_next_badge' => 4
            ]);                
    }

    public function test_api_response_as_intermediate_with_seven_achivements(): void
    {        

        /**
         * 50 Lessons = 5 achivements
         */

         $lessons = Lesson::inRandomOrder()->limit(50)->get();

         foreach($lessons as $lesson){
             $this->user->lessons()->attach([$lesson->id=>['watched'=>true]]);
         }
 
         /**
          * 3 Comments = 2 achivements
          */
 
         Comment::factory(3)->create(['user_id'=>$this->user->id]);

        $this
            ->getJson("/users/{$this->user->id}/achievements")
            ->assertStatus(200)
            ->assertJsonStructure($this->structure)
            ->assertJsonFragment([
                'unlocked_achievements'=>[
                    'First Lesson Watched',
                    '5 Lessons Watched',
                    '10 Lessons Watched',
                    '25 Lessons Watched',
                    '50 Lessons Watched',                    
                    'First Comment Written',
                    '3 Comments Written'
                ],
                'next_available_achievements' => [
                    '5 Comments Written'
                ],
                'current_badge' => 'Intermediate',
                'next_badge' => 'Advanced',
                'remaining_to_unlock_next_badge' => 1
            ]);                
    }

    public function test_api_response_as_advanced_with_nine_achivements(): void
    {        

        /**
         * 50 Lessons = 5 achivements
         */

         $lessons = Lesson::inRandomOrder()->limit(50)->get();

         foreach($lessons as $lesson){
             $this->user->lessons()->attach([$lesson->id=>['watched'=>true]]);
         }
 
         /**
          * 10 Comments = 4 achivements
          */
 
         Comment::factory(10)->create(['user_id'=>$this->user->id]);

        $this
            ->getJson("/users/{$this->user->id}/achievements")
            ->assertStatus(200)
            ->assertJsonStructure($this->structure)
            ->assertJsonFragment([
                'unlocked_achievements'=>[
                    'First Lesson Watched',
                    '5 Lessons Watched',
                    '10 Lessons Watched',
                    '25 Lessons Watched',
                    '50 Lessons Watched',                    
                    'First Comment Written',
                    '3 Comments Written',
                    '5 Comments Written',
                    '10 Comments Written'
                ],
                'next_available_achievements' => [
                    '20 Comments Written'
                ],
                'current_badge' => 'Advanced',
                'next_badge' => 'Master',
                'remaining_to_unlock_next_badge' => 1
            ]);                
    }

    public function test_api_response_as_master_with_ten_achivements(): void
    {        

        /**
         * 50 Lessons = 5 achivements
         */

         $lessons = Lesson::inRandomOrder()->limit(50)->get();

         foreach($lessons as $lesson){
             $this->user->lessons()->attach([$lesson->id=>['watched'=>true]]);
         }
 
         /**
          * 20 Comments = 5 achivements
          */
 
         Comment::factory(20)->create(['user_id'=>$this->user->id]);

        $this
            ->getJson("/users/{$this->user->id}/achievements")
            ->assertStatus(200)
            ->assertJsonStructure($this->structure)
            ->assertJsonFragment([
                'unlocked_achievements'=>[
                    'First Lesson Watched',
                    '5 Lessons Watched',
                    '10 Lessons Watched',
                    '25 Lessons Watched',
                    '50 Lessons Watched',                    
                    'First Comment Written',
                    '3 Comments Written',
                    '5 Comments Written',
                    '10 Comments Written',
                    '20 Comments Written'
                ],
                'next_available_achievements' => [],
                'current_badge' => 'Master',
                'next_badge' => null,
                'remaining_to_unlock_next_badge' => 0
            ]);                
    }
}
