<?php
namespace page;

use core\html\document\Document;

Document::addStylesheet('css/style.min.css');
Document::addScriptFile('js/script.js');

class Page extends Document {

    static function render(callable $renderFunction) {
        parent::render(function () use ($renderFunction) {
            ?>
            <div class="header">
                <div class="header-top text-3xld">
                    <p>Карпулінг —</p>
                    <p>Mitfahrgelegenheiten</p>
                    <p>für Kriegsgeflüchtete.</p>
                </div>
                <div class="header-bottom sticky top-0 flex text-3xld">
                    <span class="mr-auto">#RideWithUkraine</span>
                </div>
                <img class="h-24 absolute top-6 right-0" src="/img/auto_frontElement4.svg">
            </div>
            <div class="main grid-cols-3">
                <?php
                $renderFunction();
                ?>
            </div>
            <div class="footer">
                <div class="flex justify-center items-center">
                    <span class="mr-3">powered by</span>
                    <img src="https://avatars.githubusercontent.com/u/77687247?s=200&v=4" class="w-10 h-10">
                    <span class="text-xs md:text-base">Mitfahrverband</span>
                </div>
                <div class="end"></div>
            </div>
            <?php
        });
    }

}
