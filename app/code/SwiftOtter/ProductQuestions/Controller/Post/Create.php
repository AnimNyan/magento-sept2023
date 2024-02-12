<?php
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\Controller\Post;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ActionInterface;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
//when want flash message to pop up success or error messages
use Magento\Framework\Message\Manager;

use SwiftOtter\ProductQuestions\Command\InitPostFromRequest;
use SwiftOtter\ProductQuestions\Command\ValidatePost;
use SwiftOtter\ProductQuestions\Model\ResourceModel\Post as PostResource;

class Create implements ActionInterface, HttpPostActionInterface
{
    private RedirectFactory $redirectFactory;
    private RedirectInterface $redirect;
    private InitPostFromRequest $initPostFromRequest;
    private RequestInterface $request;
    private PostResource $postResource;
    private ValidatePost $validatePost;
    private Manager $messageManager;
    private Validator $formKeyValidator;

    public function __construct(
        RedirectFactory $redirectFactory,
        RedirectInterface $redirect,
        InitPostFromRequest $initPostFromRequest,
        RequestInterface $request,
        PostResource $postResource,
        ValidatePost $validatePost,
        Manager $messageManager,
        Validator $formKeyValidator
    ){
        $this->redirectFactory = $redirectFactory;
        $this->redirect = $redirect;
        $this->initPostFromRequest = $initPostFromRequest;
        $this->request = $request;
        $this->postResource = $postResource;
        $this->validatePost = $validatePost;
        $this->messageManager = $messageManager;
        $this->formKeyValidator = $formKeyValidator;
    }

    //controller should be lean, so we rely on command classes
    //purpose of controller class is to fetch HTTP request data
    /*
         Fetch request data
         Initialize Post
         Validation
         Save Post
         Set success/error messages
    */

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        // fetch any parameters that came in
        //query string or post data
        //we are validating that it must be POST data from this
        //InitPostFromRequest $initPostFromRequest,
        //  Fetch request data
        $data = $this->request->getParams();

        // Initialize Post
        try {
            //we do want to attach product so second argument is true
            $post = $this->initPostFromRequest->execute($data, true);

            //validate post
            if ($this->validatePost->execute($post)
                && $this->validateFormKey()){
                //if succeeds without exceptions then do the save
                $this->postResource->save($post);
                //Success message
                $this->messageManager->addSuccessMessage(__('You have successfully submitted your post.'));
            }
            //after get hydrated post
            $this->postResource->save($post);


        } catch (LocalizedException $e) {
            // Error message should be generic as shown to user
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            // Error message
            $this->messageManager->addErrorMessage(__('An unexpected error occurred'));
        }

        /** @var Redirect $redirect */
        $redirect = $this->redirectFactory->create();
        $redirect->setUrl($this->redirect->getRefererUrl());

        return $redirect;

    }

    //throws an exception if it fails
    private function validateFormKey(): bool
    {
        $this->formKeyValidator->validate($this->request);
        return true;
    }
}
