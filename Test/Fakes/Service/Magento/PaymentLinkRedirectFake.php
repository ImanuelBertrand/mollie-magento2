<?php
/*
 * Copyright Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Mollie\Payment\Test\Fakes\Service\Magento;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Mollie\Payment\Model\Mollie;
use Mollie\Payment\Service\Magento\PaymentLinkRedirect;
use Mollie\Payment\Service\Magento\PaymentLinkRedirectResult;
use Mollie\Payment\Service\Magento\PaymentLinkRedirectResultFactory;

class PaymentLinkRedirectFake extends PaymentLinkRedirect
{
    /**
     * @var PaymentLinkRedirectResultFactory
     */
    private $paymentLinkRedirectResultFactory;
    /**
     * @var PaymentLinkRedirectResult
     */
    private $result;

    public function __construct(
        EncryptorInterface $encryptor,
        OrderRepositoryInterface $orderRepository,
        Mollie $mollie,
        PaymentLinkRedirectResultFactory $paymentLinkRedirectResultFactory
    ) {
        parent::__construct($encryptor, $orderRepository, $mollie, $paymentLinkRedirectResultFactory);
        $this->paymentLinkRedirectResultFactory = $paymentLinkRedirectResultFactory;
    }

    public function fakeResponse(?string $redirectUrl, bool $alreadyPaid)
    {
        $this->result = $this->paymentLinkRedirectResultFactory->create([
            'alreadyPaid' => $alreadyPaid,
            'redirectUrl' => $redirectUrl,
        ]);
    }

    public function execute(string $orderId): PaymentLinkRedirectResult
    {
        if ($this->result) {
            return $this->result;
        }

        return parent::execute($orderId);
    }
}
