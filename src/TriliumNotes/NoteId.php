<?php

namespace App\TriliumNotes;

final class NoteId implements \Stringable
{
	public function __construct(public readonly string $noteId)
	{
	}

	public function __toString(): string
	{
		return $this->noteId;
	}
}