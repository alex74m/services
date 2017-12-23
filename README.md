# services n°1 : Redimensionnement d'images

Ce service est prévu en premier lieu pour des ressources de type images jpg ou png.

## Utilisation

Création du service:
$editPhoto = new UploadImage($_FILES['ImageNews']);

## Fonctionnalités

Modification de la qualité en pourcent:
Méthode : qualityPercent($quality)
Argument : $quality type integer
$editPhoto->qualityPercent(80); // Indique une dégradation de 20%(100% - 20% = 80%) par rapport à la qualité originale.
Par défaut la qualité est réglée à 100%.

Modification de la largeur en pixels:
Méthode : width($width)
Argument : $width type integer
$editPhoto->width(500); // Indique une largeur de 500px quelque soit la largeur originale.
Par défaut la largeur est réglée à 500px.

Modification du poid acceptable en octet:
Méthode : maxSizeUpload($size)
Argument : $size type integer
$editPhoto->maxSizeUpload(2000000); // Indique le poid maximum de l'image avant son upload (ici 2000000 octets).
Par défaut la largeur est réglée à 2097152 octets.

Modification des coordonnées de la nouvelle image en pixel:
Méthode : coordonate($coordonate)
Argument : $coordonate type array -> array(dst_x,dst_y,src_x,src_y)
dst_x : coordonnées du point de destination.
dst_y : coordonnées du point de destination.
src_x : coordonnées du point source.
src_y : coordonnées du point source.
$editPhoto->coordonate(array(0,0,0,0)); // Créer une image cadrée aux origines sur le point de destination et source.
Par défaut la largeur est réglée à 0,0,0,0 pixel.

Modification du nom de l'image qui sera enregistrée:
Méthode : newNameImage($name, $prefixe, $suffixe)
Arguments : $name, $prefixe, $suffixe type string
$editPhoto->newNameImage('name', 'prefixe', 'suffixe'); // Applique un nom, un préfixe, un suffixe et une concaténation unique avec la fonction time() sur le nom de la nouvelle image.
Par défaut les valeurs $name, $prefixe, $suffixe sont nulles.
En cas de valeurs nulles, l'image est renommées avec la fonction time() uniquement.
