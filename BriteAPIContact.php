<?php

class BriteAPIContact {

    private $api_key;
    private $options;
    private $fields = array('name','phone','ip','email','address');
    public $response;

    public $name;
    public $phone;
    public $ip;
    public $email;
    public $address;


    function __construct($api_key, $fields = array(), $options = array()) {

        $this->api_key = $api_key;
        if (empty($this->api_key)){
            throw new InvalidArgumentException("api_key required");
        }
        $this->options = $options;

        foreach ($this->fields as $field) {
            if (isset($fields[$field])){
               $this->$field = $fields[$field];
            }
        }
    }


    public function verify() {

        $data = array();
        foreach ($this->fields as $field) {
            if (!empty($this->$field)){
                $data[$field] = $this->$field;
            }
        }

        $client = new BriteAPIClient($this->api_key, $data);
        $this->response = $client->verify();

        return $this->is_valid();
    }

    public function is_valid() {
        if ($this->response == null) return null;

        $valid = true;
        foreach ($this->response as $res) {
            if (isset($res['status']) && $res['status'] != 'valid') {
                $valid = false;
            }
        }
        return $valid;
    }

    # valid -> unknown -> invalid
    public function status() {
        if ($this->response == null) return null;
        $status = 'valid';
        foreach ($this->response as $res) {
            if (isset($res['status'])) {
                if ($res['status'] == 'invalid') {
                    $status = 'invalid';
                } elseif ($res['status'] == 'unknown' && $status != 'invalid') {
                    $status = 'unknown';
                }

            }
        }
        return $status;
    }

    public function errors() {
        if ($this->response == null) return null;
        $errors = array();
        foreach ($this->response as $field => $res) {
            if (!empty($res['error'])) {
                $errors[$field] = $res['error'];
            }
        }

        return $errors;
    }

    public function error_codes() {
        if ($this->response == null) return null;
        $codes = array();
        foreach ($this->response as $field => $res) {
            if (!empty($res['error_code'])) {
                $codes[] =  $res['error_code'];
            }
        }

        return array_unique($codes);
    }




}