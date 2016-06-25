<?php

use Dmdnkv\Faker\Provider\LoremFlickr;

class LoremFlickrTest extends PHPUnit_Framework_TestCase
{
    protected $faker;

    public function setUp()
    {
        $faker = new \Faker\Generator();
        $faker->addProvider(new LoremFlickr($faker));

        $this->faker = $faker;

        parent::setUp();
    }

    public function testBasics()
    {
        $this->assertEquals(
            'http://loremflickr.com/200/200/dog',
            $this->faker->someImageUrl(
                200,
                200,
                [
                    LoremFlickr::OPTION_KEYWORDS => 'dog'
                ]
            )
        );
    }

    public function testGrayFilter()
    {
        $this->assertEquals(
            'http://loremflickr.com/g/300/300/dog',
            $this->faker->someImageUrl(
                300,
                300,
                [
                    LoremFlickr::OPTION_KEYWORDS => 'dog',
                    LoremFlickr::OPTION_FILTER => LoremFlickr::FILTER_GRAY
                ]
            )
        );
    }

    public function testRedFilter()
    {
        $this->assertEquals(
            'http://loremflickr.com/red/100/100/dog',
            $this->faker->someImageUrl(
                100,
                100,
                [
                    LoremFlickr::OPTION_KEYWORDS => 'dog',
                    LoremFlickr::OPTION_FILTER => LoremFlickr::FILTER_RED
                ]
            )
        );
    }

    public function testGreenFilter()
    {
        $this->assertEquals(
            'http://loremflickr.com/green/100/100/dog',
            $this->faker->someImageUrl(
                100,
                100,
                [
                    LoremFlickr::OPTION_KEYWORDS => 'dog',
                    LoremFlickr::OPTION_FILTER => LoremFlickr::FILTER_GREEN
                ]
            )
        );
    }

    public function testBlueFilter()
    {
        $this->assertEquals(
            'http://loremflickr.com/blue/100/100/dog',
            $this->faker->someImageUrl(
                100,
                100,
                [
                    LoremFlickr::OPTION_KEYWORDS => 'dog',
                    LoremFlickr::OPTION_FILTER => LoremFlickr::FILTER_BLUE
                ]
            )
        );
    }

    public function testKeywordsWithDefaultLogic()
    {
        $this->assertEquals(
            'http://loremflickr.com/200/200/dog,cat,bear',
            $this->faker->someImageUrl(
                200,
                200,
                [
                    LoremFlickr::OPTION_KEYWORDS => ['dog', 'cat', 'bear']
                ]
            )
        );
    }

    public function testOrKeywords()
    {
        $this->assertEquals(
            'http://loremflickr.com/200/200/dog,cat,bear',
            $this->faker->someImageUrl(
                200,
                200,
                [
                    LoremFlickr::OPTION_KEYWORDS => ['dog', 'cat', 'bear'],
                    LoremFlickr::OPTION_LOGIC => LoremFlickr::KEYWORDS_LOGIC_OR
                ]
            )
        );
    }

    public function testAndKeywords()
    {
        $this->assertEquals(
            'http://loremflickr.com/200/200/dog,cat,bear/all',
            $this->faker->someImageUrl(
                200,
                200,
                [
                    LoremFlickr::OPTION_KEYWORDS => ['dog', 'cat', 'bear'],
                    LoremFlickr::OPTION_LOGIC => LoremFlickr::KEYWORDS_LOGIC_AND
                ]
            )
        );
    }

    public function testWithoutOptions()
    {
        $this->assertEquals(
            'http://loremflickr.com/100/100',
            $this->faker->someImageUrl(100, 100)
        );
    }

    public function testExtraUnsupportedOptions()
    {
        $this->assertEquals(
            'http://loremflickr.com/100/100',
            $this->faker->someImageUrl(
                100,
                100,
                [
                    'some_extra_unknown_options' => 'does_not',
                    'influence' => 'on_image',
                    'generation' => 'and_everything',
                    'goes' => 'flawlessly'
                ]
            )
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidResolutionZero()
    {
        $this->faker->someImageUrl(0, 0);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidResolutionLessThanZero()
    {
        $this->faker->someImageUrl(100, -100);
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testInvalidResolutionHigherThanLimit()
    {
        $this->faker->someImageUrl(LoremFlickr::MAX_IMAGE_RESOLUTION + 1, 1);
    }

    public function testSavingImage()
    {
        $filePath = $this->faker->someImage(
            10,
            10,
            [
                LoremFlickr::OPTION_KEYWORDS => 'dog',
                LoremFlickr::OPTION_DIR => null,
                LoremFlickr::OPTION_FULL_PATH => true
            ]
        );

        $this->assertTrue(file_exists($filePath), "File not found {$filePath}");

        unlink($filePath);
    }

    public function testReadmeExample()
    {
        /**
         * Add LoremFlickr provider to faker generator
         */

        $faker = new Faker\Generator();
        $faker->addProvider(new LoremFlickr($faker));

        /**
         * Get url to random picture matching the keywords brazil or rio, of 320 x 240 pixels.
         */

        $imageUrl = $faker->someImageUrl(
            320,
            240,
            [
                LoremFlickr::OPTION_KEYWORDS => ['brazil', 'rio']
            ]
        );

        /**
         * Get url to random picture matching the keywords paris and girl, of 320 x 240 pixels.
         */

        $imageUrl = $faker->someImageUrl(
            320,
            240,
            [
                LoremFlickr::OPTION_KEYWORDS => ['paris', 'girl'],
                LoremFlickr::OPTION_LOGIC => LoremFlickr::KEYWORDS_LOGIC_AND,
            ]
        );

        /**
         * Get url to random picture matching the keywords brazil or rio, of 320 x 240 pixels.
         * There is color filter applied to image (available filters: gray, red, green, blue).
         */

        $imageUrl = $faker->someImageUrl(
            320,
            240,
            [
                LoremFlickr::OPTION_KEYWORDS => ['brazil', 'rio'],
                LoremFlickr::OPTION_FILTER => LoremFlickr::FILTER_GRAY,
            ]
        );

        /**
         * Download random picture matching the keywords brazil or rio, of 320 x 240 pixels.
         * Save picture to system temp folder and return filename only.
         */

        $fileName = $this->faker->someImage(
            320,
            240,
            [
                LoremFlickr::OPTION_KEYWORDS => ['brazil', 'rio'],
                LoremFlickr::OPTION_DIR => sys_get_temp_dir(),
                LoremFlickr::OPTION_FULL_PATH => false
            ]
        );

        unlink(sys_get_temp_dir().DIRECTORY_SEPARATOR.$fileName);
    }
}
