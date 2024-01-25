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
        list($exact, $current, $before, $after, $remainToNext)  = Achbad::calculate(config('iphoneschool.achivements.lessons'), $event->user->lessons()->distinct('id')->wherePivot('watched',true)->count());
        if($exact){
            $name = Achbad::stringify($exact, 'Lesson', 'Lessons','Watched');
            AchivementUnlocked::dispatch($name, $event->user);            
        }
    }
}