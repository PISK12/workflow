<?php

namespace App\TriliumNotes;

final class AccessToken
{
	public function __construct(public readonly string $token)
	{
	}
}