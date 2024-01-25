<?php
namespace App\Services;

/**
 * Achbad.php
 *
 * Calculate achivements and badges
 * 
 * @author     Fernando Castillo
 * @copyright  2024 Fernando Castillo
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * 
 */

class Achbad {

    static function calculate(array $limits, int $value): array{
        $exact=false;
        $before = [];
        $next = [];
        $current=false;
        $remainToNext=0;

        foreach($limits as $limit){
            if($value>= $limit) $current = $limit;

            if ($value > $limit ) $before[] = $limit;

            if ($value < $limit ) $next[] = $limit;

            if($limit===$value) $exact = $limit;
        }

        if(count($next)>0) $remainToNext = $next[0] - $value;

        return [$exact, $current, $before, $next, $remainToNext];
    }

    static function stringify( $value, string $singular, string $plural, string $fillAtTheEnd){

        if(is_array($value)){
            $results = [];
            foreach($value as $v){
                $results[] = self::text($v, $singular, $plural, $fillAtTheEnd);
            }
        }else{
            return self::text($value, $singular, $plural, $fillAtTheEnd);
        }

    }

    private static function text($value, $singular, $plural, $fillAtTheEnd){
        $separator = ' ';
        $txt = $value . $separator;
        if($value<=1) $txt .= $singular . $separator;
        if($value>1) $txt .= $plural . $separator;
        return $txt . $fillAtTheEnd;
    }
}