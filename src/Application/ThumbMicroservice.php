<?php
/**
 * Created by PhpStorm.
 * User: Cainon
 * Date: 14.07.2017
 * Time: 11:18
 */

namespace Application;


class ThumbMicroservice
{
    public static function generateThumbUrlFromYoutubeId($youtubeId, $quality = 1)
    {
        return THUMB_HOST . '/film/' . $youtubeId . '/thumb/' . $quality;
    }

    public static function generateThumbUrlFromYoutubeUrl($url)
    {
        return self::generateThumbUrlFromYoutubeId(self::getYoutubeMovieIdFromUrl($url));
    }

    public static function getYoutubeMovieIdFromUrl($url)
    {
        //$url = "http://www.youtube.com/watch?v=C4kxS1ksqtw&feature=relate";
        parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
        return $my_array_of_vars['v'];
    }
}