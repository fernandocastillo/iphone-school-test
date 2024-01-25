<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use App\Listeners\CommentWrittenListener;
use App\Events\CommentWritten;
use App\Events\AchivementUnlocked;
use App\Models\User;
use App\Models\Comment;


class B_CommentWrittenTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_basic_comment_config(): void
    {
        Event::fake();

        Event::assertListening(
            CommentWritten::class,
            CommentWrittenListener::class,            
        );
    }

    public function test_first_comment(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id'=>$user->id]);

        CommentWritten::dispatch($comment);        

        Event::assertDispatched(function (AchivementUnlocked $event)  {
            return $event->achievement_name === 'First Comment Written';
        });
    }
}
