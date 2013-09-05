<?php
/**
 * User: Ruslan
 * Date: 04.09.13
 * Time: 12:10
 */

class BrainPriceImport_PriceDataGrabber
{

    /**
     * @var Buzz\Client\Curl
     */
    private $curl;

    /**
     * @var Buzz\Message\Request
     */
    private $request;

    /**
     * @var Buzz\Message\Response|mixed
     */
    private $response;

    /**
     * @var string
     */
    private $authSID;


    /**
     * @param BrainPriceImport_ConnectorInterface $connect api.brain connector
     */
    public function __construct($connect)
    {

        $this->authSID = $connect->getAuthSID();

        $this->curl = $connect->getCurl();

        $this->curl->setTimeout(30000);

        $this->request = $connect->getRequest();

        $this->response = $connect->getResponse();

        $this->request->setMethod('GET');

    }

    public function getCategories()
    {


        $this->request->setResource("/categories/{$this->authSID}");

        $this->curl->send($this->request, $this->response);

        $response = json_decode($this->response->getContent());

        if ($response->status != 1) {
            throw new RuntimeException($response->error_message, $response->error_code);
        }

        return $response->result;

    }

    /**
     * @param int $categoryID
     *
     * @return array
     */
    public function getProducts($categoryID)
    {

        $allProducts = array();

        $limit = 100;
        $offset = null;

        while (true) {
            $result = $this->getProductIterator($categoryID, $limit, $offset);


            $allProducts = array_merge($allProducts, $result->list);

            if ($result->count <= $limit || $result->count <= count($allProducts)) {
                break;
            }

            $offset += $limit;

        }

        return $allProducts;
    }

    /**
     * @param      $categoryID
     * @param int  $limit
     * @param null $offset
     *
     * @return mixed
     * @throws RuntimeException
     */
    private function getProductIterator($categoryID, $limit = 100, $offset = null)
    {

        $resource = "/products/{$categoryID}/{$this->authSID}?&limit=$limit";

        if (!empty($offset)) {
            $resource .= "&offset=$offset";
        }

        $this->request->setResource($resource);

        $this->curl->send($this->request, $this->response);

        $response = json_decode($this->response->getContent());

        if ($response->status != 1) {
            throw new RuntimeException($response->error_message, $response->error_code);
        }

        return $response->result;

    }


    public function getVendors()
    {


        $this->request->setResource("/vendors/{$this->authSID}");

        $this->curl->send($this->request, $this->response);

        $response = json_decode($this->response->getContent(), true);

        if ($response['status'] != 1) {
            throw new RuntimeException($response->error_message, $response->error_code);
        }

        return $response['result'];

    }

}