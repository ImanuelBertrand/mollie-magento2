<?php
/*
 * Copyright Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Mollie\Payment\Test\Integration\GraphQL\Resolver\Checkout;

use Mollie\Payment\Service\Magento\PaymentLinkRedirect;
use Mollie\Payment\Test\Fakes\Service\Magento\PaymentLinkRedirectFake;
use Mollie\Payment\Test\Integration\GraphQLTestCase;

/**
 * @magentoAppArea graphql
 */
class PaymentLinkRedirectTest extends GraphQLTestCase
{
    public function testReturnsValidResultWhenNotYetPaid(): void
    {
        $fakeInstance = $this->objectManager->get(PaymentLinkRedirectFake::class);
        $fakeInstance->fakeResponse('https://www.example.com', false);

        $this->objectManager->addSharedInstance($fakeInstance, PaymentLinkRedirect::class);

        $this->objectManager->removeSharedInstance(\Mollie\Payment\GraphQL\Resolver\Checkout\PaymentLinkRedirect::class);

        $result = $this->graphQlQuery('
            mutation {
                molliePaymentLinkRedirect(order: "999") {
                    already_paid
                    redirect_url
                }
            }
        ');

        $this->assertSame($result['molliePaymentLinkRedirect']['redirect_url'], 'https://www.example.com');
        $this->assertSame($result['molliePaymentLinkRedirect']['already_paid'], false);
    }

    public function testReturnsValidResultWhenAlreadyPaid(): void
    {
        $fakeInstance = $this->objectManager->get(PaymentLinkRedirectFake::class);
        $fakeInstance->fakeResponse(null, true);

        $this->objectManager->addSharedInstance($fakeInstance, PaymentLinkRedirect::class);

        $result = $this->graphQlQuery('
            mutation {
                molliePaymentLinkRedirect(order: "999") {
                    already_paid
                    redirect_url
                }
            }
        ');

        $this->assertSame($result['molliePaymentLinkRedirect']['redirect_url'], null);
        $this->assertSame($result['molliePaymentLinkRedirect']['already_paid'], true);
    }
}
