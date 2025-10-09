// =========================================================================
// Isi dari file: ../../assets/demo/grafik.js
// Diasumsikan variabel global 'allRom' sudah didefinisikan di tag <script> sebelumnya.
// =========================================================================

document.addEventListener("DOMContentLoaded", function () {
  // Pastikan variabel allRom tersedia dan berupa Array
  if (typeof allRom === "undefined" || !Array.isArray(allRom)) {
    console.error("Variabel 'allRom' tidak ditemukan atau bukan array.");
    return;
  }

  const yearSelect = document.getElementById("yearSelect");

  // 1. Ambil semua tahun unik dari date_plan
  const years = [
    ...new Set(allRom.map((item) => new Date(item.date_plan).getFullYear())),
  ].sort();

  // 2. Isi dropdown tahun
  years.forEach((year) => {
    const opt = document.createElement("option");
    opt.value = year;
    opt.textContent = year;
    yearSelect.appendChild(opt);
  });

  // 3. Tentukan tahun default (tahun sekarang atau terakhir di data)
  const currentYear = new Date().getFullYear();
  yearSelect.value = years.includes(currentYear)
    ? currentYear
    : years[years.length - 1];

  // 4. Urutan nama bulan tetap
  const monthOrder = [
    "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember",
  ];

  // 5. Variabel global untuk menyimpan chart agar bisa di-destroy nanti
  let chartPerBulan = null;
  let chartPerSales = null;

  // Fungsi render semua grafik berdasarkan tahun
  function renderCharts(year) {
    // Destroy chart yang lama
    if (chartPerBulan) chartPerBulan.destroy();
    if (chartPerSales) chartPerSales.destroy();

    const filtered = allRom.filter(
      (item) => new Date(item.date_plan).getFullYear() === parseInt(year)
    );

    // ================ Grafik 1: Rombongan Per Bulan ====================
    const monthlyCount = {};
    filtered.forEach((item) => {
      const dateObj = new Date(item.date_plan);
      if (!isNaN(dateObj)) {
        const month = dateObj.toLocaleString("id-ID", { month: "long" });
        monthlyCount[month] = (monthlyCount[month] || 0) + 1;
      }
    });

    const labelsMonth = monthOrder.filter((m) =>
      Object.keys(monthlyCount).includes(m)
    );
    const valuesMonth = labelsMonth.map((m) => monthlyCount[m] || 0);

    const ctxMonth = document.getElementById("mySalesMount").getContext("2d");
    chartPerBulan = new Chart(ctxMonth, {
      type: "bar",
      data: {
        labels: labelsMonth,
        datasets: [
          {
            label: `Jumlah Rombongan per Bulan (${year})`,
            backgroundColor: "rgba(2,117,216,0.7)",
            borderColor: "rgba(2,117,216,1)",
            data: valuesMonth,
          },
        ],
      },
      options: {
        scales: {
          y: { beginAtZero: true, min: 0, ticks: { stepSize: 1 } },
        },
        plugins: { legend: { display: true } },
      },
    });

    // ================ Grafik 2: Rombongan Per Sales ====================
    const salesCount = {};
    filtered.forEach((item) => {
      const sales = item.marketing || "Tidak Ada Sales";
      salesCount[sales] = (salesCount[sales] || 0) + 1;
    });

    const labelsSales = Object.keys(salesCount);
    const valuesSales = Object.values(salesCount);
    const colors = labelsSales.map(
      (_, i) => `hsl(${(i * 60) % 360}, 70%, 50%)`
    );

    const ctxSales = document.getElementById("mySalesName").getContext("2d");
    chartPerSales = new Chart(ctxSales, {
      type: "bar",
      data: {
        labels: labelsSales,
        datasets: [
          {
            label: `Jumlah Rombongan per Sales (${year})`,
            backgroundColor: colors,
            data: valuesSales,
          },
        ],
      },
      options: {
        scales: {
          y: { beginAtZero: true, min: 0, ticks: { stepSize: 1 } },
        },
        plugins: { legend: { display: false } },
      },
    });
  }

  // 6. Render pertama kali (tahun default)
  renderCharts(yearSelect.value);

  // 7. Ubah grafik jika tahun diganti
  yearSelect.addEventListener("change", (e) => renderCharts(e.target.value));
});
