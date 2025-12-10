window.addEventListener("DOMContentLoaded", (event) => {
  // Simple-DataTables
  // https://github.com/fiduswriter/Simple-DataTables/wiki

  const datatablesSimple = document.getElementById("datatablesSimple");
  if (datatablesSimple) {
    new simpleDatatables.DataTable(datatablesSimple, {
      perPage: 10,
      perPageSelect: [5, 10, 25, 50],
      labels: {
        placeholder: "Cari...",
        perPage: "{select} data per halaman",
        noRows: "Tidak ada data",
        info: "Menampilkan {start} sampai {end} dari {rows} data",
      },
    });
  }
});
