<?php

namespace Dmdnkv\Faker\Provider;

use Dmdnkv\Faker\Contract\SomeImage;
use Faker\Generator;
use Faker\Provider\Base as FakerBase;
use GuzzleHttp\Client;

abstract class Base extends FakerBase implements SomeImage
{
    /**
     * Constructor options
     */
    const OPTION_BASE_URL = 'base_url';
    const OPTION_LIMIT_RESOLUTION = 'limit_image_resolution';

    /**
     * Image save options
     */
    const OPTION_DIR = 'dir';
    const OPTION_FULL_PATH = 'full_path';
    const OPTION_EXTENSION = 'extension';

    /**
     * Available extensions
     */
    const EXTENSION_JPG = 'jpg';
    const EXTENSION_PNG = 'png';
    const EXTENSION_GIF = 'gif';

    /**
     * Default image extension
     */
    const DEFAULT_IMAGE_EXTENSION = 'jpg';

    /**
     * @var string
     */
    protected $baseUrl;
    /**
     * @var bool|int
     */
    protected $limitImageResolution;
    /**
     * @var Client
     */
    protected $httpClient;
    /**
     * @var array
     */
    protected $validExtensions = [
        self::EXTENSION_JPG,
        self::EXTENSION_GIF,
        self::EXTENSION_PNG,
    ];

    /**
     * @return Client
     */
    protected function getHttpClient()
    {
        if (!$this->httpClient) {
            $this->httpClient = new Client();
        }

        return $this->httpClient;
    }

    /**
     * @param string $imageFormat
     * @return bool
     */
    protected function validateExtensions($imageFormat)
    {
        if (!in_array($imageFormat, $this->validExtensions)) {
            throw new \InvalidArgumentException("Invalid image format {$imageFormat}");
        }
    }

    /**
     * @param string $color
     * @return bool
     */
    protected function validateColor($color)
    {
        if (!preg_match('/^[\dA-Fa-f]{1,6}$/', $color)) {
            throw new \InvalidArgumentException("Invalid color {$color}");
        }
    }

    /**
     * Checks whether dir is directory and is writable
     *
     * @param string $dir
     */
    protected function validateDir($dir)
    {
        if (!is_dir($dir) || !is_writable($dir)) {
            throw new \InvalidArgumentException("Cannot write to directory {$dir}");
        }
    }

    /**
     * Reads option from option array
     *
     * @param array $options
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function readOption($options, $key, $default)
    {
        return isset($options[$key]) ? $options[$key] : $default;
    }

    /**
     * Checks resolution to be valid integer values
     * and not to be out of bounds according to service limits and configuration
     *
     * @param int $width
     * @param int $height
     */
    protected function validateResolution($width, $height)
    {
        if (!is_int($width) || $width <= 0) {
            throw new \InvalidArgumentException("Invalid width {$width}");
        }

        if (!is_int($height) || $height <= 0) {
            throw new \InvalidArgumentException("Invalid width {$height}");
        }

        if ($this->limitImageResolution) {
            $resolution = $height * $width;

            if ($resolution > $this->limitImageResolution) {
                throw new \OutOfBoundsException(
                    "Resolution is limited to {$this->limitImageResolution} (height * width)"
                );
            }
        }
    }

    /**
     * Generate a random filename. Use the server address so that a file
     * generated at the same time on a different server won't have a collision.
     *
     * @param string $extension
     * @return string
     */
    protected function generateRandomFilename($extension = self::DEFAULT_IMAGE_EXTENSION)
    {
        $name = md5(uniqid(empty($_SERVER['SERVER_ADDR']) ? '' : $_SERVER['SERVER_ADDR'], true));

        return "{$name}.{$extension}";
    }

    /**
     * Saves image from specified url to specified directory
     *
     * @param null|string $dir
     * @param string $imageUrl
     * @param bool $fullPath
     * @param string $extension
     * @return string
     * @throws \Exception
     */
    protected function saveImage($dir = null, $imageUrl, $fullPath = true, $extension = self::DEFAULT_IMAGE_EXTENSION)
    {
        $dir = is_null($dir) ? sys_get_temp_dir() : $dir; // GNU/Linux / OS X / Windows compatible

        $this->validateDir($dir);

        $client = $this->getHttpClient();
        $response = $client->get($imageUrl);

        if ($response->getStatusCode() != 200) { // HTTP_OK
            throw new \Exception("Unexpected response from {$imageUrl}");
        }

        $filename = $this->generateRandomFilename($extension);
        $filePath = $dir.DIRECTORY_SEPARATOR.$filename;

        if (false === file_put_contents($filePath, $response->getBody())) {
            throw new \Exception("Can't write file at {$filePath}");
        }

        return $fullPath ? $filePath : $filename;
    }

    /**
     * Constructor
     * @param Generator $generator
     * @param array $options
     */
    public function __construct(Generator $generator, $options = [])
    {
        parent::__construct($generator);

        if (isset($options[self::OPTION_BASE_URL])) {
            $this->baseUrl = $options[self::OPTION_BASE_URL];
        }

        if (isset($options[self::OPTION_BASE_URL])) {
            $this->limitImageResolution = $options[self::OPTION_BASE_URL];
        }
    }

    /**
     * Generates or gets some image somewhere and save it
     *
     * @param int $width
     * @param int $height
     * @param array $options ['dir' => '/tmp', 'full_path' => false, 'extension' => 'jpg']
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
     * @return string full path or name of saved image
     */
    public function someImage($width, $height, $options = [])
    {
        $dir = $this->readOption($options, self::OPTION_DIR, null);
        $fullPath = $this->readOption($options, self::OPTION_FULL_PATH, true);
        $extension = $this->readOption($options, self::OPTION_EXTENSION, self::DEFAULT_IMAGE_EXTENSION);
        $imageUrl = $this->someImageUrl($width, $height, $options);

        return $this->saveImage($dir, $imageUrl, $fullPath, $extension);
    }
}