<?php
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\Command;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
//need to access customer's full session information to get customer id
use Magento\Customer\Model\Session;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use SwiftOtter\ProductQuestions\Model\Post;
//need factory to get instance of class
use SwiftOtter\ProductQuestions\Model\PostFactory;


class InitPostFromRequest
{
    private PostFactory $postFactory;
    private Session $customerSession;
    private ProductRepositoryInterface $productRepository;

    public function __construct(
        PostFactory $postFactory,
        Session $customerSession,
        ProductRepositoryInterface $productRepository
    ){

        $this->postFactory = $postFactory;
        $this->customerSession = $customerSession;
        $this->productRepository = $productRepository;
    }

    //hydrate the instances fill them with data
    // to make it portable by default we won't load the product as this can be an expensive operation
    /**
     * @throws LocalizedException
     */
    public function execute(array $data, bool $attachProduct = false): Post
    {
        //the var annotation indicates what should be the return typ e of the create command
        /** @var Post $post */
        $post = $this->postFactory->create();

        //passing array into setData command set multiple properties at once
        $post->setData($data);
        //got CustomerId from session
        $post->setCustomerId($this->getCustomerId());

        if ($attachProduct) {
            //we want the fully loaded product model
            $post->setProduct($this->getProduct($data));
        }

        return $post;
    }

    //validation logic whether the customer is logged in needs to come at later time
    private function getCustomerId(): ?int
    {
        $customerId = $this->customerSession->getId();
        //explicitly type cast
        return ($customerId) ? (int) $customerId : null;
    }

    /**
     * @throws LocalizedException
     */
    private function getProduct(array $data) : ?ProductInterface
    {
        //if don't have a product id return null
        if (!isset($data['product_id'])) {
            return null;
        }


        //get exception if trying to get a product from an id that doesn't exist
        try {
            $product = $this->productRepository->getById((int)$data['product_id']);
        } catch (NoSuchEntityException $e) {
            //meant to be user facing error because being translated with __
            throw new LocalizedException(__('The product associated with the post was not found.'));
        }
        return $product;
    }
}
