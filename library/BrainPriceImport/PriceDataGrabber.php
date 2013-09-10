<?php
/**
 * User: Ruslan
 * Date: 04.09.13
 * Time: 12:10
 */

class BrainPriceImport_PriceDataGrabber
{

    static private $stocks = array();

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

    public function getBaseCategory()
    {
        $result = new stdClass();
        $result->categoryID = 1;
        $result->name = 'Без указания каталога';

        $result = Array($result);

        return $result;

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

        $this->getStocks();


        while (true) {
            $result = $this->getProductIterator($categoryID, $limit, $offset);


            $restateStocks = array_filter($result->list, "self::setStocksForItem");

//            if ($restateStocks)

            $allProducts = array_merge($allProducts, $restateStocks);

            if ($result->count <= $limit || empty($result->list)) {
                break;
            }

            $offset += $limit;

            echo "offset = $offset \r\n";

        }

        return $allProducts;
    }

    static private function setStocksForItem($product)
    {

        if (!empty($product->is_archive)) {
            return false;
        }


        if (empty($product->stocks)) {
            return false;
        }

        foreach ($product->stocks as $key => $value) {
            $product->stocks[$key] = self::$stocks[$value];
        }

        return $product;
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


    private function getStocks()
    {

        $resource = "/stocks/{$this->authSID}";
        $this->request->setResource($resource);

        $this->curl->send($this->request, $this->response);

        $response = json_decode($this->response->getContent());

        if ($response->status != 1) {
            throw new RuntimeException($response->error_message, $response->error_code);
        }


        foreach ($response->result as $value) {
            self::$stocks[$value->stockID] = $value->name;
        }

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