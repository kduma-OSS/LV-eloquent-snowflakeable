# LV-eloquent-snowflakeable
[![Latest Stable Version](https://poser.pugx.org/kduma/eloquent-snowflakeable/v/stable.svg)](https://packagist.org/packages/kduma/eloquent-snowflakeable) 
[![Total Downloads](https://poser.pugx.org/kduma/eloquent-snowflakeable/downloads.svg)](https://packagist.org/packages/kduma/eloquent-snowflakeable) 
[![Latest Unstable Version](https://poser.pugx.org/kduma/eloquent-snowflakeable/v/unstable.svg)](https://packagist.org/packages/kduma/eloquent-snowflakeable) 
[![License](https://poser.pugx.org/kduma/eloquent-snowflakeable/license.svg)](https://packagist.org/packages/kduma/eloquent-snowflakeable)

Eases using and generating SnowFlake ID's in Laravel Eloquent models.

# Setup
Install it using composer

    composer require kduma/eloquent-snowflakeable

# Prepare models
Inside your model (not on top of file) add following lines:
    
    use \KDuma\Eloquent\Snowflakeable;

In database create `ulid` string field. If you use migrations, you can use following snippet:

    $table->unsignedBigInteger('sfid')->unique();

# Usage
By default, it generates snowflake id on first save.

- `$model->regenerateSnowflake()` - Generate new snowflake id. (Remember to save it by yourself)
- `Model::whereSnowflake($id)->first()` - Find by snowflake id. (`whereSnowflake` is query scope)
- `Model::bySnowflake($id)` - Find by snowflake id.
- `$model->snowflake` - Gets `ParsedSnowflake` object. 
- `$model->snowflake->getDateTime()` - Gets `Carbon` object with snowflake creation time. 

# Packagist
View this package on Packagist.org: [kduma/eloquent-snowflakeable](https://packagist.org/packages/kduma/eloquent-snowflakeable)
