<?php
namespace Tonolucro\Delivery\Merchant\Http;

use Tonolucro\Delivery\Merchant\Http\Environment\EnvironmentInterface;

class Client
{
    /**
     * @var Auth
     */
    protected $auth;
    /**
     * @var EnvironmentInterface
     */
    protected $environment;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $adapter = null;

    /**
     * @return \GuzzleHttp\Client
     */
    public function getAdapter(){
        if( $this->adapter == null ){
            $this->adapter = new \GuzzleHttp\Client([
                'base_uri' => $this->environment->getApiUri()
            ]);
        }
        return $this->adapter;
    }

    /**
     * Client constructor.
     * @param Auth $auth
     * @param EnvironmentInterface $environment
     */
    public function __construct(Auth $auth, EnvironmentInterface $environment)
    {
        $this->auth = $auth;
        $this->environment = $environment;
        return $this;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    protected function request($method, $uri, $params=[]){
        try {
            $params['auth'] = [$this->auth->getUsername(), $this->auth->getPassword()];

            return $this->getAdapter()->request(
                $method,
                $this->environment->getApiUri().$uri,
                $params
            );

        }catch (\Exception $exception){
            throw $exception;
        }
    }

    /**
     * @param string $uri
     * @return array
     * @throws \Exception
     */
    public function get($uri){
        try {

            $response =  $this->getAdapter()->put(
                $this->environment->getApiUri().$uri,
                [
                    "headers" => ['Content-Type' => 'application/json'],
                    "auth" => [$this->auth->getUsername(), $this->auth->getPassword()]
                ]
            );

            if($response->getReasonPhrase()=="OK"){
                return json_decode($response->getBody()->getContents(), true);
            }

            throw new \Exception("");

        }catch (\Exception $exception){
            throw $exception;
        }
    }

    /**
     * @param $uri
     * @param array $body
     * @return mixed
     * @throws \Exception
     */
    public function put($uri, array $body){
        try {

            $response =  $this->getAdapter()->put(
                $this->environment->getApiUri().$uri,
                [
                    "headers" => ['Content-Type' => 'application/json'],
                    "auth" => [$this->auth->getUsername(), $this->auth->getPassword()],
                    "body" => json_encode($body),
                ]
            );

            if($response->getReasonPhrase()=="OK"){
                return json_decode($response->getBody()->getContents(), true);
            }

            throw new \Exception("");

        }catch (\Exception $exception){
            throw $exception;
        }
    }

}