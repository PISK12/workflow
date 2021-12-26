<?php

namespace App\Service\Podcast;

use League\Uri\Http;

final class PodcastCrawl
{
	public function __construct(public readonly string $title,public readonly Http $uri)
	{
	}
}