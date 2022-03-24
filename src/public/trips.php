<?php
echo '<table id="result-container-card">';
$filedata = file_get_contents('json_data.json');
$details = json_decode($filedata, true);?>
<?php
foreach ($details as $element) {
    echo '<tr>';

    echo '<td id="departTime">';
    echo($element['departTime']. '<br>');
    echo '</td>';

    echo '<td id="dateTime">';
    echo "2022.03.24.";
    echo '</td>';


    echo '<td id="origin">';
    echo ($element['stops'][0]['address']. '<br>');
    echo '</td>';
    echo '<td id="destination">';
    echo end($element['stops'])['address']. '<br>';
    echo '</td>';


    $links = $element['deeplink'];

    $mifaz_word = "mifaz";

    if(strpos($links, $mifaz_word) !== false){
        echo '<td id="logo">';
        echo '<img src="https://ride2go.com/img/mifaz_logo.png" class="mifaz-logo">';
        echo '</td>';
    } else{
        echo '<td id="logo">';
        echo '<img src="https://ride2go.com/img/r2g_favicon.png" class="ride2go-logo">';
        echo '</td>';
    }

    echo '<td id="link">';
    echo "<a id='button1' href='$links' >Show</a>";
    echo '</td>';
}

echo '</tr>';
echo '</table>';
