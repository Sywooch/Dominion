<?php
/**
 * User: Rus
 * Date: 01.09.13
 * Time: 23:05
 */

class BrainPriceImport_Connect implements BrainPriceImport_ConnectorInterface
{

    /**
     * @var string
     */
    static private $authSID = null;

    /**
     * @var \Buzz\Client\Curl
     */
    private $curl;

    /**
     * @var \Buzz\Message\Request
     */
    private $request;

    /**
     * @var \Buzz\Message\Response
     */
    private $response;

    private $login;

    private $password;

    /**
     * @param \Buzz\Client\Curl      $curl
     * @param \Buzz\Message\Request  $request
     * @param \Buzz\Message\Response $response
     * @param  string                $login
     * @param  string                $password
     */
    public function __construct($curl, $request, $response, $login, $password)
    {
        $this->curl = $curl;
        $this->request = $request;
        $this->response = $response;

        $this->login = $login;
        $this->password = $password;


    }

    /**
     * @inheritdoc
     */
    public function getCurl()
    {
        return $this->curl;
    }

    /**
     * @inheritdoc
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @inheritdoc
     */
    public function getRequest()
    {
        return $this->request;
    }

    public function setHost($host)
    {
        $this->request->setHost($host);
    }


    /**
     * Get an auth SID from api.brain.com.ua
     *
     * @return null|string
     * @throws RuntimeException
     */
    public function getAuthSID()
    {
        if (!empty(self::$authSID)) {
            return self::$authSID;
        }

        if (null == $this->request->getHost()) {
            throw new RuntimeException('Should set a Host');
        }

        $this->request->setMethod('POST');
        $this->request->setResource('/auth');
        $this->curl->setTimeout(20);

        $options = array();

        $options[CURLOPT_POSTFIELDS] = array('login' => $this->login, 'password' => md5($this->password));

        $this->curl->send($this->request, $this->response, $options);

        $authResponse = json_decode($this->response->getContent());

        if ($authResponse->status == 0) {
            throw new RuntimeException($authResponse->error_message, $authResponse->error_code);
        }

        self::$authSID = $authResponse->result;

        return self::$authSID;
    }

}