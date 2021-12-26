<?php

namespace App\Service\Podcast;

use League\Uri\Http;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class PodcastStrategyCrawler implements PodcastCrawler
{
	public function __construct(private readonly PodcastPocketCastCrawler $podcastPocketCastCrawler)
	{
	}

	public function execute(Http $uri): PodcastCrawl
	{
		if($uri->getHost()==='pca.st'){
			return $this->podcastPocketCastCrawler->execute($uri);
		}

		throw new \RuntimeException();
	}
}