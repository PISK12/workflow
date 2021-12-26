<?php

namespace App\TriliumNotes;

final class ServerUrl
{
	public function __construct(public readonly string $url)
	{
	}

	public static function create(string $url): ServerUrl
	{
		return new ServerUrl(rtrim($url,'/'));
	}
}