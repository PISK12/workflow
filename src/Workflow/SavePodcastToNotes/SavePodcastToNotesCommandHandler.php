<?php

namespace App\Workflow\SavePodcastToNotes;

use App\Service\Podcast\PodcastCrawler;
use App\Service\Transcript\TranscriptFromAudio;
use App\TriliumNotes\Client;
use App\TriliumNotes\ClipperNote;
use App\TriliumNotes\ServerUrl;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class SavePodcastToNotesCommandHandler implements MessageHandlerInterface
{
	public function __construct(
		private readonly TranscriptFromAudio $transcriptFromAudio,
		private readonly Client $triliumClient,
		private readonly PodcastCrawler $podcastCrawler,
		private readonly NotifierInterface $notifier,
		private readonly ServerUrl $serverUrl,
	) {
	}

	/**
	 * @throws TransportExceptionInterface
	 */
	public function __invoke(SavePodcastToNotesCommand $cmd)
	{
		$podcastCrawl = $this->podcastCrawler->execute($cmd->uri);
		$client = HttpClient::create();
		$response = $client->request('GET', $podcastCrawl->uri);
		if ($response->getStatusCode() !== 200) {
			$notification = (new Notification('Can`t save podcast'))
				->content(
					sprintf(
						'Can`t save podcast from "%s"',
						$cmd->uri
					)
				)
				->importance(Notification::IMPORTANCE_HIGH);
			$this->notifier->send($notification, ...$this->notifier->getAdminRecipients());
			return;
		}
		$transcript = $this->transcriptFromAudio->__invoke($response->getContent());
		$note = new ClipperNote(
			clipType: 'note', content: $transcript, pageUrl: $cmd->uri, title: $podcastCrawl->title
		);
		$noteId = $this->triliumClient->createClipperNote($note);

		$notification = (new Notification('Saved podcast to notes'))
			->content(
				sprintf(
					'Saved podcast "%s" to notes "%s/#root/%s"',
					$podcastCrawl->title,
					$this->serverUrl->url,
					$noteId
				)
			)
			->importance(Notification::IMPORTANCE_LOW);
		$this->notifier->send($notification, ...$this->notifier->getAdminRecipients());
	}
}