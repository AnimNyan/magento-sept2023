<?php
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private ScopeConfigInterface $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    const CONFIG_PATH_ENABLED = 'catalog/questions/enabled';
    const CONFIG_PATH_PRODUCT_QUESTIONS_HEADING = 'catalog/questions/heading';
    const CONFIG_PATH_RESTRICTED = 'catalog/questions/restrict_logged_in';
    public function isEnabled(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_ENABLED,
            $scopeType,
            $scopeCode
        );
    }

    public function getHeading(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): string {
        //must type cast to string since config value not guaranteed to be a string
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_PRODUCT_QUESTIONS_HEADING,
            $scopeType,
            $scopeCode
        );
    }

    public function isRestricted(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_RESTRICTED,
            $scopeType,
            $scopeCode
        );
    }
}
