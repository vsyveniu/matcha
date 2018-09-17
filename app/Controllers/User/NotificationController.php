<?php

namespace App\Controllers\User;

use App\Controllers\Controller;

class NotificationController extends Controller{
	public function notifications($request, $response) {
		return $this->view->render($response, 'user/notofications.twig', [
					'visits' => $this->visits->getByVisitorId($_SESSION['user'], 0, 5)->fetchAll(),
					'guests' => $this->visits->getByVisitedId($_SESSION['user'], 0, 5)->fetchAll(),
					'likes' => $this->likes->getByLikeId($_SESSION['user'], 0, 5)->fetchAll(),
					'liked' => $this->likes->getByLikedId($_SESSION['user'], 0, 5)->fetchAll()
				]);
	}

	public function live($request, $response) {
		$now = $request->getParsedBody();
		$now = $now['now'];
		echo $now.'/';
		//echo date('Y/m/d H:i:s', $now).'////';
		//echo date('Y/m/d H:i:s', time()).'|||||||';
		echo time().'|||||||';

		$data = $this->visits->getByVisitedId($_SESSION['user'], 0, 15);
		if($data->rowCount()) {
			$data = $data->fetchAll();
			foreach($data as $key => $row){
				//echo $now - strtotime($row['time']).',';
				echo strtotime($row['time']).'/';
				if(($now - strtotime($row['time'])) < 0){
					echo $row['time'].'/';
				}
			}
		}
		exit();
	}

	public function post($request, $response){
		$post = $request->getParsedBody();
		$response = 'reachedMax';

		switch ($post['type']) {
			case 'guests':
				$data = $this->visits->getByVisitedId($_SESSION['user'], $post['start'], $post['limit']);
				if($data->rowCount()) {
					$data = $data->fetchAll();
					foreach($data as $key => $row){
						$response .= '
							<tr>
				              <td>
				                <div class="form-check">
				                  <input type="checkbox" class="form-check-input" name="guests'.$key.'" value="'.$row["id"].'">
				                </div>
				              </td>
				              <td>
				                <a href="{{ base_url() }}/show?profile='.$row["visitor_id"].'">'.$row["firstName"].' '.$row["lastName"].'</a> wached youre profile at '.$row["time"].'
				              </td>
				            </tr>
						';
					}
				}
				break;
			case 'visits':
				$data = $this->visits->getByVisitorId($_SESSION['user'], $post['start'], $post['limit']);
				if($data->rowCount()) {
					foreach($data as $key => $row){
						$response .= '
							<tr>
				              <td>
				                <div class="form-check">
				                  <input type="checkbox" class="form-check-input" name="guests'.$key.'" value="'.$row["id"].'">
				                </div>
				              </td>
				              <td>
								You wached <a href="{{ base_url() }}/show?profile='.$row["visited_id"].'">'.$row["firstName"].' '.$row["lastName"].'</a> '.$row["time"].'
				              </td>
				            </tr>
						';
					}
				}
				break;
			case 'likes':
				$data = $this->likes->getByLikeId($_SESSION['user'], $post['start'], $post['limit']);
				if($data->rowCount()) {
					foreach($data as $key => $row){
						$response .= '
							<tr>
				              <td>
				                <div class="form-check">
				                  <input type="checkbox" class="form-check-input" name="guests'.$key.'" value="'.$row["id"].'">
				                </div>
				              </td>
				              <td>
								You liked <a href="{{ base_url() }}/show?profile='.$row["liked_id"].'">'.$row["firstName"].' '.$row["lastName"].'</a> '.$row["time"].'
				              </td>
				            </tr>
						';
					}
				}
				break;
			case 'liked':
				$data = $this->likes->getByLikedId($_SESSION['user'], $post['start'], $post['limit']);
				if($data->rowCount()) {
					foreach($data as $key => $row){
						$response .= '
							<tr>
				              <td>
				                <div class="form-check">
				                  <input type="checkbox" class="form-check-input" name="guests'.$key.'" value="'.$row["id"].'">
				                </div>
				              </td>
				              <td>
								<a href="{{ base_url() }}/show?profile='.$row["like_id"].'">'.$row["firstName"].' '.$row["lastName"].'</a> liked you '.$row["time"].'
				              </td>
				            </tr>
						';
					}
				}
				break;
		}

		echo $response;
		exit();
	}
}

?>