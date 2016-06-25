# Faker LoremFlickr Provider

Fake images provider for [Faker](https://github.com/fzaninotto/Faker) PHP library.

There are several image providers implemented:
- `LoremFlickr` ([LoremFlickr](https://github.com/MastaBaba/LoremFlickr) service is used as images source).
- `DummyImage` ([Dynamic Dummy Image Generator](http://dummyimage.com/) service is used as images source).

# Installation

```
composer require dmdnkv/faker-someimage-provider
```


# Basic Usage

## LoremFlickr

```

use Dmdnkv\Faker\Provider\LoremFlickr;

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

```

## DummyImage

```

use Dmdnkv\Faker\Provider\DummyImage;

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
$fileName = $faker->someImageUrl(
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

```