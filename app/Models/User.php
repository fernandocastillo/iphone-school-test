<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Services\Achbad;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The comments that belong to the user.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The lessons that a user has access to.
     */
    public function lessons()
    {
        return $this->belongsToMany(Lesson::class);
    }

    /**
     * The lessons that a user has watched.
     */
    public function watched()
    {
        return $this->belongsToMany(Lesson::class)->wherePivot('watched', true);
    }

    public function commentAchivements(){
        return Achbad::calculate(config('iphoneschool.achivements.comments'), $this->comments()->count());
    }

    public function lessonAchivements(){
        return Achbad::calculate(config('iphoneschool.achivements.lessons'), $this->lessons()->distinct('id')->wherePivot('watched',true)->count());
    }

    public function calculate(){
        list($exactComments,$currentComments, $beforeComments, $nextComments)  = $this->commentAchivements();
        list($exactLessons,$currentLessons, $beforeLessons, $nextLessons)  = $this->lessonAchivements();

        $total = count($beforeComments) + count($beforeLessons) + ( $exactComments ? 1 : 0) + ( $exactLessons ? 1 : 0);

        $badge = collect(config('iphoneschool.badges'))->last(function($item) use($total){
            return $item['count'] <= $total;
        });

        $nextBadge = collect(config('iphoneschool.badges'))->first(function($item) use($total){
            return $item['count'] > $total;
        });

        $nextAvailableAchivements = [];
        $helperTextLessons = config('iphoneschool.stringify.lessons');
        $helperTextComments = config('iphoneschool.stringify.comments');

        if(count($nextLessons)>0){            
            $nextLesson = Achbad::stringify($nextLessons[0], $helperTextLessons['singular'], $helperTextLessons['plural'],$helperTextLessons['action']);
            $nextAvailableAchivements[] = $nextLesson;
        }

        if(count($nextComments)>0){            
            $nextComment = Achbad::stringify($nextComments[0], $helperTextComments['singular'], $helperTextComments['plural'],$helperTextComments['action']);
            $nextAvailableAchivements[] = $nextComment;
        }

        $remainingNextBadge = 0;
        if($nextBadge){
            $remainingNextBadge = $nextBadge['count'] - $total;
        }

        $unlockedComments = collect([...$beforeComments, $exactComments])->map(function($item) use($helperTextComments){
            return Achbad::stringify($item, $helperTextComments['singular'], $helperTextComments['plural'],$helperTextComments['action']);
        })->toArray();

        if(!count($beforeComments) && !$exactComments) $unlockedComments=[];

        $unlockedLessons = collect([...$beforeLessons, $exactLessons])->map(function($item) use($helperTextLessons){
            return Achbad::stringify($item, $helperTextLessons['singular'], $helperTextLessons['plural'],$helperTextLessons['action']);
        })->toArray();

        if(!count($beforeLessons) && !$exactLessons) $unlockedLessons=[];

        return [
            $total,
            $badge ? $badge['name'] : null,
            $nextBadge ? $nextBadge['name'] : null,
            $nextAvailableAchivements,
            $remainingNextBadge,
            array_merge($unlockedLessons, $unlockedComments)
        ];
    }

    
}

