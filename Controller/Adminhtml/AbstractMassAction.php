<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */
namespace Magerubik\All\Controller\Adminhtml;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\AbstractDb;
/**
 * Class AbstractMassAction
 */
abstract class AbstractMassAction extends Action
{
    /**
     * @var Filter
     */
    private $filter;
    /**
     * @var LoggerInterface
     */
    private $logger;
    public function __construct(
        Action\Context $context,
        Filter $filter,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->logger = $logger;
    }
    /**
     * Execute action for category
     *
     * @param $category
     */
    abstract protected function itemAction($category);
    /**
     * @return AbstractDb
     */
    abstract protected function getCollection();
    /**
     * Mass action execution
     */
    public function execute()
    {
        $this->filter->applySelectionOnTargetProvider();
        $collection = $this->filter->getCollection($this->getCollection());
        $collectionSize = $collection->getSize();
        if ($collectionSize) {
            try {
                foreach ($collection->getItems() as $model) {
                    $this->itemAction($model);
                }
                $this->messageManager->addSuccessMessage($this->getSuccessMessage($collectionSize));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (CouldNotSaveException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($this->getErrorMessage());
                $this->logger->critical($e);
            }
        }
        $this->_redirect($this->_redirect->getRefererUrl());
    }
    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('We can\'t change item right now. Please review the log and try again.');
    }
    /**
     * @param int $collectionSize
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        return $collectionSize
            ? __('A total of %1 record(s) have been changed.', $collectionSize)
            : __('No records have been changed.');
    }
}