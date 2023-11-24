<?php
declare(strict_types=1);
namespace SwiftOtter\ProductQuestions\Model\ResourceModel\Post;

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SwiftOtter\ProductQuestions\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
class Collection extends AbstractCollection
{
    const ADD_ANSWERS_FLAG = 'add_answers';
    private PostCollectionFactory $postsCollectionFactory;
    protected function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        PostCollectionFactory $postsCollectionFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    )
    {
        parent::__construct(
          $entityFactory,
          $logger,
          $fetchStrategy,
          $eventManager,
          $connection,
          $resource
        );
        $this->postsCollectionFactory = $postsCollectionFactory;

    }
    protected function _construct()
    {
        $this->_init(
            \SwiftOtter\ProductQuestions\Model\Post::class,
            \SwiftOtter\ProductQuestions\Model\ResourceModel\Post::class
        );
    }

    public function addProductIdFilter(int $productID): Collection
    {
        $this->addFieldToFilter('product_id', $productID);

        return $this;
    }

    public function addQuestionsOnlyFilter(): Collection
    {
        $this->addFieldToFilter('parent_Id', ['null' => true]);

        return $this;
    }

    //there are two circumstances to use this
    //1. If filtering for answers to a specific question
    //2. If filtering for any answer to any question
    public function addAnswersOnlyFilter(
        ?array $questionsIDs = null
    ): Collection {
        //1. If filtering for answers to a specific question
        if ($questionsIDs !== null) {
            $this->addFieldToFilter('parent_id', ['in' => $questionsIDs]);
        //2. If filtering for any answer to any question
        } else {
            $this->addFieldToFilter('parent_id', ['notnull' => true]);
        }

        return $this;
    }

    public function addAnswers(): Collection
    {
        if ($this->isLoaded()) {
            $this->doAddAnswers();
        } else {
            $this->setFlag(self::ADD_ANSWERS_FLAG, true);
        }
        return $this;
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();

        if ($this->getFlag(self::ADD_ANSWERS_FLAG)) {
            $this->doAddAnswers();
        }
        return $this;
    }

    private function doAddAnswers(): Collection
    {
        // Get IDs of all posts in this collection
        $questionPosts = $this->getItems();
        $ids = array_keys($questionPosts);

        // Load all children of those posts
        /** @var Collection $answerPosts */
        $answerPosts = $this->postsCollectionFactory->create();
        $answerPosts->addAnswersOnlyFilter($ids)
            ->addOrder('created_at', 'DESC');

        // Arrange children by parent
        $answersByQuestionId = [];
        foreach ($answerPosts as $answerPost) {
            $questionId = $answerPost->getParentId();
            if (!$questionId) {
                continue;
            }

            if (!isset($answersByQuestionId[$questionId])) {
                $answersByQuestionId[$questionId] = [];
            }
            $answersByQuestionId[$questionId][] = $answerPost;
        }

        // Attach children to each item
        foreach ($questionPosts as $question) {
            $questionId = $question->getId();
            if (isset($answersByQuestionId [$questionId])) {
                $question->setAnswers ($answersByQuestionId[$questionId]);
            }
        }
        // Filter "answer" posts by parent IDs based on the current
        // collection, group the results for each parent, and set
        // them on the parent objects.
        return $this;
    }
}
