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


    static private $validStocks = array();

    /**
     * @param BrainPriceImport_ConnectorInterface $connect api.brain connector
     */
    public function __construct($connect)
    {

        $this->authSID = $connect->getAuthSID();

        $this->curl = $connect->getCurl();

        $this->curl->setTimeout(30);

        $this->request = $connect->getRequest();

        $this->response = $connect->getResponse();

        $this->request->setMethod('GET');

    }

    public static function  setValidStocks(array $stocks)
    {
        self::$validStocks = $stocks;
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

            $shouldIterate = true;
            $i = 0;
            while ($shouldIterate) {
                $result = $this->getProductIterator($categoryID, $limit, $offset);

                if (false === $result) {
                    ++$i;
                    echo "Item List is empty try again\n";
                    if ($i >= 3) {
                        $shouldIterate = false;
                    }

                } else {
                    $shouldIterate = false;
                }

            }

            if (empty($result)) {
                echo "Finally can't gat a content\n";
                continue;
            }

            $allProducts = array_merge($allProducts, array_filter($result['list'], "self::setStocksForItem"));

            if ($result['count'] <= $limit || empty($result['list'])) {
                break;
            }

            $offset += $limit;

            echo "offset = $offset \n";

        }

        return $allProducts;
    }

    static private function setStocksForItem(&$product)
    {

        if (!empty($product['is_archive'])) {
            return false;
        }


        if (empty($product['stocks'])) {
            return false;
        }

        $stocksStrings = array();
        foreach ($product['stocks'] as $key => $value) {
            if (in_array($value, self::$validStocks)) {
                $stocksStrings[$value] = self::$stocks[$value];
            }
        }

        $product['stocks'] = $stocksStrings;

        // Проверяем снова стоки - если не остался ни один из доступных - выкидываем false
        if (empty($product['stocks'])) {
            return false;
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

        $products = $this->getResponse($resource);

        if (empty($products)) {
            return false;
        } else {
            return $products;
        }

    }


    private function getStocks()
    {
        $resource = "/stocks/{$this->authSID}";

        $result = $this->getResponse($resource);

        foreach ($result as $value) {
            self::$stocks[$value['stockID']] = $value['name'];
        }

    }


    public function getVendors()
    {
        return $this->getResponse("/vendors/{$this->authSID}");
    }


    private function getResponse($request)
    {

        try {
            $this->request->setResource($request);
            $this->curl->send($this->request, $this->response);

            if ($this->response->isSuccessful()) {
                $response = json_decode($this->response->getContent(), true);
            } else {
                throw new \Buzz\Exception\RuntimeException("Cant get content on $request", $this->response->getStatusCode());
            }

            if (empty($response['status'])) {
                throw new RuntimeException("Content with error {$response['status']}", $response['error_code']);
            }

            return $response['result'];

        } catch (Exception $e) {
            echo "Error {$e->getMessage()} with code: {$e->getCode()} on {$e->getFile()}:{$e->getLine()}";
        }

    }
}