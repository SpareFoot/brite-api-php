<?php

class BriteAPIClient {

    const api_host = 'https://bpi.briteverify.com';

    private $api_key;
    private $options;
    private $fields;


    /**
     * Create API client for contact.
     *
     * @param string $api_key
     * @param array $fields
     * @param array $options
     */
    function __construct($api_key, $fields = array(), $options = array()) {
        $this->api_key = $api_key;
        if (empty($this->api_key)){
            throw new InvalidArgumentException("api_key required");
        }
        $this->options = $options;
        $this->fields = $fields;
    }

    function verify() {
        $requests = array();
        foreach ($this->fields as $field => $value) {
            $requests[$field] = $this->$field($value);
        }

        return $this->multi_request($requests);
    }

    private function multi_request($requests) {
        $mh = curl_multi_init();
        $curl_array = array();
        foreach($requests as $i => $url) {
            $curl_array[$i] = curl_init($url);
            curl_setopt($curl_array[$i], CURLOPT_RETURNTRANSFER, true);
            curl_multi_add_handle($mh, $curl_array[$i]);
        }
        $running = null;
        do {
            curl_multi_exec($mh, $running);
            usleep(10000); // 10 ms
        } while ($running > 0);

        $res = array();
        foreach($requests as $i => $url)
        {
            $res[$i] = json_decode(curl_multi_getcontent($curl_array[$i]), true);
        }

        foreach($requests as $i => $url){
            curl_multi_remove_handle($mh, $curl_array[$i]);
        }
        curl_multi_close($mh);

        return $res;
    }

    private function name($value) {
        $url = self::api_host . '/names.json';
        $params = array('fullname' => $value, 'apikey' => $this->api_key);
        return $url . '?' . http_build_query($params);
    }

    private function address($value) {
        $url = self::api_host . '/addresses.json';
        if (!is_array($value)) {
            throw new InvalidArgumentException("address should be array");
        }
        $params = array('address' => $value, 'apikey' => $this->api_key);
        return $url . '?' . http_build_query($params);
    }

    private function email($value) {
        $url = self::api_host . '/emails.json';

        $params = array('address' => $value, 'apikey' => $this->api_key);
        if (isset($this->options['verify_connected']) && $this->options['verify_connected']) {
            $params['verify_connected'] = 'true';
        }
        return $url . '?' . http_build_query($params);
    }

    private function phone($value) {
        $url = self::api_host . '/phones.json';
        $params = array('number' => $value, 'apikey' => $this->api_key);
        return $url . '?' . http_build_query($params);
    }

    private function ip($value) {
        $url = self::api_host . '/ips.json';
        $params = array('address' => $value, 'apikey' => $this->api_key);
        return $url . '?' . http_build_query($params);
    }








}