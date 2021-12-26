<?php

namespace App\Workflow\SavePodcastToNotes;

use League\Uri\Http;

final class SavePodcastToNotesCommand
{
	public function __construct(public readonly Http $uri)
	{
	}
}