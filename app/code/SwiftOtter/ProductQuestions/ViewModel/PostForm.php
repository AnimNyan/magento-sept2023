<?php
declare(strict_types=1);
namespace SwiftOtter\ProductQuestions\ViewModel;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Http\Context;
use SwiftOtter\ProductQuestions\Model\Config;

class PostForm implements ArgumentInterface
{
    private ?Product $product = null;

    private UrlInterface $url;
    private Registry $coreRegistry;
    private Context $httpContext;

    private Config $config;

    public function __construct(
        UrlInterface $url,
        Registry $coreRegistry,
        Context $httpContext,
        Config $config
    ){
        $this->url = $url;
        $this->coreRegistry = $coreRegistry;
        $this->httpContext = $httpContext;
        $this->config = $config;
    }

    public function getActionUrl(): string
    {
        return $this->url->getUrl('productquestions/post/create');
    }

    public function customerIsLoggedIn(): bool
    {
        //This is using method defined in SwiftOtter\ProductQuestions\Model\Config.php
        //To check if the restrict_logged_in Store Config value has been set SwiftOtter/ProductQuestions/etc/config.xml

        //If store config restricted to logged in setting = true
        // then check if the current customer is logged in
        $isRestrictToLoggedIn =(bool) $this->config->isRestricted();
        if ($isRestrictToLoggedIn) {
            //if they are logged in this will return true -> means they can see the comments section
            //if they are not logged this will return false -> means they can't see the comment section
            return (bool)$this->httpContext->getValue(CustomerContext::CONTEXT_AUTH);
        }

        //If store config restricted to logged in setting = false
        //it is not restricted
        //they should always see the comment box regardless logged in or not so \
        //always return true
        return true;

    }

    public function getProductId(): string
    {
        $product = $this->getProduct();
        return (string) $product->getId() ?? '';
    }

    private function getProduct(): ?Product
    {
        if ($this->product === null) {
            $this->product = $this->coreRegistry->registry('product');
        }
        return $this->product;
    }
}
