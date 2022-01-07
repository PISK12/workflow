<?php

namespace App\Workflow\SaveYoutubeToNotes;

use League\Uri\Http;

final class SaveYoutubeToNotesCommand
{
	public function __construct(public readonly Http $uri)
	{
	}
}