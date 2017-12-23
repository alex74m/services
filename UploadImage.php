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

	// Données d'objet
	private $imageUpload;
	private $imageUploadError;
	private $imageUploadName;
	private $imageUploadTmpName;
	private $imageUploadSize;
	private $imageUploadExtension;
	private $imageUploadMime;

	private $width;
	private $qualityPercent;
	private $newNameImage;


	//Contrains
	const MAX_UPLOAD_SIZE = 2097152;
	const WIDTH_INIT = 500;
	const QUALITY_PERCENT = 100;

	private $extensionAllowed;
	private $extensionEIAllowed;
	private $maxSizeUpload;

	//Traitement
	private $imageCopyTmpName;
	private $imageCopySize;
	private $pt_dst_x;
	private $pt_dst_y;
	private $pt_src_x;
	private $pt_src_y;
	private $pathFolderFile;


	public function __construct($imageUpload){
		$this->imageUpload = $imageUpload;
		$this->maxSizeUpload();
		$this->coordonate();
		$this->width();
		$this->qualityPercent();
		$this->newNameImage();
		$this->pathFolderFile();
	}

	//Traitement de base
	public function maxSizeUpload($maxSizeUpload = self::MAX_UPLOAD_SIZE){
		$this->maxSizeUpload = $maxSizeUpload;
		return $this->maxSizeUpload;
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

	public function pathFolderFile($pathFolderFile = null){
		if (!is_dir($pathFolderFile) & $pathFolderFile != null) {
			mkdir($pathFolderFile, 777);
		}
		$this->pathFolderFile = $pathFolderFile;
		return $this->pathFolderFile;
	}

	public function width($width = self::WIDTH_INIT){
		$this->width = $width;
	}
	public function qualityPercent($qualityPercent = self::QUALITY_PERCENT){
		$this->qualityPercent = $qualityPercent;
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
	/**
	 * Double vérification du MIME avec la fonction native de php : mime_content_type
	 */
	public function getImageUploadMime(){
		$checkMime = mime_content_type($this->getImageUploadTmpName());
		$this->imageUploadMime = getimagesize($this->getImageUploadTmpName());
		$this->imageUploadMime = $this->imageUploadMime['mime'];
		
		if ($checkMime == $this->imageUploadMime)
			return $this->imageUploadMime;
		else
			trigger_error("Les vérifications MIME ne correspondent pas.".$checkMime." != ".$this->imageUploadMime);
		
	}

	public function setImageCopyTmpNameInJPG(){
		$imageCopyTmpName = imagecreatefromjpeg($this->getImageUploadTmpName()); //créer une copie de votre image
		return $imageCopyTmpName;
	}
	public function setImageCopyTmpNameInPNG(){
		$imageCopyTmpName = imagecreatefrompng($this->getImageUploadTmpName()); //créer une copie de votre image
		return $imageCopyTmpName;
	}
	public function destroyImageCopyJPG(){
		imagedestroy($this->setImageCopyTmpNameInJPG());
	}
	public function destroyImageCopyPNG(){
		imagedestroy($this->setImageCopyTmpNameInPNG());
	}

	public function setImageCopySize(){
		$imageCopySize = getimagesize($this->getImageUploadTmpName()); //défini ses dimensions.
		return $imageCopySize;
	}
	public function newNameImage($name = null, $préfixe = null,$suffixe = null){
		if ($name != null)
			$this->nameNewImage = $préfixe.$name.$suffixe.time();
		else
			$this->nameNewImage = time();
		
		return $this->nameNewImage;
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

	public function resizeJPG()
	{
		if($this->getImageUploadMime() == $this->extensionAllowed()[$this->getImageUploadExtension()]  || 
			$this->getImageUploadMime() == $this->extensionEIAllowed()[$this->getImageUploadExtension()])
		{
			$copyTemporaryImage = $this->setImageCopyTmpNameInJPG(); //créer une copie de votre image
			$copyTemporaryImageSize = $this->setImageCopySize(); //défini ses dimensions.	

			$height = $this->resizingHeightProportional($this->width);

			$newImage = $this->newImage($this->width,$height);

			imagecopyresampled($newImage, $copyTemporaryImage, $this->pt_dst_x,$this->pt_dst_y,$this->pt_src_x,$this->pt_src_y, $this->width, $height, $copyTemporaryImageSize[0],$copyTemporaryImageSize[1]);
			
			//Destruction de l'image
			$this->destroyImageCopyJPG();

			//Modification de son nom
			imagejpeg($newImage , $this->pathFolderFile.$this->nameNewImage.'.'.$this->getImageUploadExtension(), $this->qualityPercent);
		
			return true;
		}
	}
	public function resizePNG()
	{
		if($this->getImageUploadMime() == $this->extensionAllowed()[$this->getImageUploadExtension()]  || 
			$this->getImageUploadMime() == $this->extensionEIAllowed()[$this->getImageUploadExtension()])
		{
			$copyTemporaryImage = $this->setImageCopyTmpNameInPNG(); //créer une copie de votre image
			$copyTemporaryImageSize = $this->setImageCopySize(); //défini ses dimensions.	

			$height = $this->resizingHeightProportional($this->width);

			$newImage = $this->newImage($this->width,$height);

			imagecopyresampled($newImage, $copyTemporaryImage, $this->pt_dst_x,$this->pt_dst_y,$this->pt_src_x,$this->pt_src_y, $this->width, $height, $copyTemporaryImageSize[0],$copyTemporaryImageSize[1]);
			
			//Destruction de l'image
			$this->destroyImageCopyPNG();

			//Modification de son nom
			if ($this->qualityPercent > 9) {
				$this->qualityPercent = ($this->qualityPercent/100) * 9;
			}
			imagepng($newImage , $this->pathFolderFile.$this->nameNewImage.'.'.$this->getImageUploadExtension(), $this->qualityPercent);
		
			return true;
		}
	}

	public function resize(){
		if ($this->getImageUploadError() > 0) {
			trigger_error("L'image contint une erreur.");
			return false;
		}
		if ($this->getImageUploadSize() > $this->maxSizeUpload) {
			trigger_error("La taille de l'image est trop grande. max : ". $this->maxSizeUpload);
			return false;
		}
		if($this->getImageUploadMime() == $this->extensionAllowed()[$this->getImageUploadExtension()]  || $this->getImageUploadMime() == $this->extensionEIAllowed()[$this->getImageUploadExtension()])
		{
			if(array_key_exists($this->getImageUploadExtension(), $this->extensionAllowed()) || array_key_exists($this->getImageUploadExtension(), $this->extensionEIAllowed()))
			{
	
				if ($this->getImageUploadExtension() == 'jpg' || $this->getImageUploadExtension() == 'jpeg') {
					$response = $this->resizeJPG();
					return $response;
				}
				if ($this->getImageUploadExtension() == 'png') {
					$response = $this->resizePNG();
					return $response;
				}
			}
		}else{
			trigger_error("Le MIME est incorrect.");
			return false;
		}
	}
}

if (!empty($_FILES['ImageNews'])) {
	$editPhoto = new UploadImage($_FILES['ImageNews']);
	$editPhoto->qualityPercent(80);
	$editPhoto->width(500);
	$editPhoto->maxSizeUpload(2000000);
	$editPhoto->coordonate(array(0,0,0,0));
	$editPhoto->newNameImage('name', 'prefixe', 'suffixe');
	$editPhoto->pathFolderFile('imagesnews/');
	$editPhoto->resize();
}



/*
----------------------- PROCEDURAL ----------------------
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


/*

Ce script n'apporte pas une sécurité optimale (faille du byte NULL par exemple)).
De plus il est préférable, quand cela est possible, d'utiliser la fonction system ou shell_exec pour connaître le type MIME du fichier de façon un peu plus certaine.

*/


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


<?php
/*

$last_line = system();

shell_exec('mkdir directory');

*/

?>

</body>
</html>
