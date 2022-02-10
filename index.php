<?php

$db = new PDO('mysql:host=localhost;port=3306;dbname=avis;charset=utf8', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$reviews = $db->query('SELECT * FROM reviews')->fetchAll();

$name = $_POST['nom'] ?? null;
$comment = $_POST['comment'] ?? null;
$notation = $_POST['notation'] ?? null;
$photo = $_FILES['photo'] ?? null;
$created_at = date('Y-m-d H:i:s');
$success = null;
$errors = [];
$black = 0;
$pathh = null;

if (!empty($_POST)) {
    if (!empty($_FILES)) {
        if ($photo['size'] > 2048 * 1024) {
            $errors[] = "L'image doit faire 2Mo maximum.";
        }
    
        $mime = '';
        if (!empty($photo['tmp_name'])) {
            $mime = mime_content_type($photo['tmp_name']);
        }
        $mimeTypes = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($mime, $mimeTypes)) {
            $errors[] = 'Le fichier doit être une image';
        }

    }
    if (empty($name)) {
        $erreurs[] = 'Votre nom est requis.';
    }

    if (empty($comment)) {
        $erreurs[] = 'Votre commentaire est requis.';
    }

    if (empty($notation)) {
        $erreurs[] = 'Votre note doit être entre 1 et 5.';
    }

    if (empty($erreurs)) {
        if (!is_dir('uploads')) {
            mkdir('uploads');
        }
        $filename = $photo['name'];
        $file = pathinfo($filename);
        $filename = 'review'.'-'.uniqid().'.'.$file['extension'];
        move_uploaded_file($photo['tmp_name'], 'uploads/'.$filename);
        $pathh = 'uploads/'.$filename;
        $success = 'Votre commentaire a bien été ajouté.';
        $query = $db->prepare('INSERT INTO reviews (name, review, note, created_at, image) VALUES (:name, :review, :note, :created_at, :image)');
        $query->execute([
            'name' => $name, 'review' => $comment, 'note' => $notation, 'created_at' => $created_at, 'image' => $filename,
        ]);
    }
}


    function dateToFrench($date, $format) 
    {
        $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $french_days = array('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche');
        $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $french_months = array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
        return str_replace($english_months, $french_months, str_replace($english_days, $french_days, date($format, strtotime($date) ) ) );
    }

    $nb = 0;
    $total = 0;
    $moyenne = 0;
    $numb = [];
    $numb[1] = 0;
    $numb[2] = 0;
    $numb[3] = 0;
    $numb[4] = 0;
    $numb[5] = 0;
    
    foreach ($reviews as $review) {
        $nb++;
        $total += $review['note'];
        $numb[$review['note']]++;
    }

    $moyenne = ($total/$nb);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <title>Avis</title>
    <style>
        .gold {
            color: gold;
        }
        .container {
            display: flex;
        }
        .cercle{
            text-align:center;
            color:white;
            background: gold;
            border-radius:50%;
            width:80px;
            height: 80px;
        }
        table {
            margin-left: 20px;
        }
        td {
            width: 400px;
        }
        #comment {
            height: 100px;
        }
        table {
            border: 1px solid black;
        }

        .note {
            display: flex;
            align-items: center;
        }
        .progress {
            margin-left: 20px;
            margin-right: 20px;
            width: 500px;
        }
        h2 {
            margin-top: 20px;
        }
        table.list tr td{
            width: 800px;
        }
    </style>
</head>
<body>
    <div class="container">
        <table class="table">
            <tr>
                <td>Notre moyenne<td>
                <td></td>
            </tr>
            <tr>
                <td align=center><?php echo round($moyenne, 1).' / 5<br>';
                for ($i = 0; $i < round($moyenne, 0, PHP_ROUND_HALF_UP); $i++) { ?>
                    <i class="fa-solid fa-star gold"></i>
                <?php } echo '<br>'.$nb.' avis'; ?>
                </td>

                <td align=center>
                    <table>
                        <?php
                            for ($i = 5; $i >= 1; $i--) { ?>
                                <div class="note">
                                    <?php echo $i; ?>
                                    <i class="fa-solid fa-star gold"></i>
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo (($numb[$i]/$nb)*100).'%' ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <?php echo '('.$numb[$i].')'; ?>
                                </div>
                            <?php } ?>
                    </table>
                </td>
                
                <td align=center><h4>Laissez votre avis</h4>

                <button class="btn btn-primary">Noter</button>
                </td>
            </tr>
        </table>
    </div>

    <?php if ($success) { ?>
        <p class="alert alert-success"><?php echo $success; ?></p>
        <a href="index.php">Ajouter un autre commentaire</a>

        <?php $first =  $name;
        str_split($first, 1); ?>
        <div class="container">
            <div class="cercle">
            <h2><?php echo $first[0] ?></h2>
            </div>

            <div>
                <img src="<?php echo $success; ?>" alt="">
                <table class="table list">
                    <tr>
                        <td colspan="2"><?php echo $name?></td>
                    </tr>
                    <tr>
                        <td id="comment" colspan="2"><?php
                            $black = (5-$notation);
                            for ($i = 0; $i < $notation; $i++) { ?>
                                <i class="fa-solid fa-star gold"></i>
                            <?php }
                                for ($z = 0; $z < $black; $z++) { ?>
                                <i class="fa-solid fa-star"></i>
                            <?php } ?>
                            <?php echo '<br>'.$comment?>
                            <img align=right height="150" src="<?php echo $pathh?>" alt="">
                        </td>
                    </tr>
                    <tr>
                        <td id="jour"><?php echo dateToFrench($created_at, "l j F Y")?></td>
                    </tr>
                </table>
        </div>
        <?php } ?>

        <?php if (!$success) { ?>

    
        <div class="container">
            <table class="table">
                <tr>
                    <td>Publier un avis</td>
                </tr>
        
                <?php if (!empty($erreurs)) { ?>
                <tr>
                    <td>
                        <ul class="alert alert-danger">
                        <?php foreach ($erreurs as $erreur) { ?>
                            <li><?php echo $erreur; ?></li>
                        <?php } ?>
                        </ul>
                    </td>
                </tr>
                <?php } ?>
                        
                <tr>
                    <td align=center>
        
                    <form method="post" class="form-group" enctype="multipart/form-data">
                        <label for="nom">Nom</label>
                        <input name="nom" type="text" placeholder="Votre nom" class="form-control">
            
                        <label for="comment">Commentaire</label>
                        <textarea name="comment" id="" cols="30" rows="3" placeholder="Votre commentaire" class="form-control"></textarea>
            
                        <label label for="notation">Note</label>
                        <input type="radio" name="notation" id="1" value="1">1
                        <input type="radio" name="notation" id="2" value="2">2
                        <input type="radio" name="notation" id="3" value="3">3
                        <input type="radio" name="notation" id="4" value="4">4
                        <input type="radio" name="notation" id="5" value="5">5

                        <input type="file" name="photo" class="form-control">
            
                        <button class="form-control btn btn-primary">Noter</button>
                    </form>
                    </td>
                </tr>
            </table>
        </div>
        <?php } ?>
        

        <?php if (!$success) {

            foreach ($reviews as $review) {
                $photos = glob('uploads/*');

                $first =  $review['name'];
                str_split($first, 1); ?>
                <div class="container">
                    <div class="cercle">
                        <h2><?php echo $first[0] ?></h2>
                    </div>
                <div>
                    <table class="table list">
                        <tr>
                            <td colspan="2"><?php echo $review['name']?></td>
                        </tr>
                        <tr>
                            <td id="comment" colspan="2"><?php 
                            $black = (5-$review['note']);
                            for ($i = 0; $i < $review['note']; $i++) { ?>
                                <i class="fa-solid fa-star gold"></i>
                            <?php }
                            for ($z = 0; $z < $black; $z++) { ?>
                                <i class="fa-solid fa-star"></i>
                            <?php } ?>
                            <?php echo '<br>'.$review['review']?>
                            <img align=right height="150" src="<?php echo 'uploads/'.$review['image']; ?>" alt="">
                            </td>
                        </tr>
                            <tr>
                                <td align=right><?php echo dateToFrench($review['created_at'], "l j F Y à G:i")?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php } ?>
        <?php } ?>
    </body>
</html>
                
                