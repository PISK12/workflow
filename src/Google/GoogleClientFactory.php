<?php

namespace App\Google;

use Google\Client;

final class GoogleClientFactory
{
	public function __invoke():Client
	{
		return new Client(['credentials'=>'/app/config/google/key.json']);
	}
}