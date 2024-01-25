<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\Events\LessonWatched;
use App\Listeners\LessonWatchedListener;
use App\Models\User;
use App\Models\Lesson;
use App\Events\AchivementUnlocked;

class C_LessonWatchedTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void{
        parent::setUp();
        Event::fake();
        $this->user = User::factory()->create();
        Lesson::factory(50)->create();
    }

    private function triggerListener(Lesson $lesson){
        $event = new LessonWatched($lesson, $this->user);     
        (new LessonWatchedListener())->handle($event);
    }

    public function test_basic_comment_config(): void
    {
        Event::assertListening(
            LessonWatched::class,
            LessonWatchedListener::class,            
        );
    }

    public function test_first_watched_lesson(): void
    {

        $lesson = Lesson::inRandomOrder()->first();
        $this->user->lessons()->attach([$lesson->id=>['watched'=>true]]);

        $this->triggerListener($lesson);

        Event::assertDispatched(function (AchivementUnlocked $event)  {
            return $event->achievement_name === 'First Lesson Watched';
        });
    }

    public function test_first_unwatched_lesson(): void
    {

        $lesson = Lesson::inRandomOrder()->first();
        $this->user->lessons()->attach([$lesson->id=>['watched'=>false]]);

        $this->triggerListener($lesson);

        Event::assertNotDispatched(AchivementUnlocked::class);
    }

    public function test_five_repeated_watched_lesson(): void
    {

        $lesson = Lesson::inRandomOrder()->first();

        for($i=1; $i<=5; $i++){
            $this->user->lessons()->attach([$lesson->id=>['watched'=>true]]);
        }                

        $this->assertDatabaseCount('lesson_user',5);

        $this->triggerListener($lesson);

        Event::assertDispatched(function (AchivementUnlocked $event)  {            
            return $event->achievement_name === 'First Lesson Watched';
        });
    }

    public function test_five_unique_watched_lesson(): void
    {

        $lessons = Lesson::inRandomOrder()->limit(5)->get();

        foreach($lessons as $lesson){
            $this->user->lessons()->attach([$lesson->id=>['watched'=>true]]);
        }                

        $this->assertDatabaseCount('lesson_user',5);

        $this->triggerListener($lesson);

        Event::assertDispatched(function (AchivementUnlocked $event)  {            
            return $event->achievement_name === '5 Lessons Watched';
        });
    }

    public function test_nine_unique_watched_lesson(): void
    {

        $lessons = Lesson::inRandomOrder()->limit(9)->get();

        foreach($lessons as $lesson){
            $this->user->lessons()->attach([$lesson->id=>['watched'=>true]]);
        }                

        $this->assertDatabaseCount('lesson_user',9);

        $this->triggerListener($lesson);

        Event::assertNotDispatched(AchivementUnlocked::class);
    }

    public function test_fifty_unique_watched_lesson(): void
    {

        $lessons = Lesson::inRandomOrder()->limit(50)->get();

        foreach($lessons as $lesson){
            $this->user->lessons()->attach([$lesson->id=>['watched'=>true]]);
        }                

        $this->assertDatabaseCount('lesson_user',50);

        $this->triggerListener($lesson);

        Event::assertDispatched(function (AchivementUnlocked $event)  {            
            return $event->achievement_name === '50 Lessons Watched';
        });
    }
}
