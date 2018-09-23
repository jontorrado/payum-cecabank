<?php
namespace JonTorrado\Payum\Cecabank\Action;

use JonTorrado\Payum\Cecabank\Api;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Convert;

class ConvertPaymentAction implements ActionInterface, ApiAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @var Api
     */
    protected $api;

    /**
     * {@inheritDoc}
     */
    public function setApi($api)
    {
        if (false === $api instanceof Api) {
            throw new UnsupportedApiException('Not supported.');
        }

        $this->api = $api;
    }

    /**
     * {@inheritDoc}
     *
     * @param Convert $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();

        $details = ArrayObject::ensureArrayObject($payment->getDetails());

        // TODO: https://github.com/crevillo/payum-redsys/blob/master/Action/ConvertPaymentAction.php
        $details->defaults(array(
            'MerchantID'    => $this->api->getMerchantId(),
            'AcquirerBIN'   => $this->api->getAcquirerBin(),
            'TerminalID'    => $this->api->getTerminalId(),
            'Num_operacion' => $payment->getNumber(),
            'Importe'       => $payment->getTotalAmount(),
            'TipoMoneda'    => $this->api->getCurrencyType(),
            'Exponente'     => 2,
        ));

        $request->setResult((array) $details);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            $request->getTo() == 'array'
        ;
    }
}
