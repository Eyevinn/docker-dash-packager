var express = require('express');
var router = express.Router();
var glob = require('glob');

var datapath = '/data/live';
if (process.env.DATAPATH) {
    datapath = process.env.DATAPATH;
}

function getAllStreams(path) {
    return new Promise(function(resolve, reject) {
        var options;
        glob(path + '/*.mpd', options, function(er, files) {
            var streams = [];
            for (var i=0; i<files.length; i++) {
                var f = files[i];
                var stream = {};
                stream.path = f;
                var pattern = /.*\/(.*)\.mpd$/;
                var match = pattern.exec(f);
                if (match) {
                    stream.name = match[1];
                    stream.hls = "/live/" + match[1] + "/master.m3u8";
                    stream.dash = "/live/" + match[1] + ".mpd/manifest.mpd";
                    stream.multidash = "/live/" + match[1] + ".mpd/multi.mpd";
                }
                streams.push(stream);
            }
            resolve(streams);
        });
    });
}

router.get('/', function(req, res) {
    getAllStreams(datapath).then(function(streams) {
        console.log(streams);
        res.render('index', { title: 'Open Source DASH Packager', streams: streams });
    });
});

module.exports = router;
