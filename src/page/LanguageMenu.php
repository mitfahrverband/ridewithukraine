<?php
namespace page;

class LanguageMenu {

    static function render(string $btnId = 'lang-btn') {
        ?>
        <div id="lang-menu" class="hidden">
            <ul>
                <li data-code="UA">UA - Українська</li>
                <li data-code="RU">RU - русский</li>
                <li data-code="DE">DE - Deutsch</li>
                <li data-code="EN">EN - English</li>
                <li data-code="PL">PL - język polski</li>
                <li data-code="SK">SK - slovenčina</li>
                <li data-code="HU">HU - magyar</li>
                <li data-code="RO">RO - Română</li>

                <script type="module">
                  let $langMenu = $("#lang-menu");
                  $langMenu.hide = () => {
                    $langMenu.toggleClass("hidden");
                    $("body, html").toggleClass("overflow-hidden");
                  };
                  $("#<?= $btnId ?>").onClick(() => $langMenu.hide());
                  $langMenu.onClick(() => $langMenu.hide());
                  $("#lang-menu li").onClick(e => {
                    e.stopPropagation();
                    window.location = `?lang=${e.target.dataset.code}`;
                  });
                </script>
            </ul>
        </div>
        <?php
    }

}
