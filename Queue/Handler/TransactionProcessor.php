<?php
/*
 * Copyright Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Mollie\Payment\Queue\Handler;

use Magento\Sales\Api\OrderRepositoryInterface;
use Mollie\Payment\Api\Data\TransactionToProcessInterface;
use Mollie\Payment\Config;
use Mollie\Payment\Model\Mollie;

class TransactionProcessor
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var Mollie
     */
    private $mollieModel;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        Config $config,
        Mollie $mollieModel
    ) {
        $this->orderRepository = $orderRepository;
        $this->config = $config;
        $this->mollieModel = $mollieModel;
    }

    public function execute(TransactionToProcessInterface $data): void
    {
        try {
            $order = $this->orderRepository->get($data->getOrderId());
            $order->setMollieTransactionId($data->getTransactionId());

            $this->mollieModel->processTransactionForOrder($order, 'webhook');
        } catch (\Throwable $throwable) {
            $this->config->addToLog('error', [
                'from' => 'TransactionProcessor consumer',
                'message' => $throwable->getMessage(),
                'trace' => $throwable->getTraceAsString(),
                'order_id' => $data->getOrderId(),
                'transaction_id' => $data->getTransactionId(),
            ]);
            throw $throwable;
        }
    }
}
