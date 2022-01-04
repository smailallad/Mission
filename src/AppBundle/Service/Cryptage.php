<?php
namespace AppBundle\Service;
class Cryptage {

    protected $secret_key;
    protected $secret_iv;

    public function __construct($secret_key,$secret_iv) {
        $this->secret_key = (string) $secret_key;
        $this->secret_iv  = (string) $secret_iv;
    }
    public function my_encrypt($value) {
	    $output = false;
	    $encrypt_method = "AES-256-CBC";
	    $key = hash( 'sha256', $this->secret_key );
	    $iv = substr( hash( 'sha256', $this->secret_iv ), 0, 16 );
        $output = base64_encode( openssl_encrypt( $value, $encrypt_method, $key, 0, $iv ) );
	    return $output;
    }
    public function my_decrypt($value) {
	    $output = false;
	    $encrypt_method = "AES-256-CBC";
	    $key = hash( 'sha256', $this->secret_key );
	    $iv = substr( hash( 'sha256', $this->secret_iv ), 0, 16 );
	    $output = openssl_decrypt( base64_decode( $value ), $encrypt_method, $key, 0, $iv );
	    return $output;
    }
}
