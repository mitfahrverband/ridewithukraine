<?php
use page\Page;

require_once "../core/Autoload.php";

Page::setName('print');
Page::addStylesheet('/css/text.css');
Page::render(function () {
    ?>
    <h1>Hi</h1>
    <?php
});
