<?php
echo '
<table id="result-container-card">';
$filedata = file_get_contents('json_data.json');
$details = json_decode($filedata, true);
foreach ($details as $element) {
    echo '
    <tr>';

    echo '
        <td>';
    echo($element['departTime'] . '<br>');
    echo '
        </td>
        ';

    echo '
        <td>';
    echo "DateTime";
    echo '
        </td>
        ';

    echo '
        <td>';
    echo($element['stops'][0]['address'] . '<br>');
    echo '
        </td>
        ';
    echo '
        <td>';
    echo end($element['stops'])['address'] . '<br>';
    echo '
        </td>
        ';

    echo '
        <td>';
    echo($element['deeplink'] . '<br>');
    echo '
        </td>
        ';

    echo '
        <td>';
    echo '<input class="submit" type="submit" value="Show">';
    echo '
        </td>
        ';
}

echo '
    </tr>
    ';
echo '
</table>';
