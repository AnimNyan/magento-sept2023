<?php
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\Block\ProductView;

use Magento\Framework\View\Element\Template;

class Questions extends Template
{
    public function getPostFormHtml(?int $parentId): string
    {
        /**@var Template postFormBlock */
        $postFormBlock = $this->getLayout()->getBlock('questions.post_form');
        if (!$postFormBlock){
            return  '';
        }

        /** @var PostForm $postFormViewModel */
        $postFormViewModel = $postFormBlock->getData('view_model');

        //if we have a view model and a parent ID
        if ($postFormViewModel && $parentId) {
            $postFormViewModel->setParentId($parentId);
        }

        //toHtml calls on the block to render the final output
        return $postFormBlock->toHtml();
    }

}
