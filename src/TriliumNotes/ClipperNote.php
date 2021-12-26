<?php

namespace App\TriliumNotes;

use JetBrains\PhpStorm\ArrayShape;

final class ClipperNote
{
	public function __construct(public readonly string $clipType, public readonly string $content, public readonly ?string $pageUrl, public readonly string $title)
	{
	}

	#[ArrayShape([
		'clipType' => "string",
		'content' => "string",
		'pageUrl' => "null|string",
		'title' => "string"
	])]
	public function toArray():array{
		return [
			'clipType'=>$this->clipType,
			'content'=>$this->content,
			'pageUrl'=>$this->pageUrl,
			'title'=>$this->title,
		];
	}
}