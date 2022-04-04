<?php
use core\language\Label;
use page\Page;

require_once "../core/Autoload.php";

Page::addScriptFile('/js/autocomplete.js', defer: true);
Page::addScriptFile('/js/createTrip.js', defer: true);

Page::render(function () {
    ?>
    <div class="col-span-1 space-y-10 contents md:block">
        <div class="order-1 space-y-10">
            <?php
            renderIntro();
            renderSteps();
            ?>
        </div>
        <div class="order-3 space-y-10">
            <?php
            renderPlatforms();
            renderPrintedCards();
            renderSafety();
            renderSupporter();
            ?>
        </div>
    </div>
    <div class="order-2 flex-1 col-span-2 flex flex-col mt-10">
        <div class="bg-secondary px-3 py-6"><?= Label::get('results.title') ?></div>
        <div class="flex-1 relative flex justify-center items-center">
            <?php renderIframe(); ?>
        </div>
    </div>
    <?php
});

function renderIntro() {
    ?>
    <div class="intro">
        <div style="background: url('/img/IMG_8802_x.webp'); background-size: cover">
            <div>
                <h2><?= Label::get('intro.platform.text') ?></h2>
                <a class="btn-white" href="#steps"><?= Label::get('intro.platform.button') ?></a>
            </div>
            <div>
                <h2><?= Label::get('intro.print.text') ?></h2>
                <a href="#printed"><?= Label::get('intro.print.button') ?></a>
            </div>
            <div>
                <h2><?= Label::get('intro.safety.text') ?></h2>
                <a href="#safety"><?= Label::get('intro.safety.button') ?></a>
            </div>
        </div>
        <p><?= Label::get('intro.subText') ?></p>
    </div>
    <?php
}

function renderSteps() {
    ?>
    <form id="steps" class="card">
        <div class="heading">
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
    </form>
    <?php
}

function renderStep1() {
    ?>
    <div id="step1" class="step">
        <div class="title">
            <h2><span>1</span>/5 — <?= Label::get('platform.step1.title') ?></h2>
            <img src="/img/exporte_pikto_mitfahren_offer_findElement%204.svg">
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
            <input name="departureTime" type="datetime-local" required>
            <button type="button">
                <img src="/img/exporte_pikto_mitfahrenElement%207.svg" class="rotate-90">
                <?= Label::get('platform.step2.now') ?>
            </button>
            <script>
              let [time] = $('#step2 input');
              let setNow = () => {
                let now = new Date();
                time.value = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().substring(0, 16);
              };
              $('#step2 button').onClick(setNow);
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
            <img src="/img/exporte_pikto_abfahrt_locationElement%203.svg">
        </div>
        <div class="actions">
            <input name="departureLocation" data-autocomplete required>
            <button type="button">
                <img src="/img/exporte_pikto_mitfahrenElement%207.svg" class="rotate-90">
                <?= Label::get('platform.step3.here') ?>
            </button>
        </div>
    </div>
    <?php
}

function renderStep4() {
    ?>
    <div id="step4" class="step">
        <div class="title">
            <h2><span>4</span>/5 — <?= Label::get('platform.step4.title') ?></h2>
            <img src="/img/exporte_pikto_zielElement%202.svg">
        </div>
        <div class="actions">
            <input name="destination" data-autocomplete required>
        </div>
    </div>
    <?php
}

function renderCreateForm() {
    ?>
    <div id="create-form" class="hidden">
        <div>
            <input name="seats" type="number" min="1" value="1">
            <span><?= Label::get('platform.step5.seats') ?></span>
        </div>
        <input name="phone" placeholder="<?= Label::get('platform.step5.phone') ?>">
        <input name="mail" type="email" placeholder="<?= Label::get('platform.step5.mail') ?>" required>
        <script type="module">
          let updateForm = () => {
            let $form = $("#create-form");
            let $formInputs = $("#create-form input");
            if ($("form")[0].elements['mode'].value === 'searching') {
              $form.addClass('hidden');
              $formInputs.disable();
            } else {
              $form.removeClass('hidden');
              $formInputs.enable();
            }
          };
          $("input[name='mode']").onClick(updateForm);
          updateForm();
        </script>
    </div>
    <?php
}

function renderStep5() {
    ?>
    <div id="step5" class="step">
        <div class="title">
            <h2><span>5</span>/5 — <?= Label::get('platform.step5.title') ?></h2>
        </div>
        <?php renderCreateForm(); ?>
        <div class="actions">
            <button>
                <img src="/img/exporte_pikto_mitfahrenElement%207.svg">
                <?= Label::get('platform.step5.send') ?>
            </button>
        </div>
        <?php renderSendingModal(); ?>
        <?php renderSuccessModal(); ?>
    </div>
    <?php
}

function renderSendingModal() {
    ?>
    <div id="sending-modal" class="modal-bg hidden">
        <div class="modal p-8">
            <div class="flex">
                <svg class="inline mx-auto w-10 h-10 text-background animate-spin fill-primary"
                     viewBox="0 0 100 101"
                     fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                          fill="currentColor"/>
                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                          fill="currentFill"/>
                </svg>
            </div>
            <p><?= Label::get('platform.step5.sending') ?></p>
        </div>
    </div>
    <?php
}

function renderSuccessModal() {
    ?>
    <div id="success-modal" class="modal-bg hidden">
        <div class="modal flex flex-col bg-secondary space-y-3">
            <div class="flex justify-center">
                <p class="w-10/12">
                    <?= Label::get('platform.step5.sent') ?>
                </p>
            </div>
            <div class="box">
                <p class="text-base"><?= Label::get('platform.step5.sentSubText') ?></p>
                <img src="/img/ride2go.webp" class="mx-auto">
            </div>
            <a class="btn-primary mx-auto" href="/">OK</a>
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
      $('.results')[0].onload = (e) => {
        let iframe = e.target;
        iframe.style.height = iframe.contentWindow.document.body.scrollHeight + 'px';
      };
      $()[0].onload = () => $('.loading')[0].classList.add('hidden');
    </script>
    <?php
}

function renderPlatforms() {
    ?>
    <div class="card py-10">
        <div class="box py-10 space-y-10 mx-3">
            <p><?= Label::get('platforms.offering') ?></p>
            <div class="flex">
                <img src="/img/logos.webp" class="mx-auto">
            </div>
            <a class="btn-white"><?= Label::get('platforms.contact') ?></a>
            <p><?= Label::get('platforms.subText') ?></p>
        </div>
    </div>
    <?php
}

function renderPrintedCards() {
    ?>
    <div id="printed" class="card">
        <div class="heading">
            <h1><?= Label::get('printed.title') ?></h1>
            <h2><?= Label::get('printed.subTitle') ?></h2>
        </div>
        <div class="box space-y-10 mx-3 mt-3">
            <p><?= Label::get('printed.hanger') ?></p>
            <div class="flex">
                <img src="/img/hanger.webp" class="mx-auto">
            </div>
            <div class="text-primary underline pb-8 ml-8">
                <a class="block" href="/pdf/Windshield_front+back_ridewithukraine.pdf"
                ><?= Label::get('printed.download') ?></a>
                <a class="block"><?= Label::get('printed.order') ?></a>
            </div>
        </div>
        <div class="box space-y-10 mx-3 mt-3">
            <p><?= Label::get('printed.postcard') ?></p>
            <div class="flex">
                <img src="/img/postcard.webp" class="mx-auto">
            </div>
            <div class="text-primary underline pb-8 ml-8">
                <a class="block" href="/pdf/Flyer_front+back_ridewithukraine.pdf"
                ><?= Label::get('printed.download') ?></a>
                <a class="block"><?= Label::get('printed.order') ?></a>
            </div>
        </div>
    </div>
    <?php
}

function renderSafety() {
    Page::addStyle('
    #safety li {
        padding-left: 1.25em;
        text-indent: -1.25em;
    }

    #safety li::before {
        content: "—";
        margin-right: 0.25rem;
    }
    ');
    ?>
    <div id="safety" class="card">
        <div class="heading">
            <h1><?= Label::get('safety.title') ?></h1>
            <h2><?= Label::get('safety.subTitle') ?></h2>
        </div>
        <div class="box space-y-6 m-3 py-5">
            <p class="text-primary"><?= Label::get('safety.passenger') ?></p>
            <ul>
                <li><?= Label::get('safety.passenger.1') ?></li>
                <li><?= Label::get('safety.passenger.2') ?></li>
                <li><?= Label::get('safety.passenger.3') ?></li>
                <li><?= Label::get('safety.passenger.4') ?></li>
            </ul>
            <p class="text-primary"><?= Label::get('safety.driver') ?></p>
            <ul>
                <li><?= Label::get('safety.driver.1') ?></li>
                <li><?= Label::get('safety.driver.2') ?></li>
                <li><?= Label::get('safety.driver.3') ?></li>
            </ul>
        </div>
    </div>
    <?php
}

function renderSupporter() {
    ?>
    <div id="supporter" class="card py-10">
        <div class="box space-y-6 m-3 py-5">
            <p><?= Label::get('supporter.title') ?></p>
            <div class="flex">
                <img src="/img/sportguide.webp" class="mx-auto">
            </div>
            <a class="btn-white" href="https://sportguide.rocks"><?= Label::get('platforms.contact') ?></a>
        </div>
    </div>
    <?php
}

?>
