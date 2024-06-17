<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="https://cdn.tailwindcss.com"></script>
	<script>
		tailwind.config = {
			theme: {
				extend: {
					colors: {
						clifford: '#da373d',
					},
				}
			}
		}
	</script>
	<style type="text/tailwindcss">
		@layer utilities {
	      .content-auto {
	        content-visibility: auto;
	      }
	    }
	</style>
	<script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp,container-queries"></script>

	<title>Snake</title>

	<style>
		#game {
			display: grid;
			grid-template-columns: repeat(20, 1fr);
			grid-template-rows: repeat(20, 1fr);
			width: 400px;
			height: 400px;
		}

		#snake0 {
			grid-column: 1;
			grid-row: 1;
		}

		#snake1 {
			grid-column: 2;
			grid-row: 1;
		}

		#snake2 {
			grid-column: 3;
			grid-row: 1;
		}
	</style>

	<?php

		$nome = $_POST['nome'];

		$dificuldade = match ($_POST['dificuldade']) {
			'facil' => [
				'key' => 'Fácil',
				'valor' => 300,
			],
			'medio' => [
				'key' => 'Médio',
				'valor' => 200,
			],
			'dificil' => [
				'key' => 'Difícil',
				'valor' => 100,
			],
			default => [
				'key' => 'Fácil',
				'valor' => 300,
			],
		};

		$qnt_comidas = $_POST['qnt_comidas'];
		
	?>
</head>
<body class="bg-gray-900">
	<div class="container mx-auto">
		<div class="bg-gray-800 p-4 rounded-lg mt-4">
			<h1 class="text-2xl text-white mb-4">
				Informações
			</h1>
			<p class="text-white">Nome: <?= $nome ?></p>
			<p class="text-white">Dificuldade: <?= $dificuldade['key'] ?></p>
			<p class="text-white">Quantidade de comidas: <?= $qnt_comidas ?></p>
		</div>
	</div>
	<div id="game" class="container mx-auto mt-4">
		<img src="snake.png" id="snake0" />
		<img src="snake.png" id="snake1" />
		<img src="snake.png" id="snake2" />

	</div>

	<script type="text/javascript">

	//to set the different game levels
	var level1Score = 50;
	var level2Score = 90;
	var level3Score = 120;
	var levelSpeed = 200;

	//Game Flags
	var moveOne = true;
	var snakeDead = false;

	//for setting Game Score
	var Score = 0;
	var snakePart = 2;

	//retrieve form parameters
	var BoardSizex = '600px';
	var BoardSizey = '600px';
	var SnakePace = "<?php echo $dificuldade['valor'] ?>";
	var Goals = "<?php echo $qnt_comidas ?>";
	var BoardSize1x = '600';
	var BoardSize1y = '600';

	document.getElementById('game').style.height = BoardSizey;
	document.getElementById('game').style.width = BoardSizex;

	//flag set to not draw the food again and again on timer
	firstDraw = false;
	function drawFood() {

		if (!firstDraw) {
			for (k = 1; k <= Goals; k++) {
				callFood(k);
			}
			firstDraw = true;
		}

	}

	function callFood(k) {
		foodX = randomNumbergen(1, BoardSize1x - 10);
		foodX = foodX - (foodX % 10);

		foodY = randomNumbergen(1, BoardSize1y - 10);
		foodY = foodY - (foodY % 10);
		//generate img tags on fly
		var incr = "<img src = \"food.png\" id=\"food" + k + "\" style = \"position:absolute;left:" + foodX + ";top:" + foodY + ";height:10px;\" />";
		document.getElementById('game').innerHTML += incr;

		document.getElementById('food' + k).style.left = foodX;
		document.getElementById('food' + k).style.top = foodY;
	}

	function redrawFood(k) {
		callFood(k);

		var changer = document.getElementById('scoreText');
		changer.value = Score;
		//change Game levels
		if (Score == level3Score) {
			var answer = confirm("Voilaaaaa!You won the game!!!")
			alert("Bye bye!")
			window.location = "index.html";
		}
		else if (Score == level2Score) {
			var answer = confirm("Yippiee!You cleared 2nd level...do you want to continue?")
			if (answer) {
				alert("Continue to next Level with faster pace...")
				clearInterval(timer);
				timer = setInterval('mrSnake.move()', 50);
			}
			else {
				alert("Bye bye!")
				window.location = "index.html";
			}
		}
		else if (Score == level1Score) {
			var answer = confirm("Yippiee!You cleared 1st level...do you want to continue?")
			if (answer) {
				alert("Continue to next Level with faster pace...")
				clearInterval(timer);
				timer = setInterval('mrSnake.move()', 100);
			}
			else {
				alert("Bye bye!")
				window.location = "index.html";

			}
		}

		return;
	}

	//generates a random number between range x,y
	function randomNumbergen(x, y) {
		return Math.floor(Math.random() * y) + x;
	}


	function Snake() {
		this.x = 30;
		this.y = 30;
		this.dir = 'R';
	}

	Snake.prototype.changeDir = function (dir) {
		this.dir = dir;
	}


	Snake.prototype.draw = function () {
		if (snakeDead) {
			return false;
		}
		else if (this.x < 0 || this.x + 10 > BoardSize1x) {
			return false;
		}
		else if (this.y < 0 || this.y + 10 > BoardSize1y) {
			return false;
		}
		//let snake tail take position of head
		for (i = snakePart; i > 0; --i) {
			document.getElementById("snake" + i).style.left = document.getElementById("snake" + (i - 1)).style.left;
			document.getElementById("snake" + i).style.top = document.getElementById("snake" + (i - 1)).style.top;
			document.getElementById("snake" + i).style.visibility = 'visible';
		}
		document.getElementById('snake0').style.left = this.x;
		document.getElementById('snake0').style.top = this.y;


		if (!moveOne) {
			//check if snake touches itself-dead
			for (i = snakePart; !snakeDead && i > 0; --i) {
				if (
					((((this.x) + "px") == document.getElementById("snake" + i).style.left) &&
						((this.y + "px") == document.getElementById("snake" + i).style.top))

				)
					snakeDead = true;
			}

			//for eating all the foods present on the screen
			for (i = 1; i <= Goals; i++) {
				if ((this.x + "px") == document.getElementById("food" + i).style.left &&
					(this.y + "px") == document.getElementById("food" + i).style.top) {
					//dynamically generate snake size
					++snakePart;
					var imageSnake = "<img src = \"snake.png\" id=\"snake" + (snakePart) + "\" style = \"position:absolute;left:0;top:0;height:10px;Visibility:hidden\" />";
					document.getElementById('Board').innerHTML += imageSnake;

					//to disappear food that is eaten up
					var image_x = document.getElementById('Board');
					image_x.removeChild(document.getElementById('food' + i));
					Score = Score + 10;
					//to generate another goal
					redrawFood(i);
				}
			}
		}
		moveOne = false;
		return true;
	}

	Snake.prototype.move = function () {
		switch (this.dir) {
			case 'L': // Left
				this.x -= 10;
				break;
			case 'U': // Up
				this.y -= 10;
				break;
			case 'R': // Right
				this.x += 10;
				break;
			case 'D': // Down
				this.y += 10;
				break;
		}

		retval = this.draw();
		if (!retval) {
			var answer = confirm("Oops Your snake is dead! Click Ok to Quit.")
			if (answer) {
				clearInterval(timer);
				window.location = "index.html";
			}
		}
		drawFood();

	}

	var mrSnake = new Snake();

	window.addEventListener('keydown', function (event) {
		switch (event.keyCode) {
			case 37: // Left
				if (mrSnake.dir != 'R') {
					mrSnake.changeDir('L');
				}
				break;
			case 38: // Up
				if (mrSnake.dir != 'D') {
					mrSnake.changeDir('U');
				}
				break;
			case 39: // Right
				if (mrSnake.dir != 'L') {
					mrSnake.changeDir('R');
				}
				break;
			case 40: // Down
				if (mrSnake.dir != 'U') {
					mrSnake.changeDir('D');
				}
				break;
		}
	}, false);

	timer = setInterval('mrSnake.move()', SnakePace);

</script>


					
</body>
</html>