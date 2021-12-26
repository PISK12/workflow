<?php

namespace App\Google;

use Google\Cloud\Storage\StorageClient;

final class GoogleStorageFactory
{
	public function __invoke(string $projectId):StorageClient
	{
		$config=[
			'keyFilePath'=>'/app/config/google/key.json',
			'projectId'=>$projectId,
		];
		return new StorageClient($config);
	}
}