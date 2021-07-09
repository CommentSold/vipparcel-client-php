<?php

use Psr\Http\Message\ResponseInterface;

class VP_Response {

    protected $_response;

    public function __construct(ResponseInterface $response)
    {
        $this->_response = $response;
    }

    public function response_client()
    {
        return $this->_response;
    }

    public function has_errors()
    {
        $result = $this->as_array();
        return $result['statusCode'] >= 400;
    }

    public function get_errors($single = TRUE)
    {
        $result = $this->as_array();
        if ( ! empty($result['error'])) {
            $errors = (array) $result['error'];
            return ($single ? array_shift($errors) : $errors);
        }
        return FALSE;
    }

    public function as_array()
    {
        return json_decode((string)$this->response_client()->getBody(), true);
    }

    public function as_object()
    {
        return json_decode((string)$this->response_client()->getBody(), false);
    }
}