<?php

namespace App\TriliumNotes;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class Client
{
	public function __construct(
		private readonly ServerUrl $serverUrl,
		private readonly AccessToken $token,
		private readonly HttpClientInterface $httpClient
	) {
	}

	private function request(string $method, string $path, ?array $body): ResponseInterface
	{
		return $this->httpClient->request($method, sprintf('%s%s', $this->serverUrl->url, $path), [
			'headers' => [
				'Authorization' => $this->token->token,
			],
			'json' => $body,
		]);
	}

	public function createClipperNote(ClipperNote $clipperNote):NoteId{
		$response = $this->request('POST','/api/clipper/notes',$clipperNote->toArray());
		if($response->getStatusCode()!==200){
			throw new \RuntimeException();
		}
		return new NoteId($response->toArray()['noteId']);
	}
}