<?php
namespace JonTorrado\Payum\Cecabank\Action;

use JonTorrado\Payum\Cecabank\Api;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpPostRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Exception\RequestNotSupportedException;

class CaptureAction implements ActionInterface, ApiAwareInterface
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
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        // TODO: https://github.com/crevillo/payum-redsys/blob/master/Action/CaptureAction.php
        $details->validatedKeysSet(array(
            'MerchantID',
            'AcquirerBIN',
            'TerminalID',
            'Num_operacion',
            'Importe',
            'TipoMoneda',
            'Exponente',
        ));

        if (!isset($details['URL_OK']) && $request->getToken()) {
            $details['URL_OK'] = $request->getToken()->getTargetUrl();
        }
        if (!isset($details['URL_NOK']) && $request->getToken()) {
            $details['URL_NOK'] = $request->getToken()->getTargetUrl();
        }

        $details['Cifrado'] = 'SHA2';
        $details['Pago_soportado'] = 'SSL';

        if (false == $details['Firma']) {
            $details['Firma'] = $this->api->sign($details->toUnsafeArray());

            throw new HttpPostRedirect($this->api->getApiEndpoint(), $details->toUnsafeArray());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
