<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use App\Events\AchivementUnlocked;
use App\Listeners\AchivementUnlockedListener;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Comment;
use App\Events\BadgeUnlocked;

class D_AchivementListenerTest extends TestCase
{
    use RefreshDatabase;
    private User $user;

    public function setUp(): void{
        parent::setUp();
        Event::fake();
        $this->user = User::factory()->create();
        Lesson::factory(50)->create();
    }

    private function triggerListener($achievement_name){
        $event = new AchivementUnlocked($achievement_name, $this->user);     
        (new AchivementUnlockedListener())->handle($event);
    }

    public function test_basic_comment_config(): void
    {
        Event::assertListening(
            AchivementUnlocked::class,
            AchivementUnlockedListener::class,            
        );
    }

    public function test_three_achivements(){

        /**
         * 5 Lessons = 2 achivements
         */

        $lessons = Lesson::inRandomOrder()->limit(5)->get();

        foreach($lessons as $lesson){
            $this->user->lessons()->attach([$lesson->id=>['watched'=>true]]);
        }

        /**
         * 1 Comment = 1 achivements
         */

        Comment::factory(1)->create(['user_id'=>$this->user->id]);

        $this->triggerListener('First Comment Written'); // Last comment

        Event::assertNotDispatched(BadgeUnlocked::class);

    }

    public function test_four_achivements(){

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

        $this->triggerListener('3 Comments Written'); // Last comment

        Event::assertDispatched(function (BadgeUnlocked $event)  {
            return $event->badge_name === 'Intermediate';
        });

    }

    public function test_six_achivements(){

        /**
         * 1 Lessons = 1 achivements
         */

        $lessons = Lesson::inRandomOrder()->limit(1)->get();

        foreach($lessons as $lesson){
            $this->user->lessons()->attach([$lesson->id=>['watched'=>true]]);
        }

        /**
         * 20 Comment = 5 achivements
         */

        Comment::factory(20)->create(['user_id'=>$this->user->id]);

        $this->triggerListener('20 Comments Written'); // Last comment

        Event::assertNotDispatched(BadgeUnlocked::class);

    }

    public function test_eight_achivements(){

        /**
         * 25 Lessons = 4 achivements
         */

        $lessons = Lesson::inRandomOrder()->limit(25)->get();

        foreach($lessons as $lesson){
            $this->user->lessons()->attach([$lesson->id=>['watched'=>true]]);
        }

        /**
         * 10 Comments = 4 achivements
         */

        Comment::factory(10)->create(['user_id'=>$this->user->id]);

        $this->triggerListener('10 Comments Written'); // Last comment

        Event::assertDispatched(function (BadgeUnlocked $event)  {
            return $event->badge_name === 'Advanced';
        });

    }

    public function test_ten_achivements(){

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

        $this->triggerListener('20 Comments Written'); // Last comment

        Event::assertDispatched(function (BadgeUnlocked $event)  {
            return $event->badge_name === 'Master';
        });

    }
}
