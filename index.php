<!DOCTYPE html>
<html>
<head>
	<title>Hang Man</title>
</head>
<body>
<div id="page">
	<div id="gallows">
		<div class="stage"></div>
		<div class="column"></div>
		<div class="support"></div>
		<div class="beam"></div>
		<div class="noose"></div>

		<div id="hangman" class="">
			<div class="head"></div>
			<div class="neck"></div>
			<div class="body"></div>
			<div class="arm-left"></div>
			<div class="arm-right"></div>
			<div class="leg-left"></div>
			<div class="leg-right"></div>
		</div>
	</div>

	<div id="wordbar"></div>
	<div id="letters">
	</div>
</div>
<style type="text/css">
	#page {
		min-height: 100%;
		height: auto !important;
		margin: 0 auto;
	}

	#gallows * {
		position: absolute;
		box-sizing: content-box;
		-moz-box-sizing: content-box;
	}

	#gallows {
		position: relative;
		width: 400px;
		height: 400px;
		margin: 0 auto;
	}

	#gallows .stage {
		top: 300px;
		left: 100px;
		background: #000;
		width: 150px;
		height: 5px;
	}

	#gallows .column {
		top: 154px;
		left: 150px;
		background: #000;
		width: 6px;
		height: 150px;
	}

	#gallows .support {
		top: 162px;
		left: 152px;
		background: #000;
		width: 25px;
		height: 2px;
		-webkit-transform: rotate(-90deg) skew(-45deg, 45deg);
		-ms-transform: rotate(-90deg) skew(-45deg, 45deg);
		transform: rotate(-90deg) skew(-45deg, 45deg);
		-webkit-border-radius: 2px 0 0 0;
		-moz-border-radius: 2px 0 0 0;
		border-radius: 2px 0 0 0;
	}

	#gallows .beam {
		top: 150px;
		left: 150px;
		background: #000;
		width: 75px;
		height: 4px;
	}

	#gallows .noose {
		top: 154px;
		left: 210px;
		background: #000;
		width: 3px;
		height: 25px;
	}

	#gallows #hangman.step1 .head,
	#gallows #hangman.step2 .head,
	#gallows #hangman.step3 .head,
	#gallows #hangman.step4 .head,
	#gallows #hangman.step5 .head,
	#gallows #hangman.step6 .head,
	#gallows #hangman.step7 .head {
		top: 175px;
		left: 198px;
		width: 25px;
		height: 25px;
		-moz-border-radius: 50%;
		-webkit-border-radius: 50%;
		border-radius: 50%;
		background: #fff;
		border: 1px solid #000;
	}

	#gallows #hangman.step2 .neck,
	#gallows #hangman.step3 .neck,
	#gallows #hangman.step4 .neck,
	#gallows #hangman.step5 .neck,
	#gallows #hangman.step6 .neck,
	#gallows #hangman.step7 .neck {
		top: 201px;
		left: 210px;
		width: 2px;
		height: 10px;
		background: #000;
	}

	#gallows #hangman.step3 .body,
	#gallows #hangman.step4 .body,
	#gallows #hangman.step5 .body,
	#gallows #hangman.step6 .body,
	#gallows #hangman.step7 .body {
		top: 211px;
		left: 210px;
		width: 2px;
		height: 30px;
		background: #000;
	}

	#gallows #hangman.step4 .arm-left,
	#gallows #hangman.step5 .arm-left,
	#gallows #hangman.step6 .arm-left,
	#gallows #hangman.step7 .arm-left {
		top: 204px;
		left: 205px;
		width: 2px;
		height: 20px;
		background: #000;
		-webkit-transform: rotate(-200deg) skew(-45deg, 45deg);
		-ms-transform: rotate(-200deg) skew(-45deg, 45deg);
		transform: rotate(-200deg) skew(-45deg, 45deg);
		-webkit-border-radius: 2px 0 0 0;
		-moz-border-radius: 2px 0 0 0;
		border-radius: 2px 0 0 0;
	}

	#gallows #hangman.step5 .arm-right,
	#gallows #hangman.step6 .arm-right,
	#gallows #hangman.step7 .arm-right {
		top: 204px;
		left: 218px;
		width: 1px;
		height: 20px;
		background: #000;
		-webkit-transform: rotate(100deg) skew(-45deg, 45deg);
		-ms-transform: rotate(100deg) skew(-45deg, 45deg);
		transform: rotate(100deg) skew(-45deg, 45deg);
		-webkit-border-radius: 2px 0 0 0;
		-moz-border-radius: 2px 0 0 0;
		border-radius: 2px 0 0 0;
	}

	#gallows #hangman.step6 .leg-left,
	#gallows #hangman.step7 .leg-left {
		top: 243px;
		left: 204px;
		width: 2px;
		height: 20px;
		background: #000;
		-webkit-transform: rotate(-200deg) skew(-45deg, 45deg);
		-ms-transform: rotate(-200deg) skew(-45deg, 45deg);
		transform: rotate(-200deg) skew(-45deg, 45deg);
		-webkit-border-radius: 2px 0 0 0;
		-moz-border-radius: 2px 0 0 0;
		border-radius: 2px 0 0 0;
	}

	#gallows #hangman.step7 .leg-right {
		top: 243px;
		left: 219px;
		width: 1px;
		height: 20px;
		background: #000;
		-webkit-transform: rotate(100deg) skew(-45deg, 45deg);
		-ms-transform: rotate(100deg) skew(-45deg, 45deg);
		transform: rotate(100deg) skew(-45deg, 45deg);
		-webkit-border-radius: 2px 0 0 0;
		-moz-border-radius: 2px 0 0 0;
		border-radius: 2px 0 0 0;
	}

	#wordbar {
		margin: 0 auto;
		width: 300px;
		text-align: center;
	}
</style>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script type="text/javascript">
	$(function () {
		$(document).keypress(function (e) {
			if ((e.which >= 65 && e.which <= 90) || (e.which >= 97 && e.which <= 122)) {
				$.ajax({
					url: "/ajax/hangman.php",
					type: "POST",
					data: { guess: String.fromCharCode(e.which) }
				}).done(function (data) {
						var obj = $.parseJSON(data);

						if (obj.wrong == 0) {
							$('#hangman').removeClass();
						}

						// Update hangman
						$('#hangman').addClass('step'+obj.wrong);

						// Update Wordbar
						var wordbar = '';

						$.each(obj.progress, function (key, val) {
							wordbar += val;
						});

						$('#wordbar').html(wordbar);

					});
			}
		});

		$.ajax({
			url: "/ajax/hangman.php",
			type: "POST",
			data: {}
		}).done(function (data) {
				var obj = $.parseJSON(data);

				// Update hangman
				$('#hangman').addClass('step'+obj.wrong);

				// Update Wordbar
				var wordbar = '';

				$.each(obj.progress, function (key, val) {
					wordbar += val;
				});

				$('#wordbar').html(wordbar);
			});
	});
</script>
</body>
</html>