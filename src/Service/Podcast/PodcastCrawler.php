<?php

namespace App\Service\Podcast;

use League\Uri\Http;

interface PodcastCrawler
{
	public function execute(Http $uri):PodcastCrawl;
}