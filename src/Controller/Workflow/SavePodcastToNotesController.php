<?php

namespace App\Controller\Workflow;

use App\Workflow\SavePodcastToNotes\SavePodcastToNotesCommand;
use App\Workflow\SavePodcastToNotes\SavePodcastToNotesCommandHandler;
use League\Uri\Http;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class SavePodcastToNotesController
{
	#[Route('/w/save-podcast-to-notes',name: 'save-podcast-to-notes')]
	public function __invoke(Request $request,MessageBusInterface $messageBus, SavePodcastToNotesCommandHandler $savePodcastToNotesCommandHandler, ValidatorInterface $validator):Response
	{
		$url=$request->query->get('url');
		$violations = $validator->validate($url,[new Url()]);
		if(count($violations)){
			throw new BadRequestException($violations->get(0)->getMessage());
		}
		$messageBus->dispatch(new SavePodcastToNotesCommand(Http::createFromString($url)));

		return new JsonResponse();
	}
}