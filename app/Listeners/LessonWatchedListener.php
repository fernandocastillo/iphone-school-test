<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\LessonWatched;
use App\Services\Achbad;
use App\Events\AchivementUnlocked;

class LessonWatchedListener
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
    public function handle(LessonWatched $event): void
    {
        list($exact, $current, $before, $after, $remainToNext)  = $event->user->lessonAchivements();
        if($exact){
            $helperText = config('iphoneschool.stringify.lessons');
            $name = Achbad::stringify($exact, $helperText['singular'], $helperText['plural'],$helperText['action']);            
            AchivementUnlocked::dispatch($name, $event->user);            
        }
    }
}
