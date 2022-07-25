<?php

require_once 'includes/db.php';

$filename = __DIR__ . '/data/articles.json';
$articles = [];
$categories = [];

$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$selectedCat = $_GET['cat'] ?? '';


$sth = $dbh->prepare('SELECT module_category_id FROM module');
$sth->execute();
$article = $sth->fetchAll();
print_r($article);

$sth_number_module = $dbh->prepare('SELECT module_category_id FROM webreath.module WHERE module_category_id = 1');
$sth_number_module->execute();
$results_number = $sth_number_module->fetchAll();
print_r($results_number);



if (file_exists($filename)) {
    $articles = json_decode(file_get_contents($filename), true) ?? [];
    $cattmp = array_map(fn ($article) => $article['category'],  $articles);
    $categories = array_reduce($cattmp, function ($acc, $cat) {
        if (isset($acc[$cat])) {
            $acc[$cat]++;
        } else {
            $acc[$cat] = 1;
        }
        return $acc;
    }, []);
    print_r($categories);


    $articlePerCategories = array_reduce($articles, function ($acc, $article) {
        if (isset($acc[$article['category']])) {
            $acc[$article['category']][] = $article;
        } else {
            $acc[$article['category']] = [$article];
        }
        return $acc;
    }, []);
}
?>



<!DOCTYPE html>
<html lang="fr">

<head>
    <?php require_once 'includes/head.php' ?>
    <link rel="stylesheet" href="/public/css/index.css">

    <title>WeBreathe</title>
</head>

<body>

    <div class="container">
        <?php require_once 'includes/header.php' ?>
        <div class="content">
            <div class="newsfeed-container">
                <div class="category-container">
                    <ul class="category-container">
                        <li class=<?= $selectedCat ? '' : 'cat-active' ?>>
                            <a href="/">Tous les articles <span class="small">(<?= count($article) ?>)</span></a>
                        </li>
                        <?php foreach ($categories as $catName => $catNum) : ?>
                        <li class=<?= $selectedCat ===  $catName ? 'cat-active' : '' ?>>
                            <a href="/?cat=<?= $catName ?>"> <?= $catName ?><span class="small">(<?= $catNum ?>)</span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="newsfeed-content">
                    <?php if (!$selectedCat) : ?>
                    <?php foreach ($categories as $cat => $num) : ?>
                    <h2><?= $cat ?></h2>
                    <div class="articles-container">
                        <?php foreach ($articlePerCategories[$cat] as $a) : ?>
                        <a href="/show-article.php?id=<?= $a['id'] ?>" class="article block">
                            <div class="overflow">
                                <div class="img-container" style="background-image:url(<?= $a['image'] ?>"> </div>
                            </div>
                            <h3><?= $a['title'] ?></h3>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>
                    <?php else : ?>
                    <h2><?= $selectedCat ?></h2>
                    <div class="articles-container">
                        <?php foreach ($articlePerCategories[$selectedCat] as $a) : ?>
                        <a href="/show-article.php?id=<?= $a['id'] ?>" class="article block">
                            <div class="overflow">
                                <div class="img-container" style="background-image:url(<?= $a['image'] ?>"></div>
                            </div>
                            <h3><?= $a['title'] ?></h3>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <a class="return-top" href="#top" id="btn-top"> Retour </a> <br><br>

        </div>
        <?php require_once 'includes/footer.php' ?>
    </div>









    <div class="container">
        <?php require_once 'includes/header.php' ?>
        <div class="content">
            <div class="newsfeed-container">
                <div class="category-container">
                    <ul class="category-container">
                        <li class=<?= $selectedCat ? '' : 'cat-active' ?>>
                            <a href="/">Tous les articles <span class="small">(<?= count($articles) ?>)</span></a>
                        </li>
                        <?php foreach ($categories as $catName => $catNum) : ?>
                        <li class=<?= $selectedCat ===  $catName ? 'cat-active' : '' ?>>
                            <a href="/?cat=<?= $catName ?>"> <?= $catName ?><span class="small">(<?= $catNum ?>)</span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="newsfeed-content">
                    <?php if (!$selectedCat) : ?>
                    <?php foreach ($categories as $cat => $num) : ?>
                    <h2><?= $cat ?></h2>
                    <div class="articles-container">
                        <?php foreach ($articlePerCategories[$cat] as $a) : ?>
                        <a href="/show-article.php?id=<?= $a['id'] ?>" class="article block">
                            <div class="overflow">
                                <div class="img-container" style="background-image:url(<?= $a['image'] ?>"> </div>
                            </div>
                            <h3><?= $a['title'] ?></h3>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>
                    <?php else : ?>
                    <h2><?= $selectedCat ?></h2>
                    <div class="articles-container">
                        <?php foreach ($articlePerCategories[$selectedCat] as $a) : ?>
                        <a href="/show-article.php?id=<?= $a['id'] ?>" class="article block">
                            <div class="overflow">
                                <div class="img-container" style="background-image:url(<?= $a['image'] ?>"></div>
                            </div>
                            <h3><?= $a['title'] ?></h3>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <a class="return-top" href="#top" id="btn-top"> Retour </a> <br><br>

        </div>
        <?php require_once 'includes/footer.php' ?>
    </div>

</body>

</html>