<?php

/* remarque :
- si l'on utilise imagecreatefrompng il faut que l'image de départ ne dépasse pas 
1 040 000 pixels (1040 x 1000). 
Ce n'est pas efficace.
- si vous utilisez la fonction équivalente pour les images de 
format GIF (imagecreatefromgif), vous devez disposer des librairies GD dans leur 
version inférieure à 1.6, ou supérieure à 2.0.28. 
Entre ces deux versions, le format n'est pas supporté par GD.
*/


class UploadImage
{
	private $imageUpload;
	private $imageUploadError;
	private $imageUploadName;
	private $imageUploadTmpName;
	private $imageUploadSize;
	private $imageUploadExtension;
	private $imageUploadMime;
	private $extensionAllowed;
	private $extensionEIAllowed;
	//Traitement
	private $imageCopyTmpName;
	private $imageCopySize;
	private $pt_dst_x;
	private $pt_dst_y;
	private $pt_src_x;
	private $pt_src_y;

	private $maxSizeUpload;

	public function __construct($imageUpload){
		$this->imageUpload = $imageUpload;
		$this->maxSizeUpload = $this->maxSizeUpload();
	}
	public function getImageUploadError(){
		$this->imageUploadError = $this->imageUpload['error'];
		return $this->imageUploadError;
	}
	public function getImageUploadName(){
		$this->imageUploadName = $this->imageUpload['name'];
		return $this->imageUploadName;
	}
	public function getImageUploadTmpName(){
		$this->imageUploadTmpName = $this->imageUpload['tmp_name'];
		return $this->imageUploadTmpName;
	}
	public function getImageUploadExtension(){
		$this->imageUploadExtension = explode('.', $this->getImageUploadName());
		$countImageUploadExtension = count($this->imageUploadExtension)-1;
		$this->imageUploadExtension = strtolower($this->imageUploadExtension[$countImageUploadExtension]);
		return $this->imageUploadExtension;
	}
	public function getImageUploadSize(){
		$this->imageUploadSize = $this->imageUpload['size'];
		return $this->imageUploadSize;
	}
	public function extensionAllowed(){
		$this->extensionAllowed = array('jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif');
		return $this->extensionAllowed;
	}
	public function extensionEIAllowed(){
		$this->extensionEIAllowed = array('jpg' => 'image/pjpg', 'jpeg'=>'image/pjpeg');
		return $this->extensionEIAllowed;
	}
	public function getImageUploadMime(){
		$this->imageUploadMime = getimagesize($this->getImageUploadTmpName());
		$this->imageUploadMime = $this->imageUploadMime['mime'];
		return $this->imageUploadMime;
	}

	public function setImageCopyTmpNameInJPG(){
		$imageCopyTmpName = imagecreatefromjpeg($this->getImageUploadTmpName()); //créer une copie de votre image
		return $imageCopyTmpName;
	}
	public function setImageCopyTmpNameInPNG(){
		$imageCopyTmpName = imagecreatefrompng($this->getImageUploadTmpName()); //créer une copie de votre image
		return $imageCopyTmpName;
	}
	public function setImageCopySize(){
		$imageCopySize = getimagesize($this->getImageUploadTmpName()); //défini ses dimensions.
		return $imageCopySize;
	}
	public function destroyImageCopyJPG(){
		imagedestroy($this->setImageCopyTmpNameInJPG());
	}
	public function destroyImageCopyPNG(){
		imagedestroy($this->setImageCopyTmpNameInPNG());
	}

	public function newNameImage($préfixe = null,$suffixe = null){
		$name = $préfixe.time().$suffixe;
		return $name;
	}

	public function newImage($newWidth, $newHeight){
		$newImage = imagecreatetruecolor($newWidth , $newHeight);
		if ($newImage == false) {
			trigger_error("Un erreur dans la fonction 'imagecreatetruecolor' c'est produite.");
		}
		return $newImage;
	}

	public function resizingHeightProportional($width){

		$copyTemporaryImageSize = $this->setImageCopySize();
		$reduction = (($width * 100)/$copyTemporaryImageSize[0]);
		$height = (($copyTemporaryImageSize[1] * $reduction)/100);
		return $height;
	}
	public function coordonate($coordonates = array(0,0,0,0)){
		$pt = [];
		//coordonnées du point de destination
		$this->pt_dst_x = $coordonates[0];
		//coordonnées du point de destination
		$this->pt_dst_y = $coordonates[1];
		//coordonnées du point source
		$this->pt_src_x = $coordonates[2];
		//coordonnées du point source
		$this->pt_src_y = $coordonates[3];
		return $pt;
	}

	public function maxSizeUpload($maxSizeUpload = 2097152){
		$this->maxSizeUpload = $maxSizeUpload;
		return $this->maxSizeUpload;
	}

	public function resizeJPG($width,$qualityPercent = 100, $prefixeNameFile, $pathFolderFile)
	{
		if($this->getImageUploadMime() == $this->extensionAllowed()[$this->getImageUploadExtension()]  || 
			$this->getImageUploadMime() == $this->extensionEIAllowed()[$this->getImageUploadExtension()])
		{
			$copyTemporaryImage = $this->setImageCopyTmpNameInJPG(); //créer une copie de votre image
			$copyTemporaryImageSize = $this->setImageCopySize(); //défini ses dimensions.	

			$height = $this->resizingHeightProportional($width);

			$newImage = $this->newImage($width,$height);

			imagecopyresampled($newImage, $copyTemporaryImage, $this->pt_dst_x,$this->pt_dst_y,$this->pt_src_x,$this->pt_src_y, $width, $height, $copyTemporaryImageSize[0],$copyTemporaryImageSize[1]);
			
			//Destruction de l'image
			$this->destroyImageCopyJPG();

			//Modification de son nom
			$nameNewImage = $this->newNameImage($prefixeNameFile);

			imagejpeg($newImage , $pathFolderFile.$nameNewImage.'.'.$this->getImageUploadExtension(), $qualityPercent);
		
			return true;
		}
	}
	public function resizePNG($width,$qualityPercent = 9, $prefixeNameFile, $pathFolderFile)
	{
		if($this->getImageUploadMime() == $this->extensionAllowed()[$this->getImageUploadExtension()]  || 
			$this->getImageUploadMime() == $this->extensionEIAllowed()[$this->getImageUploadExtension()])
		{
			$copyTemporaryImage = $this->setImageCopyTmpNameInPNG(); //créer une copie de votre image
			$copyTemporaryImageSize = $this->setImageCopySize(); //défini ses dimensions.	

			$height = $this->resizingHeightProportional($width);

			$newImage = $this->newImage($width,$height);

			imagecopyresampled($newImage, $copyTemporaryImage, $this->pt_dst_x,$this->pt_dst_y,$this->pt_src_x,$this->pt_src_y, $width, $height, $copyTemporaryImageSize[0],$copyTemporaryImageSize[1]);
			
			//Destruction de l'image
			$this->destroyImageCopyPNG();

			//Modification de son nom
			$nameNewImage = $this->newNameImage($prefixeNameFile);
			if ($qualityPercent > 9) {
				$qualityPercent = ($qualityPercent/100) * 9;
			}
			imagepng($newImage , $pathFolderFile.$nameNewImage.'.'.$this->getImageUploadExtension(), $qualityPercent);
		
			return true;
		}
	}

	public function resize($width = 100, $qualityPercent, $prefixeNameFile, $pathFolderFile = null){
		if ($this->getImageUploadError() > 0) {
			trigger_error("L'image contint une erreur.");
			return false;
		}
		if ($this->getImageUploadSize() > $this->maxSizeUpload) {
			trigger_error("La taille de l'image est trop grande.");
			return false;
		}
		if($this->getImageUploadMime() == $this->extensionAllowed()[$this->getImageUploadExtension()]  || $this->getImageUploadMime() == $this->extensionEIAllowed()[$this->getImageUploadExtension()])
		{
			if(array_key_exists($this->getImageUploadExtension(), $this->extensionAllowed()) || array_key_exists($this->getImageUploadExtension(), $this->extensionEIAllowed()))
			{
	
				if ($this->getImageUploadExtension() == 'jpg' || $this->getImageUploadExtension() == 'jpeg') {
					$response = $this->resizeJPG($width, $qualityPercent, $prefixeNameFile, $pathFolderFile);
					return $response;
				}
				if ($this->getImageUploadExtension() == 'png') {
					$response = $this->resizePNG($width, $qualityPercent, $prefixeNameFile, $pathFolderFile);
					return $response;
				}
			}
		}else{
			trigger_error("Le MIME est incorrect.");
			return false;
		}			



	}
}


//$maxSizeUpload = 2097152;
$widthImage = 350;
$qualityPercent = 100;
$prefixeNameFile = 'okok';
$pathFolderFile = 'imagesnews/';
$coordonates = array(50,50,250,250);

if (!empty($_FILES['ImageNews'])) {
	$editPhoto = new UploadImage($_FILES['ImageNews']);
	//$editPhoto->maxSizeUpload(200);
	$editPhoto->coordonate($coordonates);
	$editPhoto->resize($widthImage, $qualityPercent, $prefixeNameFile, $pathFolderFile);
}



/*
if (!empty($_FILES['ImageNews']))
{
	if ($_FILES['ImageNews']['error'] <= 0)
	{
	    if ($_FILES['ImageNews']['size'] <= $maxSizeUpload)
	    {
	        $ImageNews = $_FILES['ImageNews']['name'];
	
			$ListeExtension = array('jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif');
			$ListeExtensionIE = array('jpg' => 'image/pjpg', 'jpeg'=>'image/pjpeg');
			$ExtensionPresumee = explode('.', $ImageNews);
			$countTabExtensionPresumee = count($ExtensionPresumee)-1;
			$ExtensionPresumee = strtolower($ExtensionPresumee[$countTabExtensionPresumee]);
			if ($ExtensionPresumee == 'jpg' || $ExtensionPresumee == 'jpeg' || $ExtensionPresumee == 'pjpg' || $ExtensionPresumee == 'pjpeg' || $ExtensionPresumee == 'gif' || $ExtensionPresumee == 'png')
			{
				$ImageNews = getimagesize($_FILES['ImageNews']['tmp_name']);
				if($ImageNews['mime'] == $ListeExtension[$ExtensionPresumee]  || $ImageNews['mime'] == $ListeExtensionIE[$ExtensionPresumee])
				{
					$ImageChoisie = imagecreatefromjpeg($_FILES['ImageNews']['tmp_name']); //créer une copie de votre image
					$TailleImageChoisie = getimagesize($_FILES['ImageNews']['tmp_name']); //défini ses dimensions.
					// Étape 1 :
					$width = 350;
					// Étape 2 :
					$reduction = (($width * 100)/$TailleImageChoisie[0]);
					// Étape 3 :
					$height = (($TailleImageChoisie[1] * $reduction)/100);
					//Etape 1 :
					$nouvelleImage = imagecreatetruecolor($width , $height) or die ("Erreur");
					//Etape 2 :
					imagecopyresampled($nouvelleImage , $ImageChoisie, 0, 0, 0, 0, $width, $height, $TailleImageChoisie[0],$TailleImageChoisie[1]);
					imagedestroy($ImageChoisie);
					// Destruction de l'image et modification de son nom
					$NomImageChoisie = explode('.', $_FILES['ImageNews']['name']);
					$nameNewImage = time($NomImageChoisie[0]);
					imagejpeg($nouvelleImage , 'imagesnews/'.$nameNewImage.'.'.$ExtensionPresumee, 100);
				}
			}
		}
	}
}*/
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Autoloader d'image.</title>
</head>
<body>

<form method="post" action="" enctype="multipart/form-data">
<fieldset class="formulaire_news">    
        <p>
                <label for="image">Image : </label>
                <input type="file" name="ImageNews" id="image" />
        </p>
        <p>
                <input type="submit" name="InsererNews" value="Insérer" />
        </p>
</fieldset>
</form>

</body>
</html>
