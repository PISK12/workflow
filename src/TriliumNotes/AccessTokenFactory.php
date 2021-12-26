<?php

namespace App\TriliumNotes;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class AccessTokenFactory
{
	public function __construct(private readonly HttpClientInterface $httpClient, private readonly ServerUrl $serverUrl)
	{
	}

	public function __invoke(string $username,string $password):AccessToken
	{
		$response = $this->httpClient->request('POST',sprintf('%s/api/login/token',$this->serverUrl->url),[
			'headers'=>[
				'Accept'=>'application/json',
				'Content-Type'=>'application/json'
			],
			'json'=>['username'=>$username,'password'=>$password],
		]);
		if($response->getStatusCode()===401){
			throw new \RuntimeException($response->getStatusCode());
		}elseif($response->getStatusCode()!==200){
			throw new \RuntimeException($response->getStatusCode());
		}else{
			return new AccessToken($response->toArray()['token']);
		}
	}
}