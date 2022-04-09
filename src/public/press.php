<?php
use page\Page;

require_once "../core/Autoload.php";

Page::setName('press');

Page::addStylesheet('/css/text.css');

Page::render(function () {
    ?>
    <div class="col-span-3 mx-3">
        <h1>Presse</h1>
    </div>
    <?php
});
