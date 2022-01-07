<?php

namespace App\Google;

use YoutubeDl\YoutubeDl;

final class YoutubeDlFactory
{
	public function __invoke(): YoutubeDl
	{
		$yt = new YoutubeDl();
		$yt->setPythonPath('/usr/bin/python3');
		$yt->setBinPath('/usr/local/bin/youtube-dl');
		return $yt;
	}
}