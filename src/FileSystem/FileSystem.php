<?php

namespace App\FileSystem;

interface FileSystem
{
	public function write(string $content,string $path): void;

	public function delete(string $path): void;

	public function rewrite(string $content,string $path): void;
}