<?php
namespace page;

class LanguageMenu {

    static function render(string $btnId = 'lang-btn') {
        ?>
        <div id="lang-menu">
            <div>
                <a href="?lang=UA">UA - Українська</a>
                <a href="?lang=RU">RU - русский</a>
                <a href="?lang=DE">DE - Deutsch</a>
                <a href="?lang=EN">EN - English</a>
                <a href="?lang=FR">FR - français</a>
                <a href="?lang=PL">PL - język polski</a>
                <a href="?lang=SK">SK - slovenčina</a>
                <a href="?lang=HU">HU - magyar</a>
                <a href="?lang=RO">RO - Română</a>
            </div>
        </div>
        <?php
    }

}
