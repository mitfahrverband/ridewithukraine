<?php
use core\language\Label;
use page\Page;

require_once "../core/Autoload.php";

Page::setName('press');

Page::addStylesheet('/css/text.css');

Page::render(function () {
    ?>
    <div id="press" class="col-span-3 mx-3">
        <div>
            <h1><?= Label::get('press.title') ?></h1>
            <div class="links">
              <ul>
                <li><a href="https://drive.google.com/drive/u/1/folders/1v0_ji5kT9dR2oLlDgsBUDUSSUWFDjZ2w"
                ><?= Label::get('press.pdf') ?></a></li>
                <li><a href="https://drive.google.com/drive/u/1/folders/1lGYDaW7jJthLT-s251IHZM0ERQ65vcGN"
                ><?= Label::get('press.text') ?></a></li>
                <li><a href="https://drive.google.com/drive/u/1/folders/1JVSbwYi16tbTs6Dz1PmR84oSGyVmEoIK"
                ><?= Label::get('press.highRes') ?></a></li>
                <li><a href="https://drive.google.com/drive/u/1/folders/1A8hQUaKXXEAfFFmOqbbhkxu_gr0niDkp"
                ><?= Label::get('press.social') ?> : @ridewithukraine #ridewithukraine</a></li>
              </ul>
            </div>
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
