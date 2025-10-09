// Buat objek untuk menampung jumlah kunjungan per bulan
const monthlyData = {};

// Loop semua data
allRom.forEach((item) => {
  const date = new Date(item.date_plan); // convert ke JS date
  const month = date.toLocaleString("id-ID", { month: "long" }); // ambil nama bulan

  // Hitung jumlah kunjungan tiap bulan
  if (!monthlyData[month]) {
    monthlyData[month] = 0;
  }
  monthlyData[month]++;
});

// Ambil label (bulan) dan value (jumlah)
const labels = Object.keys(monthlyData);
const values = Object.values(monthlyData);

var ctx = document.getElementById("mySalesMount");
var myBarChart = new Chart(ctx, {
  type: "bar",
  data: {
    labels: labels, // dari monthlyData
    datasets: [
      {
        label: "Jumlah Rombongan",
        backgroundColor: "rgba(2,117,216,1)",
        data: values, // jumlah per bulan
      },
    ],
  },
  options: {
    scales: {
      xAxes: [
        {
          gridLines: {
            display: false,
          },
          ticks: {
            maxTicksLimit: 12,
          },
        },
      ],
      yAxes: [
        {
          ticks: {
            beginAtZero: true,
            maxTicksLimit: 6,
          },
          gridLines: {
            display: true,
          },
        },
      ],
    },
    legend: {
      display: true,
    },
  },
});
