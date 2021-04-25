<?php

$decoded = '';
$alphabets = range('a', 'z');
$counts = [];
$wordLengths = [];
$wordCounts = [];

if (count($_POST) !== 0) {
    $text = $_POST['text'];
    $textArray = str_split($text);
    $textInfoArray = array_map(
        function (string $char) {
            return [
                'char' => $char,
                'isDecoded' => false,
            ];
        },
        $textArray
    );

    foreach ($_POST['char'] as $before => $after) {
        if ($after === '') {
            continue;
        }
        foreach ($textInfoArray as $key => $textInfo) {
            if (strtolower($textInfo['char']) !== $before) {
                continue;
            }
            if ($textInfo['isDecoded'] === true) {
                continue;
            }

            $textInfo['char'] = $textInfo['char'] !== $before ? strtoupper($after) : $after;
            $textInfo['isDecoded'] = true;
            $textInfoArray[$key] = $textInfo;
        }
    }

    foreach ($textInfoArray as $textInfo) {
        $decoded .= $textInfo['isDecoded'] ? "<span class='decoded'>{$textInfo['char']}</span>" : $textInfo['char'];
    }

    $trimmedArray = [];
    foreach ($textArray as $key => $value) {
        if (!in_array($value, $alphabets, true)) {
            continue;
        }
        $value = strtolower($value);
        $trimmedArray[] = $value;
    }
    $length = count($trimmedArray);
    $counts = array_count_values($trimmedArray);
    arsort($counts);
    $counts = array_map(function (string $count, string $key) use ($length) {
        return [
                'char' => $key,
                'count' => $count,
                '%' => (int)$count / $length * 100,
        ];
    }, $counts, array_keys($counts));

    $words = str_word_count($text, 1);
    $wordLengths = [];
    foreach (array_unique($words) as $word) {
        $wordLengths[$word] = strlen($word);
    }
    asort($wordLengths);

    $wordCounts = array_count_values($words);
    arsort($wordCounts);
}

?>

<form method="POST">
    <div>
        <label>
            Text:
            <textarea name="text" id="" cols="100" rows="10"><?= $_POST['text'] ?? '' ?></textarea>
        </label>
    </div>
    <table>
        <?php $count = 0 ?>
        <tr>
            <?php foreach ($alphabets as $alphabet): ?>
                <td><?= $alphabet ?></td>
                <td><input type="text" name="char[<?= $alphabet ?>]" value="<?= $_POST['char'][$alphabet] ?? '' ?>" maxlength="1" size="1"></td>
                <?php if (++$count % 7 === 0): ?>
                    </tr>
                    <tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tr>
    </table>
    <input type="submit">
</form>

<div>
    <?= $text ?>
</div>

<div>
    <?= $decoded ?>
</div>

<div>
    <table>
        <?php foreach ($counts as $countInfo): ?>
            <tr>
                <td><?= $countInfo['char'] ?></td>
                <td><?= $countInfo['count'] ?> ( <?= round($countInfo['%'], 2) ?>% ) </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div>
    Length
    <table>
        <?php foreach ($wordLengths as $word => $wordLength): ?>
            <tr>
                <td><?= $word ?></td>
                <td><?= $wordLength ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div>
    Count
    <table>
        <?php foreach ($wordCounts as $word => $wordCount): ?>
            <tr>
                <td><?= $word ?></td>
                <td><?= $wordCount ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<style>
    div {
        margin-bottom: 4rem;
    }
    .decoded {
        color: red;
    }
</style>