<?php
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\ViewModel\ProductView;

//used to fetch Store Config values
//ScopeConfigInterface.getValue and ScopeConfigInterface.isSetFlag cast as boolean
//can fetch on scope basis
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use SwiftOtter\ProductQuestions\Model\Config;

use SwiftOtter\ProductQuestions\Model\ResourceModel\Post\Collection as PostCollection;
use SwiftOtter\ProductQuestions\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;

use Magento\Framework\Registry;


class Questions implements ArgumentInterface
{
    //caching it the first time we fetch it
    private ?Product $product = null;
    private Config $config;
    private PostCollectionFactory $postCollectionFactory;
    private Registry $coreRegistry;

    public function __construct(
        Config $config,
        PostCollectionFactory $postCollectionFactory,
        Registry $coreRegistry
    ) {
        $this->config = $config;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->coreRegistry = $coreRegistry;
    }

    public function getHeading(): string
    {
        return $this->config->getHeading();
    }

    public function getQuestions(): array
    {
        //caching the first time we are fetching it
        $product = $this->getProduct();

        //if we didn't get back a product
        if (!$product) {
            return [];
        }

        /** @var PostCollection $posts */
        $posts = $this->postCollectionFactory->create();
        //don't trust type
        //we only want questions
        $posts->addProductIdFilter((int) $product->getId())
            ->addQuestionsOnlyFilter()
            ->addAnswers()
            ->addOrder('created_at', 'DESC');

        //collection model $posts we could pass that straight to an iterator
        //we are calling getItems() to explicitly get that array
        return $posts->getItems();
    }

    private function getProduct(): ?Product
    {
        if ($this->product === null) {
            $this->product = $this->coreRegistry->registry('product');
        }
        return $this->product;
    }
}

//function generateString() {
//    return "Welcome to backsub!";
//}
