<?php

namespace App\Service\Transcript;

use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\SpeechClient;
use Google\Cloud\Speech\V1p1beta1\RecognitionConfig\AudioEncoding;
use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageClient;

class TranscriptFromAudio implements Transcript
{
	private readonly Bucket $bucket;

	public function __construct(StorageClient $storageClient, string $bucketName)
	{
		$this->bucket = $storageClient->bucket($bucketName);
	}

	/**
	 * @throws \Google\ApiCore\ApiException
	 * @throws \Google\ApiCore\ValidationException
	 */
	public function __invoke(string $content): string
	{
		$fileObject = $this->bucket->upload($content, ['name' => uniqid('tmp_', false)]);
		try{
			$encoding = AudioEncoding::MP3;
			$sampleRateHertz = 44100;
			$languageCode = 'pl-PL';

			$audio = (new RecognitionAudio())->setUri($fileObject->gcsUri());
			$config = (new RecognitionConfig())
				->setEncoding($encoding)
				->setSampleRateHertz($sampleRateHertz)
				->setLanguageCode($languageCode);

			$client = new SpeechClient([
				'credentials' => '/app/config/google/key.json',
			]);
			$operation = $client->longRunningRecognize($config, $audio);
			$operation->pollUntilComplete();

			if ($operation->operationSucceeded()) {
				$response = $operation->getResult();

				// each result is for a consecutive portion of the audio. iterate
				// through them to get the transcripts for the entire audio file.
				$results = [];
				foreach ($response->getResults() as $result) {
					$alternatives = $result->getAlternatives();
					$mostLikely = $alternatives[0];
					$transcript = $mostLikely->getTranscript();
					$results[] = $transcript;
				}
				$stringResult = implode('<br><br>', $results);
				if (!empty($stringResult)) {
					$fileObject->delete();
					return $stringResult;
				}
			}

			throw new TranscriptException();
		}catch (\Exception $exception){
			$fileObject->delete();
			throw $exception;
		}
	}
}