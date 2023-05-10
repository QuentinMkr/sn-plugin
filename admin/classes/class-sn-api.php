<?php

class Savage_Note_Api
{
    private $authorization = '';
    private $api_url = "https://app.savage-note.com/api/v2";

    public function __construct(){
        $options = get_option('sn_options');
        $this->authorization = 'Bearer ' . $options['api_key'];
    }

    public function get($call, $data = [])
    {
        
        $args = [
            'headers' => [
                'Authorization' => $this->authorization
            ]
        ];

        if(!empty($data)){
            $response = wp_remote_get( $this->api_url . $call . '?' . http_build_query($data), $args );
        }
        else{
            $response = wp_remote_get( $this->api_url . $call, $args );
        }

        // echo json_encode($response);
        // die;

        if($response){
            return json_decode($response['body'], true);
        }else{
            return 'Error';
        }

    }

    public function post($call, $data = []){
        $args = array(
            'headers' => [
                'Authorization' => $this->authorization,
            ],
            'body'        => $data,
            'timeout' => 100
        );

        $response = wp_remote_post( $this->api_url . $call, $args );


        return json_decode($response['body']);

    }
}