<?php
$TSDEMUX = "/usr/local/bin/ts-to-fmp4";
$FFPROBE = "/usr/local/bin/ffprobe";
$HLSTODASH = "/usr/local/bin/hls-to-dash";
$HLSTODASHMP = "/usr/local/bin/hls-to-dash --multi";

$dest_path = "/data/live" . $_SERVER['PATH_INFO'];

if ($_SERVER['REQUEST_METHOD'] === "POST" || $_SERVER['REQUEST_METHOD'] === "PUT") {
    $dir = dirname($dest_path);
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }

    if (($stream = fopen('php://input', "r")) !== FALSE) {
        $dest_fp = fopen($dest_path, "w");
        while ($buf = fread($stream, 1024)) {
            fwrite($dest_fp, $buf);
        }
        fclose($dest_fp);
    }

    if (preg_match('/master(\d+)_(\d+).ts/', $_SERVER['PATH_INFO'], $matches)) {
        $profile = $matches[1];
        $seqno = $matches[2];
        $dashdir = $dir . ".mpd";
        if (!file_exists($dashdir)) {
            mkdir($dashdir, 0777, true);
        }

        $ffprobecmd = $FFPROBE . " -of compact -show_entries stream=start_time -select_streams a ". $dest_path ." 2>/dev/null | grep -v program | sed -e 's/stream|start_time=//'";
        $timeoffset = (float)shell_exec($ffprobecmd);

        $tsdemuxcmd = $TSDEMUX . " --outdir " . $dashdir ." " . $dest_path . " " . $profile . "_" . ltrim($seqno, '0') . ".dash";
        exec($tsdemuxcmd);
    }

    // TBD: Configure which variant playlist to trigger on
    if (preg_match('/master1500\.m3u8/', $dest_path, $matches)) {
        $dashdir = $dir . ".mpd";
        if (!file_exists($dashdir)) {
            mkdir($dashdir, 0777, true);
        }
        $source = dirname($dest_path) . "/master.m3u8";
        $hlstodashcmd = $HLSTODASH . " " . $source . " > " . $dashdir . "/manifest.mpd";
        exec($hlstodashcmd);

        $hlstodashcmd = $HLSTODASHMP . " " . $source . " > " . $dashdir . "/multi.mpd";
        exec($hlstodashcmd);
    }
} else if ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    unlink($dest_path);

    if (preg_match('/master(\d+)_(\d+).ts/', $_SERVER['PATH_INFO'], $matches)) {
        $profile = $matches[1];
        $seqno = $matches[2];
        $dir = dirname($dest_path);

        $dashdir = $dir . ".mpd";
        unlink($dashdir . "/audio-" . $profile . "_" . ltrim($seqno, '0') . ".dash");
        unlink($dashdir . "/video-" . $profile . "_" . ltrim($seqno, '0') . ".dash");
    }
}

?>
