<?php
// Query untuk mengambil data laporan pemeliharaan (semua user)
$query = "SELECT 
            pi.id_pemeliharaan,
            pi.id_ikan,
            pi.id_kolam,
            tb.tanggal_tebar,
            di.jenis_bibit,
            GROUP_CONCAT(DISTINCT pi.nama_pakan ORDER BY pi.usia_pemberian_pakan_awal SEPARATOR '||') AS nama_pakan,
            GROUP_CONCAT(DISTINCT CONCAT(pi.usia_pemberian_pakan_awal, '-', pi.usia_pemberian_pakan_akhir, ' hari') ORDER BY pi.usia_pemberian_pakan_awal SEPARATOR '||') AS usia_pemberian_pakan,
            GROUP_CONCAT(DISTINCT pi.pakan_harian ORDER BY pi.usia_pemberian_pakan_awal SEPARATOR '||') AS pakan_harian,
            MAX(pi.usia_pemeliharaan) AS usia_pemeliharaan,
            tb.jumlah_bibit AS jumlah_awal,
            SUM(pi.jumlah_mati) AS jumlah_mati,
            (tb.jumlah_bibit - SUM(pi.jumlah_mati)) AS jumlah_hidup,
            pi.user_id,
            u.username
          FROM pemeliharaan_ikan pi
          JOIN tebar_bibit tb ON pi.id_ikan = tb.id_ikan AND pi.id_kolam = tb.id_kolam AND pi.user_id = tb.user_id
          JOIN data_ikan di ON pi.id_ikan = di.id_ikan AND pi.user_id = di.user_id
          LEFT JOIN user u ON pi.user_id = u.id
          GROUP BY pi.id_ikan, pi.id_kolam
          ORDER BY tb.tanggal_tebar DESC";

$result = mysqli_query($conn, $query);

// Debugging: Periksa hasil query
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<h2>Laporan Pemeliharaan (Semua User)</h2>
<div class="data-laporan">
<input type="text" id="search-input" placeholder="Cari Laporan Pemeliharaan...">
        <button id="search-button">Cari</button>
        <button id="print-pdf-button" class="print-button">Cetak PDF</button>
        <button id="print-excel-button" class="print-button">Cetak Excel</button>
    <div class="data-table">
        <table id="laporan-table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>ID Pemeliharaan</th>
                    <th>ID Ikan</th>
                    <th>ID Kolam</th>
                    <th>Tanggal Tebar</th>
                    <th>Jenis Bibit</th>
                    <th>Nama Pakan</th>
                    <th>Usia pemberian pakan</th>
                    <th>Usia pemeliharaan</th>
                    <th>Pakan Harian</th>
                    <th>Jumlah Awal</th>
                    <th>Jumlah mati</th>
                    <th>Jumlah Hidup</th>
                    <th>Username</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td><input type='checkbox' class='row-checkbox' value='" . $row['id_pemeliharaan'] . "'></td>";
                        echo "<td>" . htmlspecialchars($row['id_pemeliharaan']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['id_ikan']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['id_kolam']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['tanggal_tebar']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['jenis_bibit']) . "</td>";
                        echo "<td>" . str_replace("||", "<br>", htmlspecialchars($row['nama_pakan'])) . "</td>";
                        echo "<td>" . str_replace("||", "<br>", htmlspecialchars($row['usia_pemberian_pakan'])) . "</td>";
                        echo "<td>" . htmlspecialchars($row['usia_pemeliharaan']) . " hari</td>";
                        
                        // Perbaikan untuk kolom Pakan Harian
                        $pakan_harian = explode("||", $row['pakan_harian']);
                        echo "<td>";
                        foreach ($pakan_harian as $pakan) {
                            echo htmlspecialchars(number_format((float)$pakan, 2, '.', '')) . " kg<br>";
                        }
                        echo "</td>";
                        
                        echo "<td>" . htmlspecialchars($row['jumlah_awal']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['jumlah_mati']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['jumlah_hidup']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='14'>Tidak ada data pemeliharaan ikan</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const printPdfButton = document.getElementById('print-pdf-button');
    const printExcelButton = document.getElementById('print-excel-button');

    selectAllCheckbox.addEventListener('change', function() {
        rowCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updatePrintButtonState();
    });

    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updatePrintButtonState);
    });

    function updatePrintButtonState() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        printPdfButton.disabled = checkedBoxes.length === 0;
        printExcelButton.disabled = checkedBoxes.length === 0;
    }

    printPdfButton.addEventListener('click', function() {
        const selectedRows = getSelectedRows();
        if (selectedRows.length > 0) {
            generatePDF(selectedRows);
        }
    });

    printExcelButton.addEventListener('click', function() {
        const selectedRows = getSelectedRows();
        if (selectedRows.length > 0) {
            generateExcel(selectedRows);
        }
    });

    function getSelectedRows() {
        const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
        return Array.from(selectedCheckboxes).map(checkbox => checkbox.closest('tr'));
    }

    function generatePDF(rows) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'mm', 'a4'); // Landscape orientation

    // Judul
    doc.setFontSize(24);
    doc.setFont("helvetica", "bold");
    doc.text('Laporan Pemeliharaan', doc.internal.pageSize.width / 2, 20, { align: "center" });

    // Garis bawah judul
    doc.setLineWidth(0.5);
    doc.line(10, 25, doc.internal.pageSize.width - 10, 25);

    // Tabel
    doc.autoTable({
        head: [['ID Pemeliharaan', 'ID Ikan', 'ID Kolam', 'Tanggal Tebar', 'Jenis Bibit', 'Nama Pakan', 'Usia pemberian pakan', 'Usia pemeliharaan', 'Pakan Harian', 'Jumlah Awal', 'Jumlah mati', 'Jumlah Hidup']],
        body: rows.map(row => {
            const cells = Array.from(row.cells).slice(1);
            return cells.map((cell, index) => {
                if (index === 5 || index === 6 || index === 8) {
                    return cell.innerHTML.trim().split('<br>').map(text => text.trim());
                }
                return cell.textContent.trim();
            });
        }),
        startY: 35,
        margin: { left: 10, right: 10 },
        styles: { 
            fontSize: 8,
            cellPadding: 2,
            lineColor: [0, 0, 0],
            lineWidth: 0.1,
            halign: 'center',
            valign: 'middle'
        },
        headStyles: {
            fillColor: [173, 216, 150], // Light green color
            textColor: [0, 0, 0],
            fontStyle: 'bold',
        },
        bodyStyles: {
            fillColor: [220, 230, 241], // Light blue color
        },
        columnStyles: {
            0: { cellWidth: 25 },
            1: { cellWidth: 15 },
            2: { cellWidth: 15 },
            3: { cellWidth: 25 },
            4: { cellWidth: 25 },
            5: { cellWidth: 30 },
            6: { cellWidth: 30 },
            7: { cellWidth: 30 },
            8: { cellWidth: 30, cellPadding: { top: 5, bottom: 5 } }, // Add consistent padding for Pakan Harian
            9: { cellWidth: 20 },
            10: { cellWidth: 20 },
            11: { cellWidth: 20 },
        },
        didParseCell: function(data) {
            if (data.section === 'head') {
                data.cell.styles.fillColor = [173, 216, 150]; // Light green for header
            } else if (data.section === 'body') {
                data.cell.styles.fillColor = [220, 230, 241]; // Light blue for body
            }
        },
        willDrawCell: function(data) {
            if (data.section === 'body' && (data.column.index === 5 || data.column.index === 6 || data.column.index === 8)) {
                if (Array.isArray(data.cell.raw)) {
                    data.cell.text = data.cell.raw;
                }
            }
        },
    });

    doc.save('laporan_pemeliharaan.pdf');
}

    function generateExcel(rows) {
        const data = rows.map(row => Array.from(row.cells).slice(1).map(cell => cell.textContent.trim()));
        const ws = XLSX.utils.aoa_to_sheet([
            ['Laporan Pemeliharaan'],
            ['ID Pemeliharaan', 'ID Ikan', 'ID Kolam', 'Tanggal Tebar', 'Jenis Bibit', 'Nama Pakan', 'Usia pemberian pakan', 'Usia pemeliharaan', 'Pakan Harian', 'Jumlah Awal', 'Jumlah mati', 'Jumlah Hidup'],
            ...data
        ]);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Laporan Pemeliharaan");
        XLSX.writeFile(wb, 'laporan_pemeliharaan.xlsx');
    }
});
</script>