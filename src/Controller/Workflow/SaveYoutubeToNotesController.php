<?php

namespace App\Controller\Workflow;

use App\Workflow\SaveYoutubeToNotes\SaveYoutubeToNotesCommand;
use League\Uri\Http;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class SaveYoutubeToNotesController
{
	#[Route('/w/save-youtube-to-notes',name: 'save-youtube-to-notes')]
	public function __invoke(Request $request,MessageBusInterface $messageBus, ValidatorInterface $validator):JsonResponse
	{
		$url=$request->query->get('url');
		$violations = $validator->validate($url,[new Url()]);
		if(count($violations)){
			throw new BadRequestException($violations->get(0)->getMessage());
		}
		$messageBus->dispatch(new SaveYoutubeToNotesCommand(Http::createFromString($url)));

		return new JsonResponse();

	}
}