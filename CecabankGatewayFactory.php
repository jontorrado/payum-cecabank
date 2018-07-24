<?php
namespace JonTorrado\Payum\Cecabank;

use JonTorrado\Payum\Cecabank\Action\AuthorizeAction;
use JonTorrado\Payum\Cecabank\Action\CancelAction;
use JonTorrado\Payum\Cecabank\Action\ConvertPaymentAction;
use JonTorrado\Payum\Cecabank\Action\CaptureAction;
use JonTorrado\Payum\Cecabank\Action\NotifyAction;
use JonTorrado\Payum\Cecabank\Action\RefundAction;
use JonTorrado\Payum\Cecabank\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class CecabankGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'cecabank',
            'payum.factory_title' => 'Cecabank',
            'payum.action.capture' => new CaptureAction(),
            'payum.action.authorize' => new AuthorizeAction(),
            'payum.action.refund' => new RefundAction(),
            'payum.action.cancel' => new CancelAction(),
            'payum.action.notify' => new NotifyAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = array(
                'sandbox' => true,
            );
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return new Api((array) $config, $config['payum.http_client'], $config['httplug.message_factory']);
            };
        }
    }
}
