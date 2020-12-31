<?php

namespace Dmdnkv\Faker\Provider;

use Dmdnkv\Faker\Contract\SomeImage;

class DummyImage extends Base implements SomeImage
{
    /**
     * Image options
     */
    const OPTION_BG_COLOR = 'bg';
    const OPTION_FG_COLOR = 'fg';
    const OPTION_TEXT = 'text';

    /**
     * Default colors
     */
    const DEFAULT_BG_COLOR = '0';
    const DEFAULT_FG_COLOR = 'f';

    /**
     * Max resolution (W * H)
     * when this provider was written the service dummyimage.com shows error
     * on higher resolution than specified in this constant
     */
    const MAX_IMAGE_RESOLUTION = 15992001;

    /**
     * @var string
     */
    protected $baseUrl = 'https://dummyimage.com';
    /**
     * @var bool|int
     */
    protected $limitImageResolution = self::MAX_IMAGE_RESOLUTION;

    /**
     * Builds image url for dummyimage service
     *
     * @param int $width
     * @param int $height
     * @param string $bgColor
     * @param string $fgColor
     * @param string $extension
     * @param string $text
     * @return string
     */
    protected function buildImageUrl($width, $height, $bgColor, $fgColor, $extension, $text)
    {
        $urlParts = [$this->baseUrl];

        $this->validateResolution($width, $height);
        $urlParts[] = "{$width}x{$height}";

        $this->validateColor($bgColor);
        $urlParts[] = $bgColor;

        $this->validateColor($fgColor);
        $urlParts[] = $fgColor;

        $url = implode('/', $urlParts);

        if ($extension) {
            $this->validateExtensions($extension);

            $url .= ".{$extension}";
        }

        if ($text) {
            $url .= '&';
            $url .= http_build_query(['text' => urlencode($text)]);
        }

        return $url;
    }

    /**
     * Generates or gets some image url somewhere
     *
     * @param int $width
     * @param int $height
     * @param array $options
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
     * @return string
     */
    public function someImageUrl($width, $height, $options = [])
    {
        $bgColor = $this->readOption($options, self::OPTION_BG_COLOR, self::DEFAULT_BG_COLOR);
        $fgColor = $this->readOption($options, self::OPTION_FG_COLOR, self::DEFAULT_FG_COLOR);
        $text = $this->readOption($options, self::OPTION_TEXT, false);
        $extension = $this->readOption($options, self::OPTION_EXTENSION, false);

        return $this->buildImageUrl($width, $height, $bgColor, $fgColor, $extension, $text);
    }
}