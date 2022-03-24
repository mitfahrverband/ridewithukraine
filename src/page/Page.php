<?php
namespace page;

use core\html\document\Document;

Document::addStylesheet('css/style.min.css');
Document::addStylesheet('trips.css');
Document::addScriptFile('js/script.js');

class Page extends Document {

    static function render(callable $renderFunction) {
        parent::render(function () use ($renderFunction) {
            ?>
            <div class="a text-3xld">Hi</div>
            <div class="a sticky top-0 flex text-3xld">
                <span class="mr-auto">Header</span>
                <span class="md:hidden menuButton">☰</span>
            </div>
            <div class="main grid-cols-3">
                <?php
                $renderFunction();
                ?>
            </div>
            <div class="a">Footer</div>
            <?php
        });
    }

}
