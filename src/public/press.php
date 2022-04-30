<?php
use page\Page;

require_once "../core/Autoload.php";

Page::setName('press');

Page::addStylesheet('/css/text.css');

Page::render(function () {
    ?>
    <div id="press" class="col-span-3 mx-3">
        <h1>Presse</h1>
        <div class="images">
            <?php
            $files = glob(__DIR__ . '/img/press/*.*');
            foreach ($files as $file) {
                $file = basename($file);
                ?>
                <a href="/img/press/<?= $file ?>">
                    <img src="/img/press/scaled/<?= $file ?>.w480.jpg">
                </a>
                <?php
            }
            ?>
        </div>
    </div>
    <?php
});
