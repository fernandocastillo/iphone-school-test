<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\AchivementUnlocked;
use App\Events\BadgeUnlocked;

class AchivementUnlockedListener
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
    public function handle(AchivementUnlocked $event): void
    {        
        list($total) = $event->user->calculate();
        $badge = collect(config('iphoneschool.badges'))->first(function($item) use($total){
            return $item['count'] == $total;
        });
        
        if($badge){
            BadgeUnlocked::dispatch($badge['name'],$event->user);
        }
    }
}
