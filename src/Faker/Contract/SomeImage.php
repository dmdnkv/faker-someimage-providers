<?php

namespace Dmdnkv\Faker\Contract;

interface SomeImage
{
    /**
     * Generates or gets some image url somewhere
     *
     * @param int $width
     * @param int $height
     * @param array $options
     * @return string
     */
    function someImageUrl($width, $height, $options = []);

    /**
     * Generates or gets some image somewhere and save it
     *
     * @param int $width
     * @param int $height
     * @param array $options
     * @return string full path or name of saved image
     */
    function someImage($width, $height, $options = []);
}