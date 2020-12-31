<?php

namespace Dmdnkv\Faker\Provider;

class LoremFlickr extends Base
{
    /**
     * Available filter values
     */
    const FILTER_GRAY = 'g';
    const FILTER_RED = 'red';
    const FILTER_GREEN = 'green';
    const FILTER_BLUE = 'blue';

    /**
     * Image options
     */
    const OPTION_FILTER = 'filter';
    const OPTION_KEYWORDS = 'keywords';
    const OPTION_LOGIC = 'logic';

    /**
     * Available keywords logic
     */
    const KEYWORDS_LOGIC_AND = 'and';
    const KEYWORDS_LOGIC_OR = 'or';

    /**
     * Max resolution (W * H)
     * when this provider was written the service loremflickr.com shows error
     * on higher resolution than specified in this constant
     */
    const MAX_IMAGE_RESOLUTION = 47400000;

    /**
     * @var string
     */
    protected $baseUrl = 'https://loremflickr.com';
    /**
     * @var bool|int
     */
    protected $limitImageResolution = self::MAX_IMAGE_RESOLUTION;
    /**
     * @var array
     */
    protected $validFilters = [
        self::FILTER_GRAY,
        self::FILTER_RED,
        self::FILTER_GREEN,
        self::FILTER_BLUE,
    ];

    /**
     * Checks whether filter is supported by
     *
     * @param string $filter
     * @return bool
     */
    protected function validateFilter($filter)
    {
        if (!in_array($filter, $this->validFilters)) {
            throw new \InvalidArgumentException("Invalid filter {$filter}");
        }
    }

    /**
     * Builds image url for loremflickr service
     *
     * @param string $filter
     * @param int $width
     * @param int $height
     * @param array ...$args
     * @return string
     */
    protected function buildImageUrl($filter, $width, $height, ...$args)
    {
        $urlParts = [$this->baseUrl];

        // filter

        if ($filter) {
            $this->validateFilter($filter);

            $urlParts[] = $filter;
        }


        // resolution

        $this->validateResolution($width, $height);
        $urlParts[] = $width;
        $urlParts[] = $height;


        // keywords

        if (!empty($args)) {
            $allKeywords = false;

            if (count($args) == 1 && is_array($args[0])) {
                $keywordsList = $args[0];
                $allKeywords = true;
            } else {
                $keywordsList = $args;
            }

            $urlParts[] = implode(',', $keywordsList);

            if ($allKeywords) {
                $urlParts[] = 'all';
            }
        }


        // format url

        return implode('/', $urlParts);
    }

    /**
     * Generates or gets some image url somewhere
     *
     * @param int $width
     * @param int $height
     * @param array $options ['filter' => 'g', 'keywords' => [], 'logic' => 'and']
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
     * @return string
     */
    public function someImageUrl($width, $height, $options = [])
    {
        $filter = $this->readOption($options, self::OPTION_FILTER, false);
        $logic = $this->readOption($options, self::OPTION_LOGIC, self::KEYWORDS_LOGIC_OR);
        $keywords = $this->readOption($options, self::OPTION_KEYWORDS, false);

        if ($keywords) {
            if (!is_array($keywords)) {
                $keywords = [$keywords];
            }

            if ($logic == self::KEYWORDS_LOGIC_OR) {
                $url = $this->buildImageUrl($filter, $width, $height, ...$keywords);
            } elseif ($logic == self::KEYWORDS_LOGIC_AND) {
                $url = $this->buildImageUrl($filter, $width, $height, $keywords);
            } else {
                throw new \InvalidArgumentException("Unsupported logic option value {$logic}");
            }
        } else {
            $url = $this->buildImageUrl($filter, $width, $height);
        }

        return $url;
    }
}