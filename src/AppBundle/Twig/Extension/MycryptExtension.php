<?php
namespace AppBundle\Twig\Extension;

use Twig\TwigFilter;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MycryptExtension extends \Twig_Extension
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function getFilters()
    {
        //return array(
            //'Mycrypt' => new \Twig_Filter_Method($this, 'crypter')
        //    'Mycrypt' => new \Twig_SimpleFilter('Mycrypt', array($this, 'Mycrypt'))
        //);
        return [
            new TwigFilter('Mycrypt', [$this, 'crypter']),
        ];
    } 

    public function crypter($value)
    {
        $secret_key = $this->container->getParameter('secret_key');
        $secret_iv = $this->container->getParameter('secret_iv');
        //dump($secret_key);dump($secret_iv);
	    $output = false;
	    $encrypt_method = "AES-256-CBC";
	    $key = hash( 'sha256', $secret_key );
	    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
        $output = base64_encode( openssl_encrypt( $value, $encrypt_method, $key, 0, $iv ) );
	    return $output;
    }

    public function getName()
    {
        return 'Mycrypt_extension';
    }

}