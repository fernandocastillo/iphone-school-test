<?php

return [

    'achivements' =>[
        'lessons'   => [1,5,10,25,50],
        'comments'  => [1,3,5,10,20]
    ],

    'badges' => [
        [
            'count' =>  0,
            'name'  =>  'Beginner'
        ],
        [
            'count' =>  4,
            'name'  =>  'Intermediate'
        ],
        [
            'count' =>  8,
            'name'  =>  'Advanced'
        ],
        [
            'count' =>  10,
            'name'  =>  'Master'
        ],        
    ],

    'stringify' => [
        'comments' =>   [
            'singular'  =>'Comment', 
            'plural'    =>'Comments',
            'action'    =>'Written'
        ],
        'lessons' =>   [
            'singular'  =>'Lesson', 
            'plural'    =>'Lessons',
            'action'    =>'Watched'
        ]
    ]

];