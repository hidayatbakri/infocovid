<?php

function http_request($url)
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $out = curl_exec($ch);
  curl_close($ch);
  return $out;
}

$provinsi = http_request('https://rs-bed-covid-api.vercel.app/api/get-provinces');
$provinsi = json_decode($provinsi, TRUE);

$provinsi = $provinsi['provinces'];

if (isset($_GET)) {
  $tipe = $_GET['tipe'];
  $rs = $_GET['rs'];

  $info = http_request('https://rs-bed-covid-api.vercel.app/api/get-bed-detail?hospitalid=' . $rs . '&type=' . $tipe);
  $info = json_decode($info, TRUE);
  $statusBed = $info['data']['bedDetail'];
}




?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Info Rumah Sakit | Indonesia</title>
  <link rel="stylesheet" href="assets/css/tailwind.css">
  <link rel="stylesheet" href="src/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" />
</head>

<body>
  <nav class="bg-white w-full py-6 px-11 flex shadow sticky top-0 flex-col md:flex-row">
    <h1 class="font-bold text-xl text-yellow-500">Info Covid-19</h1>
    <div class="menu ml-auto">
      <ul class="flex md:flex-row">
        <li class="mx-9 md:py-0"><a href="index.php">Home</a></li>
      </ul>
    </div>
    <button id="toggle-nav" class="block md:hidden absolute right-9" style="width: 30px; height: 30px;">
      <i id="hambur" class="fas fa-align-right font-bold text-xl"></i>
    </button>
  </nav>

  <div class="container mx-auto flex items-center mt-10">

    <div class="card w-full bg-white rounded-md py-8">
      <h1 class="text-center text-3xl font-bold text-gray-700"><?= $info['data']['name']; ?></h1>
      <h5 class="text-sm text-center text-gray-400"><?= $info['data']['address']; ?></h5>

      <div class="px-5 pt-10" style="overflow-x: scroll;">
        <h4 class="text-xl font-semibold text-gray-700"><i class="fas fa-bed text-yellow-500 pr-1"></i> Informasi Tempat Tidur</h4>

        <table id="table-cov" class="w-full mt-10 table-fixed ">
          <thead>
            <tr>
              <th class="text-lg text-gray-700 w-1/4 text-center">Tipe</th>
              <th class="text-lg text-gray-700 w-1/3 text-center">Tempat Tidur Tersedia</th>
              <th class="text-lg text-gray-700 w-1/3 text-center">Tempat Tidur Kosong</th>
              <th class="text-lg text-gray-700 w-1/2 text-center">Antrian</th>
            </tr>
          </thead>
          <tbody>
            <?php
            for ($i = 0; $i < count($statusBed); $i++) :
            ?>
              <tr>
                <td class="text-md py-5 border-b-2 rounded-sm text-center"><?= $statusBed[$i]['stats']['title']; ?></td>
                <td class="text-md py-5 border-b-2 rounded-sm text-center"><?= $statusBed[$i]['stats']['bed_available']; ?></td>
                <td class="text-md py-5 border-b-2 rounded-sm text-center"><?= $statusBed[$i]['stats']['bed_empty']; ?></td>
                <td class="text-md py-5 border-b-2 rounded-sm text-center"><?= $statusBed[$i]['stats']['queue']; ?></td>
              </tr>
            <?php endfor; ?>
          </tbody>
        </table>

      </div>
      <a href="index.php" class="flex justify-center mr-5 items-center bg-yellow-500 text-white font-semibold w-24 text-lg rounded-md hover:bg-yellow-600 duration-75 mt-5 float-right" style="height: 50px;">Kembali</a>
    </div>
  </div>



  <script src="assets/js/jQuery.min.js"></script>
  <script src="src/js/Chart.js"></script>
  <script>
    const button = document.querySelector('#toggle-nav'),
      hambur = document.querySelector('#hambur'),
      ul = document.querySelector('nav ul');

    button.addEventListener('click', () => {
      ul.classList.toggle('active');
      hambur.classList.toggle('fa-align-right');
      hambur.classList.toggle('fa-times');
      hambur.classList.toggle('active');
    });
  </script>

</body>

</html>