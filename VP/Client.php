<?php

use GuzzleHttp\Client;

class VP_Client {

    // @TODO version, url, authToken in config

    const URL_PRODUCTION = 'https://vipparcel.com/api/v1';
    const URL_TEST = 'https://vipparcel.com/api-test/v1';

    protected $_request;
    protected $_auth_token;
    protected $_is_test = FALSE;

    public function auth_token($value)
    {
        $this->_auth_token = $value;
        return $this;
    }

    public function is_test($value)
    {
        $this->_is_test = (bool) $value;
        return $this;
    }

    public function request(VP_Abstract_Request $request)
    {
        $this->_request = $request;
        return $this;
    }

    protected function _client_url()
    {
        if ($this->_is_test === TRUE) {
            return self::URL_TEST;
        }
        return self::URL_PRODUCTION;
    }

    protected function _default_params()
    {
        if ( ! $auth_token = $this->_auth_token) {
            throw new VP_Exception('Required parameter not passed: authToken');
        }
        return array('authToken' => $auth_token);
    }

    public function execute()
    {
        if ($this->_request === NULL) {
            throw new VP_Exception('Request object does not exist');
        }

        $request_method = $this->_request->get_method();
        $request_url = $this->_request->get_url();
        $send_params = array_merge($this->_default_params(), $this->_request->get_params());
        $http_client = new Client();

        $full_url = $this->_client_url().$request_url;

        if (in_array($request_method, array('put', 'post')))
        {
            $params_key = 'form_params';
        }
        else
        {
            $params_key = 'query';
        }
        $response = $http_client->$request_method($full_url, [
            $params_key => $send_params,
            'http_errors' => false
        ]);
        return new VP_Response($response);
    }

}
