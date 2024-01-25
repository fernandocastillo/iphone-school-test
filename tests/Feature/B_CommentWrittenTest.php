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
    
    private User $user;
    
    public function setUp(): void{
        parent::setUp();
        Event::fake();
        $this->user = User::factory()->create();
    }

    private function triggerListener(Comment $comment){
        $event = new CommentWritten($comment, $this->user);     
        (new CommentWrittenListener())->handle($event);
    }
    
    public function test_basic_comment_config(): void
    {        
        Event::assertListening(
            CommentWritten::class,
            CommentWrittenListener::class,            
        );
    }

    public function test_first_comment(): void
    {
                
        $comment = Comment::factory()->create(['user_id'=>$this->user->id]);
        
        $this->triggerListener($comment);

        Event::assertDispatched(function (AchivementUnlocked $event)  {
            return $event->achievement_name === 'First Comment Written';
        });
    }

    public function test_two_comments(): void
    {        
        
        $comment = Comment::factory(2)->create(['user_id'=>$this->user->id])->last();
        
        $this->triggerListener($comment);

        Event::assertNotDispatched(AchivementUnlocked::class);
    }

    public function test_five_comments(): void
    {
        
        $comment = Comment::factory(5)->create(['user_id'=>$this->user->id])->last();
                
        $this->triggerListener($comment);
        
        Event::assertDispatched(function (AchivementUnlocked $event)  {
            return $event->achievement_name === '5 Comments Written';
        });
    }

    public function test_twenty_comments(): void
    {
        
        $comment = Comment::factory(20)->create(['user_id'=>$this->user->id])->last();
                
        $this->triggerListener($comment);
        
        Event::assertDispatched(function (AchivementUnlocked $event)  {
            return $event->achievement_name === '20 Comments Written';
        });
    }
}
