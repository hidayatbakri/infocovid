$(document).ready(function(){
  $('#tbl-data').DataTable();
  setInterval(function(){
    // TABEL DATA > LABEL PENCARIAN
    // $('.dataTables_filter').addClass('d-flex justify-content-end');
    $('.dataTables_filter').css('margin-top', '-40px');

    // TABEL DATA > INPUT PENCARIAN
    $('.dataTables_filter input').addClass('form-control');
    $(".dataTables_filter input").attr("placeholder","Cari Data Guru....");

    // dataTables_paginate 
    $('.dataTables_paginate').addClass('d-flex justify-content-between')
    $('span a.paginate_button').addClass('mx-2');
    $('span a.paginate_button').addClass('btn btn-outline-dark');

    // TOMBOL PREVIOUS dan next
    $('.previous').addClass('btn btn-outline-primary')
    $('.next').addClass('btn btn-outline-primary ')
  }, 500);
});