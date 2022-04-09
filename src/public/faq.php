<?php
use core\language\Label;
use page\Page;

require_once "../core/Autoload.php";

Page::setName('faq');

Page::addStylesheet('/css/text.css');

Page::render(function () {
    $entries = getEntries();
    ?>
    <div id="faq" class="col-span-3 mx-3">
        <h1>FAQ</h1>
        <div class="entries">
            <?php
            foreach ($entries as $entry) renderEntry(...$entry);
            ?>
        </div>
    </div>
    <?php
});

function renderEntry(string $question, string $answer) {
    ?>
    <details>
        <summary><?= $question ?></summary>
        <div>
            <?= $answer ?>
        </div>
    </details>
    <?php
}

function getEntries(): array {
    $entries = [];
    $i = 1;
    while ($question = Label::getOrDefault("faq.$i.question", false)) {
        $entries[] = [
            'question' => $question,
            'answer' => Label::get("faq.$i.answer"),
        ];
        $i++;
    }
    return $entries;
}
