<?php
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\ViewModel\ProductView;

//used to fetch Store Config values
//ScopeConfigInterface.getValue and ScopeConfigInterface.isSetFlag cast as boolean
//can fetch on scope basis
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use SwiftOtter\ProductQuestions\Model\Config;


class Questions implements ArgumentInterface
{
    private Config $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    public function getHeading(): string
    {
        return $this->config->getHeading();
    }
}

//function generateString() {
//    return "Welcome to backsub!";
//}
