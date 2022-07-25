<?php

require_once 'includes/db.php';

const ERROR_REQUIRED = 'Veuillez renseigner ce champ';
const ERROR_TITLE_TOO_SHORT = 'Le titre est trop court';
const ERROR_CONTENT_TOO_SHORT = 'L\'article est trop court';
const ERROR_IMAGE_URL = 'L\'image doit être une URL valide';

// $filename = __DIR__ . '/data/articles.json';
$errors = [
  'module_name' => '',
  'module_category_id' => '',
  'module_image_url' => '',
  'module_description' => '',
];

/*if (file_exists($filename)) {
  $articles = json_decode(file_get_contents($filename), true) ?? [];
}*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $_POST = filter_input_array(INPUT_POST, [
    'module_name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'module_image_url' => FILTER_SANITIZE_URL,
    'module_category_id' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'module_description' => [
      'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
      'flags' => FILTER_FLAG_NO_ENCODE_QUOTES
    ]
  ]);

  $module_name = $_POST['module_name'] ?? '';
  $module_image_url = $_POST['module_image_url'] ?? '';
  $module_category_id = $_POST['module_category_id'] ?? '';
  $module_description = $_POST['module_description'] ?? '';

  if (!$module_name) {
    $errors['module_name'] = ERROR_REQUIRED;
  } elseif (mb_strlen($module_name) < 5) {
    $errors['module_name'] = ERROR_TITLE_TOO_SHORT;
  }

  if (!$module_image_url) {
    $errors['module_image_url'] = ERROR_REQUIRED;
  } elseif (!filter_var($module_image_url, FILTER_VALIDATE_URL)) {
    $errors['module_image_url'] = ERROR_IMAGE_URL;
  }

  if (!$module_category_id) {
    $errors['module_category_id'] = ERROR_REQUIRED;
  }

  if (!$module_description) {
    $errors['module_description'] = ERROR_REQUIRED;
  } elseif (mb_strlen($module_description) < 50) {
    $errors['module_description'] = ERROR_CONTENT_TOO_SHORT;
  }

  if (empty(array_filter($errors, fn ($e) => $e !== ''))) {
    /* $articles = [...$articles, [
      'module_name' => $module_name,
      'module_image_url' => $module_image_url,
      'module_category_id' => $module_category_id,
      'module_description' => $module_description,
    ]]; */

    // file_put_contents($filename, json_encode($articles));
    $sth = $dbh->prepare('INSERT INTO module (module_category_id, module_name, module_image_url, module_description) VALUES (:module_category_id, :module_name, :module_image_url, :module_description)');
    $sth->bindValue('module_category_id', $module_category_id);
    $sth->bindValue('module_name', $module_name);
    $sth->bindValue('module_image_url', $module_image_url);
    $sth->bindValue('module_description', $module_description);
    $sth->execute();

    header('Location: /');
  }
}

// Fetch the category list
$sth_category = $dbh->prepare('SELECT category_id, category_name FROM module_category');
$sth_category->execute();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php require_once 'includes/head.php' ?>
    <link rel="stylesheet" href="/public/css/add-article.css">

    <title>Ajouter un module</title>
</head>

<body>
    <div class="container">
        <?php require_once 'includes/header.php' ?>
        <div class="content">
            <div class="block p-20 form-container">
                <h1>Caractéristique du module</h1>
                <form action="/add-article.php" method="POST">

                    <div class="form-control">
                        <label for="module_name">Nom</label>
                        <input type="text" name="module_name" id="module_name" value=<?= $module_name ?? '' ?>>
                        <?php if ($errors['module_name']) : ?>
                        <p class="text-danger"><?= $errors['module_name'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-control">
                        <label for="module_image_url">Image (Via URL)</label>
                        <input type="text" name="module_image_url" id="module_image_url"
                            value=<?= $module_image_url ?? '' ?>>
                        <?php if ($errors['module_image_url']) : ?>
                        <p class="text-danger"><?= $errors['module_image_url'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-control">
                        <label for="module_category_id">Technologie</label>
                        <select name="module_category_id" id="module_category_id">
                            <?php foreach ($sth_category->fetchAll() as $rows) : ?>
                            <option value="<?= $rows['category_id'] ?>"><?= $rows['category_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($errors['module_category_id']) : ?>
                        <p class="text-danger"><?= $errors['module_category_id'] ?></p>
                        <?php endif; ?>
                    </div>

                    <!--
                    <div class="form-control">
                        <label for="measurement">Mesure à relevé</label>
                        <input type="text" name="module_name" id="module_name" value= <?= $module_name ?? '' ?>>
                        <?php if ($errors['module_name']) : ?>
                        <p class="text-danger"><?= $errors['module_name'] ?></p>
                        <?php endif; ?>
                    </div> -->

                    <div class="form-control">
                        <label for="module_description">Descriptif du module</label>
                        <textarea name="module_description" id="module_description"></textarea>
                        <?php if ($errors['module_description']) : ?>
                        <p class="text-danger"><?= $errors['module_description'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions">
                        <button class="btn btn-secondary" type="button">Annuler</button>
                        <button class="btn btn-primary" type="submit">Sauvegarder</button>
                    </div>
                </form>
            </div>
        </div>
        <?php require_once 'includes/footer.php' ?>
    </div>

</body>

</html>