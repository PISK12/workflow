<?php

namespace App\FileSystem;

use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageClient;

class GoogleStorageFileSystem implements FileSystem
{
	private readonly Bucket $bucket;

	public function __construct(StorageClient $storageClient, string $bucketName)
	{
		$this->bucket = $storageClient->bucket($bucketName);
	}

	public function write(string $content, string $path): void
	{
		$this->bucket->upload($content,['name'=>$path]);
	}

	public function delete(string $path): void
	{
		$object = $this->bucket->object($path);
		$object->delete();
	}

	public function rewrite(string $content, string $path): void
	{
		$this->delete($path);
		$this->write($content,$path);
	}
}