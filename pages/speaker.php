<!DOCTYPE html>
<html lang="ja">

<head>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../styleSmart.css">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:url" content=" ページの URL" />
    <meta property="og:type" content=" website" />
    <meta property="og:title" content=" ❤︎ずんだもんちゃん達の読み上げアプリ❤︎" />
    <meta property="og:description" content=" 書いた文章をずんだもんちゃんたちが読み上げてくれます。あんなことやこんなことを読み上げてもらうのもいいですね、、、。" />
    <meta property="og:site_name" content="VOICEVOXの読み上げアプリ" />
    <meta property="og:image" content=" https://zunko.jp/sozai/zundamon_s/zzs_zunmon001.png" />
    <title>DDDSpeaker</title>
</head>

<body>

    <!-- ヘッダー -->
    <header>
        <nav>
            <ul class="nav-list">
                <li><a href="../index.php">Home</a></li>
                <li><a href="speaker.php">Speaker</a></li>
                <li><a href="../pages/otherApp.php">Other App</a></li>
            </ul>
        </nav>
        <div class="hero">
            <h1>DDD SPEAKER</h1>
            <h2>by VOICEVOX</h2>
        </div>
    </header>

    <!-- 話者選択ボタン -->
    <form action="" method="post">
        <h3 class="speakerSelect">話者を選択してね (⋈◍＞◡＜◍)。✧♡
            <select class="speakerSelectButton" name="speaker">
                <?php
                $url = 'http://127.0.0.1:50021/speakers';
                $options = array(
                    'http' => array(
                        'method' => 'GET',
                        'header' => 'Content-type: application/json; charset=UTF-8'
                    )
                );
                $context = stream_context_create($options);
                $raw_data = file_get_contents($url, false, $context);
                $data = json_decode($raw_data, true);
                for ($i = 0; $i < count($data); $i++) {
                    for ($j = 0; $j < count($data[$i]["styles"]); $j++) {
                        if (isset($_POST["speaker"]) && $data[$i]["styles"][$j]["id"] == $_POST["speaker"]) {
                            //POSTにあるspeakerと一致してたらそれを選択済みにしておく(selectedを追加して書いておく)
                            echo "<option value=" . $data[$i]["styles"][$j]["id"] . " selected >" . $data[$i]["name"] . " " . $data[$i]["styles"][$j]["name"] . "</option>";
                        } else {
                            // 何もPOSTされてない場合はずんだもんノーマルをselected
                            if (!isset($_POST["speaker"]) && $data[$i]["styles"][$j]["id"] == 3) {
                                echo "<option value=" . $data[$i]["styles"][$j]["id"] . " selected >" . $data[$i]["name"] . " " . $data[$i]["styles"][$j]["name"] . "</option>";
                            } else {
                                //違う場合はそのまま
                                echo "<option value=" . $data[$i]["styles"][$j]["id"] . ">" . $data[$i]["name"] . " " . $data[$i]["styles"][$j]["name"] . "</option>";
                            };
                        };
                    };
                };
                ?>
            </select>
        </h3>

        <!-- テキストエリア -->
        <input type="text" name="text" placeholder="こんちくわなのだ！ここに読み上げてほしい文章を入力するのだ！" class="hoge" required>
        <div class="speakerZunda">
            <img src="https://gyazo.com/661e7b192527050aa893bca34a6154a2/max_size/400" alt="ずんだもん1">
        </div>
        <br>
        <!-- 読み上げボタン -->
        <input type="submit" value="読み上げ" class="hoge2">
    </form>

    <br>

    <?php
    if (!isset($_POST["text"])) {
        echo '<p class="warning-text">リストから話者を選ぶのだ！</p>';
    } else {
        $query_url = 'http://127.0.0.1:50021/audio_query?text=' . urlencode('"' . $_POST["text"] . '"') . "&speaker=" . $_POST["speaker"];
        $query_options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/json; charset=UTF-8',
            )
        );
        $query_context = stream_context_create($query_options);
        $query_raw_data = file_get_contents($query_url, false, $query_context);
        $query_data = json_decode($query_raw_data, true);
        $synthesis_url = 'http://127.0.0.1:50021/synthesis?speaker=' . $_POST["speaker"] . '&enable_interrogative_upspeak=true';
        $synthesis_options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/json; audio/wav',
                'content' => json_encode($query_data),
                'responseType' => "stream"
            )
        );
        $synthesis_context = stream_context_create($synthesis_options);
        $synthesis_raw_data = file_get_contents($synthesis_url, false, $synthesis_context);
        echo '<audio class="custom-audio "controls="controls" autobuffer="autobuffer" autoplay="autoplay">';
        echo '<source src="data:audio/wav;base64,' .  base64_encode($synthesis_raw_data) . '"/>';
        echo '</audio>';
    };
    ?>



    <footer>
        <p>&copy; 2023 TanakaTeam. All rights reserved.</p>
    </footer>

</html>