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

if (isset($_POST['prov'])) {
  $data = htmlspecialchars($_POST['prov']);
  $kota = http_request('https://rs-bed-covid-api.vercel.app/api/get-cities?provinceid=' . $data);
  $kota = json_encode($kota);

  echo $kota;
}

$provinsi = http_request('https://rs-bed-covid-api.vercel.app/api/get-provinces');
$provinsi = json_decode($provinsi, TRUE);

$provinsi = $provinsi['provinces'];


$dataCovid = http_request('https://data.covid19.go.id/public/api/update.json');
$dataCovid = json_decode($dataCovid, TRUE);

$countDataCovid = $dataCovid['update']['total'];

$covidDaerah = http_request('https://data.covid19.go.id/public/api/prov.json');
$covidDaerah = json_decode($covidDaerah, TRUE);

$covidDaerah = $covidDaerah['list_data'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Info Covid-19 | Indonesia</title>
  <link rel="stylesheet" href="assets/css/tailwind.css">
  <link rel="stylesheet" href="assets/js/DataTables/datatable.min.css">
  <link rel="stylesheet" href="src/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" />
</head>

<body>
  <nav class="bg-white w-full py-6 px-11 flex shadow sticky top-0 flex-col md:flex-row">
    <h1 class="font-bold text-xl text-yellow-500">Info Covid-19</h1>
    <div class="menu ml-auto">
      <ul class="flex md:flex-row">
        <li class="mx-9 md:py-0"><a class="page-scroll" href="#covid">Data Covid-19</a></li>
        <li class="mx-9 md:py-0"><a class="page-scroll" href="#bed">Cek Ketersediaan Tempat Tidur</a></li>
      </ul>
    </div>
    <button id="toggle-nav" class="block md:hidden absolute right-9" style="width: 30px; height: 30px;">
      <i id="hambur" class="fas fa-align-right font-bold text-xl"></i>
    </button>
  </nav>

  <!-- intro -->
  <div class="container mx-auto px-4 flex items-center" id="intro">
    <div class="left">
      <h1 class="font-bold text-3xl md:text-5xl text-gray-800">Selamat datang,
        Info <span class="text-yellow-500">Covid-19</span> dan Info Tentang
        <span class="text-yellow-500">Tempat Tidur Yang Tersedia</span>
        Di Rumah Sakit Indonesia.</h1>
    </div>
    <div class="right"></div>
  </div>



  <div class="go-top flex justify-center items-center" style="position: fixed; right: 30px;"><i class="fas fa-arrow-up"></i></div>

  <!-- data covid -->
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
    <path fill="#ffffff" fill-opacity="1" d="M0,256L48,229.3C96,203,192,149,288,149.3C384,149,480,203,576,197.3C672,192,768,128,864,117.3C960,107,1056,149,1152,144C1248,139,1344,85,1392,58.7L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
  </svg>

  <section class="data-covid bg-white" id="covid">


    <div class="container mx-auto px-4 flex flex-col md:flex-row justify-center" id="data-cov">
      <div class="card rounded-lg px-7 h-52 w-full my-3 md:my-1 flex flex-col justify-center items-center text-white md:mx-1 bg-green-600">
        <div class="icon-cov bg-green-600">
          <i class="fas fa-heartbeat"></i>
        </div>
        <h4 class="font-bold text-4xl"><?= number_format($countDataCovid['jumlah_sembuh']); ?> <span class="text-base">Orang</span></h4>
        <p class="text-3xl pt-5">Sembuh</p>
        <p>Jumlah Data Pasien Sembuh</p>
      </div>
      <div class="card rounded-lg px-7 h-52 w-full my-3 md:my-1 flex flex-col justify-center items-center text-white md:mx-1 bg-yellow-500">
        <div class="icon-cov bg-yellow-500">
          <i class="fas fa-procedures"></i>
        </div>
        <h4 class="font-bold text-4xl"><?= number_format($countDataCovid['jumlah_positif']); ?> <span class="text-base">Orang</span></h4>
        <p class="text-3xl pt-5">Positif</p>
        <p>Jumlah Data Pasien Positif</p>
      </div>
      <div class="card rounded-lg px-7 h-52 w-full my-3 md:my-1 flex flex-col justify-center items-center text-white md:mx-1 bg-red-500">
        <div class="icon-cov bg-red-500">
          <i class="fas fa-skull-crossbones"></i>
        </div>
        <h4 class="font-bold text-4xl"><?= number_format($countDataCovid['jumlah_meninggal']); ?> <span class="text-base">Orang</span></h4>
        <p class="text-3xl pt-5">Meninggal</p>
        <p>Jumlah Data Pasien Meninggal</p>
      </div>
    </div>

    <center>
      <div class="w-full md:w-1/2" style="height: 300px; margin-top: 50px; margin-bottom: 60px">
        <canvas id="myChart"></canvas>
        <span class="mb-10 text-gray-400 float-left">Terakhir di Update <?= $dataCovid['update']['penambahan']['tanggal']; ?></span>
      </div>
    </center>


    <div id="table-cov" class="container mx-auto mt-10 px-10" style="overflow-x: scroll;">
      <table class="mt-10 w-full data-table">
        <thead>
          <tr>
            <th class="text-lg text-gray-700 text-center">Nama Provinsi</th>
            <th class="text-lg text-gray-700 text-center">Total Positif</th>
            <th class="text-lg text-gray-700 text-center">Total Sembuh</th>
            <th class="text-lg text-gray-700 text-center">Total Meninggal</th>
          </tr>
        </thead>
        <tbody id="myTable">
          <?php $i = 0;
          foreach ($covidDaerah as $daerah) : ?>
            <tr>
              <td class="text-md py-5 border-b-2 rounded-sm text-left"><?= $daerah['key']; ?></td>
              <td class="text-md py-5 border-b-2 rounded-sm text-center"><?= number_format($daerah['jumlah_kasus']); ?> / Orang</td>
              <td class="text-md py-5 border-b-2 rounded-sm text-center"><?= number_format($daerah['jumlah_sembuh']); ?> / Orang</td>
              <td class="text-md py-5 border-b-2 rounded-sm text-center"><?= number_format($daerah['jumlah_meninggal']); ?> / Orang</td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </section>

  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
    <path fill="#ffffff" fill-opacity="1" d="M0,128L48,106.7C96,85,192,43,288,48C384,53,480,107,576,138.7C672,171,768,181,864,170.7C960,160,1056,128,1152,149.3C1248,171,1344,245,1392,282.7L1440,320L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>
  </svg>

  <div id="bed"></div>
  <section id="tempat-tidur" class="mb-5">
    <h3 class="text-center text-3xl font-bold text-gray-700"><i class="fas fa-check-square text-yellow-500 pr-3"></i> Cek Ketersediaan Tempat Tidur</h3>
    <div class="temp-tidur container mx-auto flex justify-center mt-10">
      <div class="left-check w-full px-5">
        <form action="#" method="post" id="form-beds">

          <select id="prov" name="prov" class="w-full mt-3 font-medium border-2 rounded-md px-3 bg-white" style="height: 60px; color: rgb(73, 73, 73);" name="provinsi" id="">
            <option selected value="#">~ Pilih Provinsi ~</option>
            <?php
            foreach ($provinsi as $provin) :
            ?>
              <option value="<?= $provin['id']; ?>"><?= $provin['name']; ?></option>
            <?php endforeach; ?>
          </select>
          <select name="kota" id="kota" class="w-full mt-3 font-medium border-2 rounded-md px-3 bg-white" style="height: 60px; color: rgb(73, 73, 73);" name="provinsi" id="">
            <option selected value="#">~ Pilih Kota ~</option>
          </select>

          <label for="tipe1">Covid</label>
          <input type="radio" id="tipe1" value="1" name="tipe" checked class="mr-2">
          <label for="tipe2">Non Covid</label>
          <input type="radio" id="tipe2" value="2" name="tipe">

          <button type="submit" id="cariBed" name="cariBed" class="bg-yellow-500 text-white font-semibold w-24 text-xl rounded-md hover:bg-yellow-600 duration-75 mt-5 float-right" style="height: 50px;"><i class="fas fa-search pr-2"></i> Cari</button>
        </form>
      </div>
      <div class="right-check w-full bg-white my-10 md:my-0 rounded-md">
        <div class="container px-10 py-10">
          <h4 class="text-xl font-semibold"><i class="fas fa-search-location text-yellow-500 pr-2"></i> Hasil Pencarian</h4>
          <div class="loader"></div>
          <table class="mt-5 w-full">
            <thead>
              <tr>
                <th class=""></th>
              </tr>
            </thead>
            <tbody>
              <div class="table-rs"></div>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </section>


  <footer class=" bg-gray-900 text-gray-300 flex justify-between md:items-center px-10">
    <h3 class="font-bold text-xl">@2021 Created By.Hidayat</h3>
    <div class="social-media">
      <ul>
        <li class="font-semibold pb-2">Sosial Media</li>
        <li><i class="fab fa-instagram"></i> @hidayat_yy</li>
        <li><i class="fas fa-envelope-open-text"></i> manusiabiasa819@gmail.com</li>
      </ul>
    </div>
    <div class="github">
      <ul>
        <li class="font-semibold pb-2">Akun Github</li>
        <li><i class="fab fa-github"></i> <a href="https://github.com/hdyt007">https://github.com/hdyt007</a></li>
      </ul>
    </div>
  </footer>

  <a href="#intro" class="page-scroll" id="circl">
    <div class="circl d-flex justify-content-center align-items-center">
      <i class="fas fa-arrow-up"></i>
    </div>
  </a>

  <script src="assets/js/jQuery.min.js"></script>
  <script src="assets/js/DataTables/datatables.min.js"></script>
  <script src="src/js/Chart.js"></script>
  <script>
    const button = document.querySelector('#toggle-nav'),
      hambur = document.querySelector('#hambur'),
      cariBed = document.querySelector('#cariBed'),
      form = document.querySelector('#form-beds'),
      prov = document.querySelector('#prov'),
      kota = document.querySelector('#kota'),
      ul = document.querySelector('nav ul');
    button.addEventListener('click', () => {
      ul.classList.toggle('active');
      hambur.classList.toggle('fa-align-right');
      hambur.classList.toggle('fa-times');
      hambur.classList.toggle('active');
    });

    form.onsubmit = (e) => {
      e.preventDefault();
    }

    $('#prov').on('change', () => {
      const val = prov.value;

      if (val !== '#') {
        let xhr = new XMLHttpRequest();
        xhr.open('GET', 'https://rs-bed-covid-api.vercel.app/api/get-cities?provinceid=' + val, true);
        xhr.send();
        xhr.onload = () => {
          if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            let data = xhr.response;
            getBed = JSON.parse(data);
            for (let i = 0; i < getBed.cities.length; i++) {
              const element = "<option selected value='" + getBed.cities[i].id + "'>" + getBed.cities[i].name + "</option>";
              $('#kota').append(element);
            }
          }
        }
      }

    });

    $('.loader').hide(100);
    cariBed.addEventListener('click', () => {
      const valprov = prov.value;
      const valkota = kota.value;
      const valtipe = $('input[type=radio][name=tipe]:checked').val();

      $('.loader').show(100);

      if (valprov !== '#' && valkota !== '#') {
        if (valtipe === '1') {
          $('.table-rs').html('');
          let xhr = new XMLHttpRequest();
          xhr.open('GET', 'https://rs-bed-covid-api.vercel.app/api/get-hospitals?provinceid=' + valprov + '&cityid=' + valkota + '&type=1', true);
          xhr.send();
          xhr.onload = () => {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
              let data = xhr.response;
              getRs = JSON.parse(data);
              for (let i = 0; i < getBed.cities.length; i++) {
                const element = `<tr class="border-b-2">
                <td class="py-10">
                  <p class="font-bold w-1/2 text-left text-xl ">` + getRs.hospitals[i].name + `</p>
                  <div class="bg-yellow-500 flex justify-center items-center text-white rounded-md float-right" style="height: 40px; width: 50px; margin-top: -50px;">` + getRs.hospitals[i].bed_availability + `</div>
                  <p>` + getRs.hospitals[i].address + `</p>
                  <p>Tersedia ` + getRs.hospitals[i].bed_availability + ` Tempat Tidur Kosong </p>
                  <p class="text-left md:text-right">Konfirmasi Tempat Tidur <span class="text-blue-500">` + getRs.hospitals[i].phone + `</span></p>
                  <a href="infors.php?tipe=1&rs=` + getRs.hospitals[i].id + `" class="hover:text-blue-600 hover:font-bold duration-100 text-blue-500">Cek Detail <i class="fas fa-angle-right"></i></a>
                  <p class="text-gray-400 text-sm">Terakhir ` + getRs.hospitals[i].info + `></p>
                </td>
              </tr>`;
                $('.table-rs').append(element);
                $('.loader').hide(100);
              }
            }
          }
        }
        if (valtipe === '2') {
          $('.table-rs').html('');
          let xhr = new XMLHttpRequest();
          xhr.open('GET', 'https://rs-bed-covid-api.vercel.app/api/get-hospitals?provinceid=' + valprov + '&cityid=' + valkota + '&type=2', true);
          xhr.send();
          xhr.onload = () => {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
              let data = xhr.response;
              getRs = JSON.parse(data);
              for (let i = 0; i < getBed.cities.length; i++) {
                const element = `<tr class="border-b-2">
                <td class="py-10">
                  <p class="font-bold w-1/2 text-left text-xl ">` + getRs.hospitals[i].name + `</p>
                  <div class="bg-yellow-500 flex justify-center items-center text-white rounded-md float-right" style="height: 40px; width: 50px; margin-top: -50px;">` + getRs.hospitals[i].available_beds.length + `</div>
                  <p>` + getRs.hospitals[i].address + `</p>
                  <p>Tersedia ` + getRs.hospitals[i].available_beds.length + ` Tempat Tidur Kosong</p>
                  <p class="text-left md:text-right">Konfirmasi Tempat Tidur <span class="text-blue-500">` + getRs.hospitals[i].phone + `</span></p>
                  <a href="infors.php?tipe=2&rs=` + getRs.hospitals[i].id + `" class="hover:text-blue-600 hover:font-bold duration-100 text-blue-500">Cek Detail <i class="fas fa-angle-right"></i></a>
                  <p class="text-gray-400 text-sm">Terakhir ` + getRs.hospitals[i].info + `></p>
                </td>
              </tr>`;
                $('.table-rs').append(element);
                $('.loader').hide(100);
              }
            }
          }
        }
      }
    });


    $('.data-table').DataTable();
    $('#DataTables_Table_0_filter input').attr('placeholder', 'Cari Data....');


    var ctx = document.getElementById("myChart").getContext('2d');
    var myChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ["Sembuh", "Positif", "Meninggal"],
        datasets: [{
          label: 'Data Grafik Bulan Ini',
          data: [`<?= $countDataCovid['jumlah_sembuh']; ?>`, `<?= $countDataCovid['jumlah_positif']; ?>`, `<?= $countDataCovid['jumlah_meninggal']; ?>`],
          backgroundColor: [
            'rgba(5,150,105, 0.8)',
            'rgba(245,158,11, 0.8)',
            'rgba(239,68,68, 0.8)',
          ],
          borderColor: [
            'rgba(99,255,132,1)',
            'rgba(201, 187, 0, 1)',
            'rgba(255, 50, 86, 1)',
          ],
          borderWidth: 1
        }]
      },
      options: {
        scales: {

        }
      }
    });

    $('.page-scroll').on('click', function(e) {

      var tujuan = $(this).attr('href');

      $('.page-scroll').removeClass('active')
      $(this).addClass('active')

      var elemenTujuan = $(tujuan);

      $('html , body').animate({
        scrollTop: elemenTujuan.offset().top - 120
      });

      e.preventDefault();
    });

    $('#circl').hide()
    $(window).scroll(function() {
      let wScroll = $(this).scrollTop();
      if (wScroll >= 500) {
        $('#circl').show(1000)
      } else {
        $('#circl').hide(500)
      }
    });
  </script>

</body>

</html>