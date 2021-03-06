<?php
namespace page;

use core\html\document\Document;
use core\http\Url;
use core\language\Label;

Label::addFile(__DIR__ . '/../labels/labels_de.properties');

Document::addIcon('image/svg+xml', '/img/exporte_pikto_mitfahren_v2Element 1.svg');
Document::addStylesheet(Url::version('/css/style.min.css'));
Document::addScriptFile(Url::version('/js/script.js'));

class Page extends Document {

    static function render(callable $renderFunction) {
        parent::render(function () use ($renderFunction) {
            ?>
            <div class="header-top">
                <a href="/">
                    <div><?= Label::get('headerTop') ?></div>
                </a>
            </div>
            <div class="header-bottom">
                <span class="mr-auto"><a href="/"><?= Label::get('headerBottom') ?></a></span>
            </div>
            <?php LanguageMenu::render(); ?>
            <img class="header-logo" src="/img/exporte_pikto_mitfahren_v2Element 1.svg">
            <div class="flex"><a id="lang-btn" href="#lang-menu">Language</a></div>
            <div class="main">
                <?php $renderFunction(); ?>
            </div>
            <div class="footer">
                <div class="logo">
                    <div class="text-base ml-3 md:ml-12">
                        <p>Initiated and powered by</p>
                    </div>
                    <a href="https://mitfahrverband.org">
                        <img src="/img/Mitfahrverband_eV_logo_lang.png" class="h-20 mr-12">
                    </a>
                </div>
                <div class="bar"></div>
            </div>
            <?php
        });
    }

}
