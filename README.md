#Commode: Silent Service

[![Build Status](https://travis-ci.org/laravel-commode/silent-service.svg?branch=master)](https://travis-ci.org/laravel-commode/silent-service)
[![Code Climate](https://codeclimate.com/github/laravel-commode/silent-service/badges/gpa.svg)](https://codeclimate.com/github/laravel-commode/silent-service)
[![Coverage Status](https://coveralls.io/repos/laravel-commode/silent-service/badge.svg?branch=master)](https://coveralls.io/r/laravel-commode/silent-service?branch=master)

>**_laravel-commode/silent-service_** is a customized service provider with useful features for designed for 
laravel-commode package environment or laravel 5.1 package development. 

####Contents

+ <a href="#installing">Installing</a>
+ <a href="#usage">Usage examples</a>


##<a name="installing">Installing</a>

You can install ___laravel-commode/silent-service___ using composer:

```json
"require": {
    "laravel-commode/silent-service": "dev-master"
}
```
    
There are two ways to register service provider. First one is a classic registration in `app.php` config file, 
but it's optional, since any `SilentService` instance does dependency checks on other service providers and 
if they are not registered, their registration is being enforced.

```php
<?php
    // apppath/config/app.php
    return [
        // config code...
        
        'providers' => [
            // your app providers... ,
            LaravelCommode\SilentService\SilentServiceServiceProvider::class
            // or any your SilentService instance
        ]
    ];
```



##<a name="usage">Usage</a>

**_laravel-commode/silent-service_** was developed to provide service dependency loading and alias binding 
without modifying `app.php` code, for example if service provider of your package depends on five different 
from different package, you would need to make config modification for all five packages - this way your service 
is responsible for dependency control, but not the "final developer".

To create a silent service provider you need to extend ``LaravelCommode\SilentService\SilentService`` class and 
implement two protected methods: ``SilentService::registering()`` and ``SilentService::launching()``.

To declare dependencies on different service providers you need to override protected method 
``SilentService::uses()``. This method must return an array of service providers' class names, which need to
be registered before ``SilentService::registering()`` is triggered. 

If your service provider needs to implement aliases on laravel facades you need to override protected method 
``SilentService::aliases()``. This method must return an array of strings, where array keys are alias names and 
array values are facade class names.

Since the usage of service providers is not always for registering new features, but also modifying the old ones 
and still there are a lot of facade haters among the community, silent service providers protected method 
``SilentService::with(array $resolvable, callable $do)``, which usage you will see in example. Note that it can 
resolve not only service names, but everything that is found in IoC of can be resolved|instantiated.


```php
<?php
    namespace MyVendor\MyPackage;
    
    use LaravelCommode\SilentService\SilentService;
    
    use Illuminate\View\Factory;
    use Illuminate\Http\Request;
    
    class MyPackageServiceProvider extends SilentService
    {
        protected function uses()
        {
            return [\CustomVendor\CustomPackage\CustomPackageServiceProvider::class];
        }
        
        protected function aliases()
        {
            return [
                'MyFacade' => 'MyVendor\MyPackage\MyPackageFacade'
            ];
        }
        
        /**
         * This method will be triggered instead
         * of original ServiceProvider::register().
         * @return mixed
         */
         public function registering()
         {
            $this->with(['view', 'request'], function (Factory $view, Request $request) {
                // do registrations
            });
         }
    
    
        /**
         * This method will be triggered instead
         * when application's booting event is fired.
         * @return mixed
         */
         public function launching()
         {
         
         }
    }
```

