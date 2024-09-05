<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Controller;

use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\NotifyFactoryInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayInterface;
use Payum\Core\Payum;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Reply\ReplyInterface;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class PaymentNotificationAction
{
    public function __construct (
        private Payum $payum,
        private NotifyFactoryInterface $notifyFactory,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $token = $this->getHttpRequestVerifier()->verify($request);
        $gateway = $this->getGateway($token->getGatewayName());

        $notify = $this->notifyFactory->createNewWithModel($token, new ArrayObject($request->request->all()));

        try {
            $gateway->execute($notify);

            return new Response('TRUE');
        } catch (HttpResponse $reply) {
            return new Response((string) $reply->getCode(), $reply->getStatusCode(), $reply->getHeaders());
        } catch (ReplyInterface $reply) {
            throw new \LogicException('Unsupported reply', previous: $reply);
        }
    }

    private function getHttpRequestVerifier(): HttpRequestVerifierInterface
    {
        return $this->payum->getHttpRequestVerifier();
    }

    private function getGateway(string $gatewayName): GatewayInterface
    {
        return $this->payum->getGateway($gatewayName);
    }
}
