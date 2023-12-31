<?php
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\Command;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use SwiftOtter\ProductQuestions\Model\Post;

class ValidatePost
{
    private array $requiredFields = ['customer_nickname', 'content', 'product_id'];
    private ProductRepositoryInterface $productRepository;

    public function __construct (
        ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * @throws AuthorizationException
     * @throws InputException
     * @throws LocalizedException
     */
    public function execute(Post $post): bool
    {
        //validate customer id
        if (!$post->getCustomerId()) {
            throw new AuthorizationException(__('You must be logged in to submit a product question or answer.'));
        }
        // Required fields
        //customer nickname
        //question content
        //product id (corresponds with valid product)
        //customer id

        $missingFields = [];
        foreach($this->requiredFields as $fieldName) {
            //hasData checks whether the data for a key exists at all
            //but also want to check has non-empty value
            // will return null if the value doesn't exist
            // or if empty string still evaluate false
            if(!$post->getData($fieldName)){
                $missingFields[] = $fieldName;
            }
        }

        if (!empty($missingFields)) {
            throw new InputException(
                __(
                    'The following fields are required: %1',
                    implode(', ', $missingFields)
                )
            );
        }

        //validate product

        //if we already have a hydrated post
        //it must have been successfully loaded
        //if it's not a hydrated post go to product validation
        if(!$post->getProduct()) {
            //this is if there is no hydrated post
            try {
                $this->productRepository->getById((int) $post->getProductId());
            } catch (NoSuchEntityException $e) {
                throw new LocalizedException(__('The product associated with the post was not found.'));
            }

        }
        return true;
    }
}
