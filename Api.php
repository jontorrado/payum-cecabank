<?php
namespace JonTorrado\Payum\Cecabank;

use Http\Message\MessageFactory;
use Payum\Core\Exception\Http\HttpException;
use Payum\Core\HttpClientInterface;
use Psr\Http\Message\ResponseInterface;

class Api
{
    /**
     * @var HttpClientInterface
     */
    protected $client;

    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param array               $options
     * @param HttpClientInterface $client
     * @param MessageFactory      $messageFactory
     *
     * @throws \Payum\Core\Exception\InvalidArgumentException if an option is invalid
     */
    public function __construct(array $options, HttpClientInterface $client, MessageFactory $messageFactory)
    {
        $this->options = $options;
        $this->client = $client;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @param array $fields
     *
     * @return ResponseInterface
     */
    protected function doRequest($method, array $fields)
    {
        $headers = [];

        $request = $this->messageFactory->createRequest($method, $this->getApiEndpoint(), $headers, http_build_query($fields));

        $response = $this->client->send($request);

        if (false == ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300)) {
            throw HttpException::factory($request, $response);
        }

        return $response;
    }

    /**
     * @return string
     */
    public function getApiEndpoint()
    {
        return $this->options['sandbox']
            ? 'http://tpv.ceca.es:8000/cgi-bin/tpv'
            : 'https://pgw.ceca.es/cgi-bin/tpv';
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->options['merchant_id'];
    }

    /**
     * @return string
     */
    public function getAcquirerBin()
    {
        return $this->options['acquirer_bin'];
    }

    /**
     * @return string
     */
    public function getTerminalId()
    {
        return $this->options['terminal'];
    }

    /**
     * @return string
     */
    public function getCurrencyType()
    {
        return 978; // EUR
    }

    /**
     * Sing request sent to Gateway
     *
     * @param array $params
     *
     * @return string
     */
    public function sign(array $params)
    {
        $sign = '';

        $signString = $this->options['secret_key'] .
            $this->options['merchant_id'] .
            $this->options['acquirer_bin'] .
            $this->options['terminal'] .
            $params['Num_operacion'] .
            $params['Importe'] .
            $params['TipoMoneda'] .
            $params['Exponente'] .
            $params['Cifrado'] .
            $params['URL_OK'] .
            $params['URL_NOK'];

        if (strlen(trim($signString)) > 0) {
            $sign = strtolower(hash('sha256', $signString));
        }

        return $sign;
    }
}
