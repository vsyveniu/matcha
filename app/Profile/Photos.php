<?php

namespace App\Profile;

class Photos {
	private $container;

	public function __construct($container){
		$this->container = $container;
	}

	public function get($request) {
		$directory = $this->container->upload_directory;
		$images = $request->getUploadedFiles();
		if (!file_exists($directory)) {
		    mkdir($directory, 0777, true);
		}

		if(count($images['picture']) > 5)
			$images['picture'] = array_slice($images['picture'], 0, 5);
		foreach($images['picture'] as $image){
 			if ($image->getError() === UPLOAD_ERR_OK) {
            	$extension = pathinfo($image->getClientFilename(), PATHINFO_EXTENSION);
            	$basename = bin2hex(random_bytes(8));
            	$filename = sprintf('%s.%0.8s', $basename, $extension);
            	$filenames[] = $filename;

            	$image->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
        	}
		}
		
		if(isset($filenames))
			return implode(',', $filenames);

		return false;
	}

	public function toArray($userProfile) {
		$photos = array();

		if(!$userProfile || !$userProfile['photos'])
			$photos[] = 'profile-img.jpg';
		else
			$photos = explode(',', $userProfile['photos']);

		return $photos;		
	}
}

?>