<?php

if (!function_exists('getRandomPhrase')) {
    /**
     * Returns a randomize string from array
     *
     * @return string a string in human readable format
     *
     * */
    function getRandomPhrase()
    {
        $arr = ['enabling reuse of trait data',
            'making your data long lived',
            'building the trait data infrastructure for the future'];
        return $arr[rand(0, 2)];
    }
}