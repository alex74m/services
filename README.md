# services n°1 : Redimensionnement d'image  :star: :star: :star: :milky_way:

Ce service est prévu en premier lieu pour des ressources de type images jpg ou png.

## Utilisation

Création du service :<br/>
**$editPhoto = new UploadImage($_FILES['ImageNews']);** // la ressource $_FILES est obligatoire<br/>
<br/>
Redimensionnement de l'image :<br/>
**$editPhoto->resize();** // sans arguments<br/>

<table>
    <thead>
        <tr>
            <th align="center">Utilité</th>
            <th align="center">Méthode</th>
            <th align="center">Argument</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Qualité</td>
            <td align="center">qualityPercent()</td>
            <td align="center">$quality [int]</td>
        </tr>
        <tr>
            <td>Dimension</td>
            <td align="center">width()</td>
            <td align="center">$width [int]</td>
        </tr>
        <tr>
            <td>Poids</td>
            <td align="center">maxSizeUpload()</td>
            <td align="center">$size [int]</td>
        </tr>
        <tr>
            <td>Coordonnées</td>
            <td align="center">coordonate()</td>
            <td align="center">$coordonate [array]</td>
        </tr>
        <tr>
            <td>Nom</td>
            <td align="center">qualityPercent()</td>
            <td align="center">$name [string], $prefixe [string], $suffixe [string]</td>
        </tr>
        <tr>
            <td>Dossier</td>
            <td align="center">pathFolderFile()</td>
            <td align="center">$folder [string]</td>
        </tr>
    </tbody>
</table>

## Fonctionnalités

### Modification de la qualité de la nouvelle image en pourcent:
Méthode : **qualityPercent($quality)**<br/>
Argument : $quality type integer<br/>
$editPhoto->qualityPercent(80); // Indique une dégradation de 20%(100% - 20% = 80%) par rapport à la qualité originale.<br/>
Par défaut la qualité est réglée à 100%.<br/>

### Modification de la largeur en pixel:
Méthode : **width($width)**   <br/>
Argument : $width type integer   <br/>
$editPhoto->width(500); // Indique une largeur de 500px quelque soit la largeur originale.  <br/> 
Par défaut la largeur est réglée à 500px.   <br/>

### Modification du poid acceptable en octet:
Méthode : **maxSizeUpload($size)**<br/>
Argument : $size type integer  <br/> 
$editPhoto->maxSizeUpload(2000000); // Indique le poid maximum de l'image avant son upload (ici 2000000 octets).  <br/> 
Par défaut la largeur est réglée à 2097152 octets.  <br/> 

### Modification des coordonnées de la nouvelle image en pixel:
Méthode : **coordonate($coordonate)**<br/>
Argument : $coordonate type array -> array(dst_x,dst_y,src_x,src_y) <br/>
dst_x : coordonnées du point de destination.  <br/>
dst_y : coordonnées du point de destination.  <br/>
src_x : coordonnées du point source.  <br/>
src_y : coordonnées du point source.   <br/>
$editPhoto->coordonate(array(0,0,0,0)); // Créer une image cadrée aux origines sur le point de destination et source. <br/>  
Par défaut la largeur est réglée à 0,0,0,0 pixel, soit aucuns décalages. <br/>

### Modification du nom de l'image qui sera enregistrée:
Méthode : **newNameImage($name, $prefixe, $suffixe)**  <br/> 
Arguments : $name, $prefixe, $suffixe type string  <br/> 
$editPhoto->newNameImage('name', 'prefixe', 'suffixe'); // Applique un nom, un préfixe, un suffixe et une concaténation unique    avec la fonction time() sur le nom de la nouvelle image.   <br/>  
Par défaut les valeurs $name, $prefixe, $suffixe sont nulles.  <br/>
En cas de valeurs nulles, l'image est renommée avec la fonction time() uniquement. <br/> 

### Modification du dossier d'enregistrement:
Méthode : **pathFolderFile($folder)**  <br/>
Argument : $folder type string <br/> 
$editPhoto->pathFolderFile('images/'); // Indique le dossier dans lequel l'image sera enregistré.  <br/> 
Si le dossier n'existe pas, celui-ci sera créé.  <br/>
Par défaut l'image est enregistrée dans le dossier courant.  <br/>
   
<br/><br/>
*Ne pas oublier d'appliquer la méthode **resize()** pour finaliser l'enregistrement de la ressources.* <br/>
