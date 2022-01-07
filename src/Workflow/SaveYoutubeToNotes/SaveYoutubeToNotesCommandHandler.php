<?php

namespace App\Workflow\SaveYoutubeToNotes;

use App\Service\Transcript\TranscriptFromAudio;
use App\TriliumNotes\Client;
use App\TriliumNotes\ClipperNote;
use App\TriliumNotes\ServerUrl;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

final class SaveYoutubeToNotesCommandHandler implements MessageHandlerInterface
{
	public function __construct(
		private readonly YoutubeDl $youtubeDl,
		private readonly NotifierInterface $notifier,
		private readonly TranscriptFromAudio $transcriptFromAudio,
		private readonly Client $triliumClient,
		private readonly ServerUrl $serverUrl,
	) {
	}

	/**
	 * @throws \Google\ApiCore\ValidationException
	 * @throws \Google\ApiCore\ApiException
	 */
	public function __invoke(SaveYoutubeToNotesCommand $cmd)
	{
		$path = sprintf('/tmp/%d-%s', time(), uniqid('', false));
		$fs = new Filesystem();
		$fs->mkdir($path);
		$collection = $this->youtubeDl->download(
			Options::create()
				->downloadPath($path)
				->extractAudio(true)
				->audioFormat('mp3')
				->audioQuality(0)
				->output('%(title)s.%(ext)s')
				->url($cmd->uri)
		);

		$audioPaths = [];
		foreach ($collection->getVideos() as $video) {
			if ($video->getError() !== null) {
				$notification = (new Notification('Can`t save youtube video'))
					->content(
						sprintf(
							'Can`t save youtube video from "%s"',
							$cmd->uri
						)
					)
					->importance(Notification::IMPORTANCE_HIGH);
				$this->notifier->send($notification, ...$this->notifier->getAdminRecipients());

			} else {
				$audioPaths[$video->getTitle()] = $video->getFilename();
			}
		}

		foreach ($audioPaths as $title => $audioPath) {
			$transcript = $this->transcriptFromAudio->__invoke(file_get_contents($audioPath));
			$note = new ClipperNote(
				clipType: 'note', content: $transcript, pageUrl: $cmd->uri, title: $title
			);
			$noteId = $this->triliumClient->createClipperNote($note);

			$notification = (new Notification('Saved youtube video to notes'))
				->content(
					sprintf(
						'Saved youtube video "%s" to notes "%s/#root/%s"',
						$title,
						$this->serverUrl->url,
						$noteId
					)
				)
				->importance(Notification::IMPORTANCE_LOW);
			$this->notifier->send($notification, ...$this->notifier->getAdminRecipients());
		}
		$fs->remove($path);
	}
}