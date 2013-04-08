<?php
/**
 * PHP client for BriteVerify API
 *
 * @link https://github.com/BriteVerify/brite-api-php
 * @link https://www.briteverify.com/
 * @link http://dev.iron.io/
 * @version 0.0.2
 * @copyright MIT
 */

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

    /**
     * Initalize new contact
     *
     * @param string $api_key
     * @param array $fields array of contact fields
     * @param array $options additonal options. Supported fields:
     *  - verify_connected: If you pass an additional parameter of "verify_connected=true", and the email is valid, we will then scan the online networks, wishlists, public directories, social networks, photo sharing sites (basically the internet itself) to see if the email is "connected" to other active accounts.
     */
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

    /**
     * Verifies contact
     *
     * @return bool
     */
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

    /**
     * Check contact validity.
     *
     * @return bool|null
     */
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

    /**
     * Contact status
     * returns null if verify() didn't called yet
     * returns statuses in that order:
     * valid -> unknown -> invalid
     *
     * @return null|string
     */
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

    /**
     * Returns associated array in field => error form
     *
     * @return array|null
     */
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

    /**
     * Returns plain array of error codes
     *
     * @return array|null
     */
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