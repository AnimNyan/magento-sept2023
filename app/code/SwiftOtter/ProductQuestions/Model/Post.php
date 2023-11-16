<?php
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\Model;

use Magento\Framework\Model\AbstractModel;
class Post extends AbstractModel
{
    //inheritdoc is for overriding or implementing a method from a base class or interface
    /**
     * {@inheritdoc}
     */
    protected  function  _construct()
    {
        $this->_init(
            \SwiftOtter\ProductQuestions\Model\ResourceModel\Post::class
        );
    }
}
