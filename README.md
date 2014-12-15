TagBundle For Symfony2
=========

##About

The goal of this bundle is to allow you to associate tags with several entities.

##Documentation

###Installation

1. Add package to `require` in `composer.json`:

	```json
	"require": {
	    "mykees/symfony2-tagbundle": "dev-master"
	}
	```

2. Install package:

	```
	$ php composer.phar require mykees/symfony2-tagbundle
	```

3. Add this bundle to your application's kernel :

	```php
	$bundles = array(
            new Mykees\MediaBundle\MykeesMediaBundle(),
        );
	```

##Requirement

- Php : ">= 5.4"
- LiipFunctionalTestBundle : for TDD
