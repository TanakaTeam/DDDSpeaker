<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:type" content="website" />
    <meta property="og:title" content="❤︎ずんだもんちゃん達の読み上げアプリ❤︎" />
    <meta property="og:description" content="自分で書いた文章を読み上げて欲しいときにはここ！かわいいかわいいずんだもんちゃんたちがあなたの文章を読み上げてくれます。あんなことやこんなことを読み上げてもらうのもいいですね、、、。" />
    <meta property="og:site_name" content="❤︎ずんだもんちゃん達の読み上げアプリ❤︎" />
    <meta property="og:image" content=" https://zunko.jp/sozai/zundamon_s/zzs_zunmon001.png" />
    <title>❤︎ずんだもんちゃん達の読み上げアプリ❤︎</title>
</head>

<body>
    <form action="" method="post">
        <p>話者を選択してね(⋈◍＞◡＜◍)。✧♡<br />
            <select name="speaker">
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
                        if ($data[$i]["styles"][$j]["id"] == $_POST["speaker"]) {
                            //POSTにあるspeakerと一致してたらそれを選択済みにしておく(selectedを追加して書いておく)
                            echo "<option value=" . $data[$i]["styles"][$j]["id"] . " selected >" . $data[$i]["name"] . " " . $data[$i]["styles"][$j]["name"] . "</option>";
                        } else {
                            //違う場合はそのまま
                            echo "<option value=" . $data[$i]["styles"][$j]["id"] . ">" . $data[$i]["name"] . " " . $data[$i]["styles"][$j]["name"] . "</option>";
                        };
                    };
                };
                ?>
            </select>
        </p>
        <textarea name="text">
読み上げ文字
        </textarea>
        <input type="submit" value="読み上げ">
    </form>

    <?php
    if (!isset($_POST["text"])) {
        echo "未選択";
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
        echo '<audio controls="controls" autobuffer="autobuffer" autoplay="autoplay">';
        echo '<source src="data:audio/wav;base64,' .  base64_encode($synthesis_raw_data) . '"/>';
        echo '</audio>';
    };
    ?>
</body>

</html>