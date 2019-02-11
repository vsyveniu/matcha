<?php

namespace App\Controllers\User;

use App\Controllers\Controller;
use App\Pusher\CreateConnection;

class ShowProfileController extends Controller
{
	public function show($request, $response)
	{
		$param = $request->getQueryParams();
		$visited_id = $param['profile'];
		$user_id = $_SESSION['user'];
		$profile = $this->container->userProfile->getUserProfileById($visited_id);
		$tags = $this->container->tag->get_tags($param['profile']);
		$tags = array_map(function($elem){return $elem['tag'];}, $tags);
		$tags = array_unique($tags);
		$like = 'like';

		if(!$profile || $this->blocked->is_blocked($user_id, $visited_id) || !$this->userProfile->is_filled($visited_id) || $visited_id == $user_id)
			return $response->withRedirect($this->router->pathFor('search'));

		$this->container->notification->save($visited_id, $user_id, 2);

		$pusher = new CreateConnection();
		$pusher->notification_message($visited_id, $this->notification->getLastInsert($this->db->lastInsertId())->fetchAll());

		foreach ($tags as $key => &$value) {
			$value = '#'.$value;
		}
		$photos = $this->photos->toArray($profile[0]);
		$count = count($photos);
		$mainPhoto = $this->container->userProfile->get_mainPhoto($visited_id);
		$location = $this->container->userProfile->get_location($visited_id);

		if($this->likes->findLike($visited_id, $user_id))
			$like = 'dislike';
		if(isset($param['profile'])) {
			if($profile){
				$this->container->visits->save($param['profile']);
				$this->container->fame_rating->update($param['profile']);

				return $this->view->render($response, 'show.twig', [
					'photos' => $photos,
					'profile' => $profile[0],
					'fame_rating' => $this->fame_rating->getRating($param['profile']),
					'tags' => $tags,
					'mainPhoto' => $mainPhoto,
					'location' => $location,
					'id' =>$param['profile'],
					'len' => $count,
					'like' => $like
				]);
			}
		}

		return $response->withRedirect($this->router->pathFor('search'));
	}


	public function postShow($request, $response)
	{
		$blocker = $_SESSION['user'];
		$params = $request->getParsedBody();
		foreach ($params as &$value) {
			$value = htmlspecialchars($value);
		}
		$res = $this->container->search->block_user($params['id'], $blocker);
		if($res)
			$this->chats->disable($blocker, $params['id']);
		return $response->withJson($res);		
	}


}

?>