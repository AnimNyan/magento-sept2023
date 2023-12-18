<?php
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
class Post extends AbstractModel implements IdentityInterface
{
    //inheritdoc is for overriding or implementing a method from a base class or interface
    /**
     * {@inheritdoc}
     */
    protected  function  _construct()
    {
        //hey
        $this->_init(
            \SwiftOtter\ProductQuestions\Model\ResourceModel\Post::class
        );
    }

    public function getIdentities()
    {
        //we attach the post object to our product before saving it
        $product = $this->getProduct();
        return ($product) ? $product->getIdentities() : [];
    }

}
