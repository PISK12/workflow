<?php

namespace App\Service\Transcript;

interface Transcript
{
	/**
	 * @param string $content
	 * @return string
	 * @throw TranscriptException
	 */
	public function __invoke(string $content):string;
}