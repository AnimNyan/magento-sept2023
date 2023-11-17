<?php
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\Model;

use SwiftOtter\ProductQuestions\Model\Post;
use SwiftOtter\ProductQuestions\Model\PostFactory;
use SwiftOtter\ProductQuestions\Model\ResourceModel\Post
    as PostResource;

class TestClass
{
    private PostFactory $postFactory;
    private PostResource $postResource;

    public function __construct(
        PostFactory $postFactory,
        PostResource $postResource
    ) {
        $this->postFactory = $postFactory;
        $this->postResource = $postResource;
    }

    public function execute(int $id) : string
    {
        /** @var Post $post */
//        //create new class
//        $post = $this->postFactory->create();
//        $post->setCustomerNickname('Joe');
//        $this->postResource->save($post);

        //load new class
//        $post = $this->postFactory->create();
//        $this->postResource->load($post, 1);


//        $this->postResource->load($post, $id);
//
//        return $post->getCustomerNickname()
//            . ' says: '
//            . $post->getContent();
    }
}
