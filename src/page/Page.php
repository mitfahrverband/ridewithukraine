<?php
namespace page;

use core\html\document\Document;

Document::addStylesheet('css/style.min.css');
Document::addScriptFile('js/script.js');

class Page extends Document {

    static function render(callable $renderFunction) {
        parent::render(function () use ($renderFunction) {
            ?>
            <div class="header-top text-3xld">
                <a href="/">
                    <p>Карпулінг —</p>
                    <p>Mitfahrgelegenheiten</p>
                    <p>für Kriegsgeflüchtete.</p>
                </a>
            </div>
            <div class="header-bottom sticky md:static top-0 flex text-3xld">
                <a href="/"><span class="mr-auto">#RideWithUkraine</span></a>
                <span class="md:hidden menuButton">☰</span>
                <div class="md:hidden menu hidden absolute top-full right-0"><?php Menu::render() ?></div>
                <script>
                  $('.menuButton').onClick((e) => {
                    e.target.classList.toggle('bg-primary');
                    $('.header-bottom .menu').classList.toggle('hidden');
                  })
                </script>
            </div>
            <img class="h-24 absolute top-6 right-0 pointer-events-none	z-20" src="/img/auto_frontElement4.svg">
            <div class="main grid-cols-3">
                <?php
                $renderFunction();
                ?>
            </div>
            <div class="footer font-sans">
                <div class="flex justify-center items-center">
                    <span class="mr-3">powered by</span>
                    <img src="https://avatars.githubusercontent.com/u/77687247?s=200&v=4" class="w-10 h-10">
                    <span class="text-xs md:text-base">Mitfahrverband</span>
                </div>
                <div class="end bg-mitfahrverband h-4"></div>
            </div>
            <?php
        });
    }

}
