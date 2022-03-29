<?php
use page\Page;

require_once "../core/Autoload.php";

Page::render(function () {
    ?>
    <div class="col-span-3 mx-3">
        <h1>Impressum</h1>
        <p>Verantwortlich für die Inhalte dieser Seite ist:<br>Bundesverband Mitfahren e.V. </p>
        <p>E-Mail: post@mitfahrverband.org <br>Postanschrift: Landsberger Allee 61, D-10249 Berlin</p>
        <p>Registergericht: Amtsgericht Charlottenburg<br>Registernummer: VR 38932 B</p>
        <p>Vertretungsberechtigte Vorstandsmitglieder: </p>
        <p>Natürliche Personen:</p>
        <ul>
            <li>Adrian Frey</li>
            <li>Dr. Frank Gerhardt</li>
            <li>Uwe Hömer</li>
            <li>Ludwig Haimmerer</li>
            <li>Martin Hovekamp</li>
            <li>Yan Minagawa</li>
            <li>Lina Mosshammer</li>
            <li>Clemens Rath</li>
            <li>Robin Weidner</li>
        </ul>
        <p>Juristische Personen mit ihren offiziellen Vertreter:</p>
        <ul>
            <li>goFLUX Mobility GmbH (Wolfram Uerlich)</li>
            <li>Match Rider UG (Dr. Benedikt Krams)</li>
            <li>Troodle Mobility Solution GmbH (Bernd Sailer)</li>
        </ul>
        <p>Presseverantwortliche nach § 55 Abs.2 RStV:<br>Wolfram Uerlich, Lina Mosshammer</p>
    </div>
    <?php
});
