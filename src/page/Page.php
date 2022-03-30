<?php
namespace page;

use core\html\document\Document;
use core\language\Label;

Document::addIcon('image/svg+xml', '/img/auto_frontElement4.svg');
Document::addStylesheet('css/style.min.css');
Document::addScriptFile('js/script.js');

Label::addFile(__DIR__ . '/../labels/labels_de.properties');

class Page extends Document {

    static function render(callable $renderFunction) {
        parent::render(function () use ($renderFunction) {
            ?>
            <div class="header-top text-3xld">
                <a href="/">
                    <div><?= Label::get('headerTop') ?></div>
                </a>
            </div>
            <div class="header-bottom">
                <span class="mr-auto"><a href="/"><?= Label::get('headerBottom') ?></a></span>
                <span class="menuButton">☰</span>
                <div class="menu hidden absolute top-full right-0"><?php Menu::render() ?></div>
                <script>
                  $('.menuButton').onClick((e) => {
                    e.target.classList.toggle('bg-primary');
                    $('.header-bottom .menu').classList.toggle('hidden');
                  })
                </script>
            </div>
            <img class="logo" src="/img/auto_frontElement4.svg">
            <div class="main grid-cols-3">
                <?php
                $renderFunction();
                ?>
            </div>
            <div class="footer font-sans">
                <div class="flex justify-center items-center">
                    <span class="mr-3">powered by</span>
                    <img src="/img/Mitfahrverband_eV_logo_lang.png" class="h-20" style="margin: -15px">
                </div>
                <div class="end bg-mitfahrverband h-4"></div>
            </div>
            <?php
        });
    }

}
