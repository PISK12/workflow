<?php

namespace App\Service\Podcast;

use League\Uri\Http;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class PodcastPocketCastCrawler implements PodcastCrawler
{
	public function execute(Http $uri): PodcastCrawl
	{
		$browser = new HttpBrowser(HttpClient::create());
		$crawler = $browser->request('GET', (string)$uri);

		$fileUrl = $crawler->filter('.download-button>a')->first()->link();
		$title = $crawler->filter('head>title')->first()->innerText();

		return new PodcastCrawl(title: $title, uri: Http::createFromString($fileUrl->getUri()));
	}
}