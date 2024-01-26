<?php

namespace App\Listeners;

use App\Events\CommentWritten;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\Achbad;
use App\Events\AchivementUnlocked;

class CommentWrittenListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CommentWritten $event): void
    {
        
        list($exact, $current, $before, $after, $remainToNext)  = $event->user->commentAchivements();
        
        if($exact){
            $helperText = config('iphoneschool.stringify.comments');
            $name = Achbad::stringify($exact, $helperText['singular'], $helperText['plural'],$helperText['action']);
            AchivementUnlocked::dispatch($name, $event->user);            
        }

    }    
}
