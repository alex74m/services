# services n°1 : Redimensionnement d'images

Ce service est prévu en premier lieu pour des ressources de type images jpg ou png.

## Utilisation

Création du service:
$editPhoto = new UploadImage($_FILES['ImageNews']);

## Fonctionnalités

Modification de la qualité en pourcent:
$editPhoto->qualityPercent(80); // Indique une dégradation de 20%(100% - 20% = 80%) par rapport à la qualité originale.
Par défaut la qualité est réglée à 100%.

Modification de la largeur en pixels:
$editPhoto->width(500); // Indique largeur de 500px quelque soit la largeur originale.
Par défaut la largeur est réglée à 500px.
