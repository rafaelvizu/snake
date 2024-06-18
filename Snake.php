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

	<?php
		$nome_jogador = $_POST['nome'];
		$dificuldade = match ($_POST['dificuldade']) {
			'facil' => 200,
			'medio' => 100,
			'dificil' => 50,
		};
		$qnt_comidas = $_POST['qnt_comidas'];

		$width = "600";
		$height = "600";
	?>

	<title>Snake</title>
	<style>
		canvas {
			border: 1px solid #000;
		}
	</style>
</head>
<body class="bg-gray-800">
	<div class="flex justify-center items-center h-screen">
		<div class="bg-gray-900 p-4 rounded-lg">
			<div class="text-center text-white text-2xl font-bold">Snake</div>
			<div class="text-center text-white text-lg font-bold">Jogador: <?= htmlspecialchars($nome_jogador) ?></div>
			<div class="text-center text-white text-lg font-bold">Dificuldade: <?= htmlspecialchars($_POST['dificuldade']) ?></div>
			<div class="text-center text-white text-lg font-bold">Quantidade de comidas: <?= htmlspecialchars($qnt_comidas) ?></div>
			<div class="flex justify-center items-center">
				<canvas id="snake" width="<?= $width ?>" height="<?= $height ?>" class="bg-gray-800"></canvas>
			</div>
			<div class="text-center text-white text-lg font-bold">Pontos: <span class="pontos">0</span></div>
		</div>
	</div>
	<script>
		const canvas = document.getElementById('snake');
		const ctx = canvas.getContext('2d');

		const dificuldade = <?= $dificuldade ?>;
		const qnt_comidas = <?= $qnt_comidas ?>;
		const width = <?= $width ?>;
		const height = <?= $height ?>;
		let pontos = 0;

		const snake = {
			x: 0, 
			y: 0,
			width: 20,
			height: 20,
			dx: 20, // direção x
			dy: 0, // direção y
			vel: 20, // velocidade
			tail: [{x: 0, y: 0}],
			total: 1, // tamanho da cobra
		};

		const food = {
			x: 0,
			y: 0,
			width: 20,
			height: 20,
		};

		function drawSnake() {
			ctx.fillStyle = '#fff';
			for (let i = 0; i < snake.tail.length; i++) {
				ctx.fillRect(snake.tail[i].x, snake.tail[i].y, snake.width, snake.height);
			}
		}

		function drawFood() {
			ctx.fillStyle = '#f00';
			ctx.fillRect(food.x, food.y, food.width, food.height);
		}

		function randomFood() {
			food.x = Math.floor(Math.random() * (width / food.width)) * food.width;
			food.y = Math.floor(Math.random() * (height / food.height)) * food.height;
		}

		function eatFood() {
			if (snake.x === food.x && snake.y === food.y) {
				snake.total++;
				pontos++;
				randomFood();
			}
		}

		function gameOver() {
			if (pontos === qnt_comidas) {
				alert('Você venceu!');
				snake.total = 1;
				snake.tail = [{x: snake.x, y: snake.y}];
				pontos = 0;
				randomFood();
			}
		}

		function updatePontos() {
			document.querySelector('.pontos').innerHTML = pontos;
		}

		function update() {
			// movimentação da cobra
			for (let i = snake.total - 1; i > 0; i--) {
				// exclui o último elemento da cauda
				snake.tail[i] = { ...snake.tail[i - 1] };
			}

			// movimenta a cabeça da cobra
			snake.tail[0].x += snake.dx;
			snake.tail[0].y += snake.dy;


			if (snake.tail[0].x < 0) {
				snake.tail[0].x = width - snake.width;
			}

			if (snake.tail[0].x >= width) {
				snake.tail[0].x = 0;
			}

			if (snake.tail[0].y < 0) {
				snake.tail[0].y = height - snake.height;
			}

			if (snake.tail[0].y >= height) {
				snake.tail[0].y = 0;
			}

			// mov a cabeça da cobra
			snake.x = snake.tail[0].x;
			snake.y = snake.tail[0].y;

			eatFood();
			gameOver();
		}

		function draw() {
			ctx.clearRect(0, 0, width, height);
			drawSnake();
			drawFood();
		}

		function loop() {
			updatePontos();
			update();
			draw();
		}

		function start() {
			randomFood();
			setInterval(loop, dificuldade);
		}

		document.addEventListener('keydown', (e) => {
			switch (e.key) {
				case 'ArrowUp':
					if (snake.dy === 0) {
						snake.dx = 0;
						snake.dy = -snake.vel;
					}
					break;
				case 'ArrowDown':
					if (snake.dy === 0) {
						snake.dx = 0;
						snake.dy = snake.vel;
					}
					break;
				case 'ArrowLeft':
					if (snake.dx === 0) {
						snake.dx = -snake.vel;
						snake.dy = 0;
					}
					break;
				case 'ArrowRight':
					if (snake.dx === 0) {
						snake.dx = snake.vel;
						snake.dy = 0;
					}
					break;
			}
		});

		start();
	</script>
</body>
</html>
