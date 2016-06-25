<?php

use Dmdnkv\Faker\Provider\DummyImage;

class DummyImageTest extends PHPUnit_Framework_TestCase
{
    protected $faker;

    public function setUp()
    {
        $faker = new \Faker\Generator();
        $faker->addProvider(new DummyImage($faker));

        $this->faker = $faker;

        parent::setUp();
    }

    public function testBasics()
    {
        $this->assertEquals(
            'http://dummyimage.com/600x400/0/f',
            $this->faker->someImageUrl(600, 400)
        );
    }

    public function testBgColor()
    {
        $this->assertEquals(
            'http://dummyimage.com/600x400/ffffff/f',
            $this->faker->someImageUrl(
                600,
                400,
                [
                    DummyImage::OPTION_BG_COLOR => 'ffffff',
                ]
            )
        );
    }

    public function testFgColor()
    {
        $this->assertEquals(
            'http://dummyimage.com/600x400/0/ffffff',

            $this->faker->someImageUrl(
                600,
                400,
                [
                    DummyImage::OPTION_FG_COLOR => 'ffffff',
                ]
            )
        );
    }

    public function testExtension()
    {
        $this->assertEquals(
            'http://dummyimage.com/600x400/0/ffffff.jpg',

            $this->faker->someImageUrl(
                600,
                400,
                [
                    DummyImage::OPTION_FG_COLOR => 'ffffff',
                    DummyImage::OPTION_EXTENSION => DummyImage::EXTENSION_JPG,
                ]
            )
        );
    }

    public function testText()
    {
        $this->assertEquals(
            'http://dummyimage.com/600x400/0/ffffff&text=some%2Btext%2Bhere',

            $this->faker->someImageUrl(
                600,
                400,
                [
                    DummyImage::OPTION_FG_COLOR => 'ffffff',
                    DummyImage::OPTION_TEXT => 'some text here',
                ]
            )
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidBgColor()
    {
        $this->faker->someImageUrl(
            600,
            400,
            [
                DummyImage::OPTION_BG_COLOR => 'jrwe0tpwjertwherk',
            ]
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidFgColor()
    {
        $this->faker->someImageUrl(
            600,
            400,
            [
                DummyImage::OPTION_FG_COLOR => 'jrwe0tpwjertwherk',
            ]
        );
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
        $this->faker->someImageUrl(DummyImage::MAX_IMAGE_RESOLUTION + 1, 1);
    }

    public function testSavingImage()
    {
        $filePath = $this->faker->someImage(
            10,
            10,
            [
                DummyImage::OPTION_DIR => null,
                DummyImage::OPTION_FULL_PATH => true
            ]
        );

        $this->assertTrue(file_exists($filePath), "File not found {$filePath}");

        unlink($filePath);
    }

    /**
     * @dataProvider imageExtensionsProvider
     *
     * @param string $extension
     * @param string $expectedResult
     */
    public function testImageExtensions($extension, $expectedResult)
    {
        $filePath = $this->faker->someImage(
            10,
            10,
            [
                DummyImage::OPTION_DIR => null,
                DummyImage::OPTION_FULL_PATH => true,
                DummyImage::OPTION_EXTENSION => $extension,
            ]
        );

        $this->assertTrue(file_exists($filePath), "File not found {$filePath}");

        $fileInfo = new SplFileInfo($filePath);

        $this->assertEquals($expectedResult, $fileInfo->getExtension());

        unlink($filePath);
    }

    public function imageExtensionsProvider()
    {
        return [
            ['jpg', 'jpg'],
            ['png', 'png'],
            ['gif', 'gif']
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidImageExtensions()
    {
        $filePath = $this->faker->someImage(
            10,
            10,
            [
                DummyImage::OPTION_DIR => null,
                DummyImage::OPTION_FULL_PATH => true,
                DummyImage::OPTION_EXTENSION => 'dafasdf',
            ]
        );
    }

    public function testReadmeExample()
    {
        /**
         * Add DummyImage provider to faker generator
         */

        $faker = new Faker\Generator();
        $faker->addProvider(new DummyImage($faker));

        /**
         * Get url to PNG picture with black background color and white text `some text here`
         */
        $imageUrl = $faker->someImageUrl(
            320,
            240,
            [
                DummyImage::OPTION_BG_COLOR => '000000',
                DummyImage::OPTION_FG_COLOR => 'ffffff',
                DummyImage::OPTION_TEXT => 'some text here',
                DummyImage::OPTION_EXTENSION => DummyImage::EXTENSION_PNG,
            ]
        );

        /**
         * Download PNG picture with black background color and white text `some text here`.
         * Save picture to system temp folder and return filename only.
         */
        $fileName = $faker->someImage(
            320,
            240,
            [
                DummyImage::OPTION_BG_COLOR => '000000',
                DummyImage::OPTION_FG_COLOR => 'ffffff',
                DummyImage::OPTION_TEXT => 'some text here',
                DummyImage::OPTION_EXTENSION => DummyImage::EXTENSION_PNG,
                DummyImage::OPTION_DIR => sys_get_temp_dir(),
                DummyImage::OPTION_FULL_PATH => false,
            ]
        );

        unlink(sys_get_temp_dir().DIRECTORY_SEPARATOR.$fileName);
    }
}
