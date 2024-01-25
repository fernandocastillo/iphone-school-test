<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class A_CommentAndLessonTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_basic_database_and_factories(): void
    {

        $user = User::factory()->create();
        $comments = Comment::factory(5)->create(['user_id'=>$user->id]);
        
        $this->assertDatabaseHas('users',(array) $user->only(['id','email','name']));
        foreach($comments as $comment){
            $verify = $comment->only(['body']);
            $verify['user_id'] = $user->id;
            $this->assertDatabaseHas('comments',$verify);
        }        

    }
}
