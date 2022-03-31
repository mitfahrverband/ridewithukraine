<?php
use core\language\Label;
use page\Page;

require_once "../core/Autoload.php";

Page::render(function () {
    ?>
    <div class="col-span-1 space-y-10">
        <?php
        renderIntro();
        //    renderPlatform();
        renderSteps();
        ?>
    </div>
    <?php

    ?>
    <div class="flex-1 col-span-2 flex flex-col mt-10">
        <div class="flex-1 relative flex justify-center items-center">
            <?php renderIframe(); ?>
        </div>
    </div>
    <?php
});

function renderIntro() {
    ?>
    <div class="intro">
        <div>
            <h2><?= Label::get('intro.platform.text') ?></h2>
            <a href="#steps"><?= Label::get('intro.platform.button') ?></a>
        </div>
        <div>
            <h2><?= Label::get('intro.print.text') ?></h2>
            <a><?= Label::get('intro.print.button') ?></a>
        </div>
        <p><?= Label::get('intro.subText') ?></p>
    </div>
    <?php
}

function renderSteps() {
    ?>
    <div id="steps">
        <div>
            <h1><?= Label::get('platform.steps.title') ?></h1>
            <h2><?= Label::get('platform.steps.subTitle') ?></h2>
        </div>
        <?php
        renderStep1();
        renderStep2();
        renderStep3();
        renderStep4();
        renderStep5();
        ?>
    </div>
    <?php
}

function renderStep1() {
    ?>
    <div id="step1" class="step">
        <div class="title">
            <h2><span>1</span>/5 — <?= Label::get('platform.step1.title') ?></h2>
            <img src="/img/exporte_pikto_mitfahrenElement%2013.svg">
        </div>
        <div class="actions">
            <div>
                <input type="radio" name="mode" value="searching" checked
                       style="background-image: url('/img/exporte_pikto_mitfahrenElement 18.svg')">
                <p><?= Label::get('platform.step1.searching') ?></p>
            </div>
            <div>
                <input type="radio" name="mode" value="driving"
                       style="background-image: url('/img/exporte_pikto_mitfahrenElement 15.svg')">
                <p><?= Label::get('platform.step1.driving') ?></p>
            </div>
        </div>
    </div>
    <?php
}

function renderStep2() {
    ?>
    <div id="step2" class="step">
        <div class="title">
            <h2><span>2</span>/5 — <?= Label::get('platform.step2.title') ?></h2>
            <img src="/img/exporte_pikto_mitfahrenElement%209.svg">
        </div>
        <div class="actions">
            <input name="departureTime" type="datetime-local">
            <input name="departureTimeOffset" type="hidden">
            <button>
                <p>🡣</p>
                <?= Label::get('platform.step2.now') ?>
            </button>
            <script>
              let [time, offset] = $('#step2 input');
              let setNow = () => {
                let now = new Date();
                offset.value = -now.getTimezoneOffset();
                time.value = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().substring(0, 16);
              };
              $('#step2 button').onClick(setNow)
              setNow();
            </script>
        </div>
    </div>
    <?php
}

function renderStep3() {
    ?>
    <div id="step3" class="step">
        <div class="title">
            <h2><span>3</span>/5 — <?= Label::get('platform.step3.title') ?></h2>
            <!--            <img src="/img/exporte_pikto_mitfahrenElement%209.svg">-->
        </div>
        <div class="actions">
            <input name="departureLocation">
            <button>
                <p>🡣</p>
                <?= Label::get('platform.step3.here') ?>
            </button>
        </div>
    </div>
    <?php
}

function renderStep4() {
    ?>
    <div id="step4" class="step">
        <h2><?= Label::get('platform.step4.title') ?></h2>
        <div class="actions">
            <div>
            </div>
        </div>
    </div>
    <?php
}

function renderStep5() {
    ?>
    <div id="step5" class="step">
        <h2><?= Label::get('platform.step5.title') ?></h2>
        <div class="actions">
            <button>
                <span><?= Label::get('platform.step5.send') ?></span>
            </button>
        </div>
    </div>
    <?php
}

function renderIframe() {
    ?>
    <iframe class="results w-full h-0" src="./iframe.php"></iframe>
    <div class="loading absolute h-full w-full flex justify-center items-center">
        <svg class="inline mr-2 w-10 h-10 text-background animate-spin fill-primary"
             viewBox="0 0 100 101"
             fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                  fill="currentColor"/>
            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                  fill="currentFill"/>
        </svg>
    </div>
    <script>
      $('.results').onLoad((e) => {
        let iframe = e.target;
        iframe.style.height = iframe.contentWindow.document.body.scrollHeight + 'px';
      });
      $().onLoad(() => $('.loading').classList.add('hidden'));
    </script>
    <?php
}

?>
