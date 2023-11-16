<?php
//means that php will not auto convert types and throw a TypeError instead
declare(strict_types=1);

use Magento\Framework\Component\ComponentRegistrar;

//tell magento registering a module called SwiftOtter_ProductQuestions
ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'SwiftOtter_ProductQuestions',
    __DIR__
);
