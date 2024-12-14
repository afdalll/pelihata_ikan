<?php
$user_id = $_SESSION['user_id'];

// Query untuk mengambil data laporan panen
$query = "SELECT 
            p.id_panen,
            p.id_ikan,
            p.id_kolam,
            p.tanggal_panen,
            di.jenis_bibit AS jenis_ikan,
            COALESCE(pi.usia_pemeliharaan, p.usia_pemeliharaan) AS usia_pemeliharaan,
            p.harga_per_kg,
            p.berat_total,
            p.harga_total
          FROM panen p
          JOIN data_ikan di ON p.id_ikan = di.id_ikan
          LEFT JOIN pemeliharaan_ikan pi ON p.id_ikan = pi.id_ikan AND pi.user_id = p.user_id
          WHERE p.user_id = ?
          GROUP BY p.id_panen
          ORDER BY p.tanggal_panen DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Debugging: Periksa hasil query
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<h2>Laporan Panen</h2>
<div class="data-laporan">
    <div class="data-laporan-header">
        <input type="text" id="search-input" placeholder="Cari Laporan Panen...">
        <button id="search-button">Cari</button>
        <button id="print-pdf-button" class="print-button">Cetak PDF</button>
        <button id="print-excel-button" class="print-button">Cetak Excel</button>
    </div>
    <div class="data-table">
        <table id="laporan-table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>ID Panen</th>
                    <th>ID Ikan</th>
                    <th>ID Kolam</th>
                    <th>Tanggal Panen</th>
                    <th>Jenis Ikan</th>
                    <th>Usia Pemeliharaan</th>
                    <th>Harga per/kg</th>
                    <th>Berat Total</th>
                    <th>Harga Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td><input type='checkbox' class='row-checkbox' value='" . $row['id_panen'] . "'></td>";
                        echo "<td>" . htmlspecialchars($row['id_panen']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['id_ikan']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['id_kolam']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['tanggal_panen']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['jenis_ikan']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['usia_pemeliharaan']) . " hari</td>";
                        echo "<td>Rp " . number_format($row['harga_per_kg'], 2, ',', '.') . "</td>";
                        echo "<td>" . number_format($row['berat_total'], 2, ',', '.') . " kg</td>";
                        echo "<td>Rp " . number_format($row['harga_total'], 2, ',', '.') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>Tidak ada data panen</td></tr>";
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
        doc.text('Laporan Panen', doc.internal.pageSize.width / 2, 20, { align: "center" });

        // Garis bawah judul
        doc.setLineWidth(0.5);
        doc.line(10, 25, doc.internal.pageSize.width - 10, 25);

        // Tabel
        doc.autoTable({
            head: [['ID Panen', 'ID Ikan', 'ID Kolam', 'Tanggal Panen', 'Jenis Ikan', 'Usia Pemeliharaan', 'Harga per/kg', 'Berat Total', 'Harga Total']],
            body: rows.map(row => {
                const cells = Array.from(row.cells).slice(1);
                return cells.map(cell => cell.textContent.trim());
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
                1: { cellWidth: 25 },
                2: { cellWidth: 25 },
                3: { cellWidth: 30 },
                4: { cellWidth: 30 },
                5: { cellWidth: 30 },
                6: { cellWidth: 30 },
                7: { cellWidth: 30 },
                8: { cellWidth: 30 },
            },
            tableWidth: 'wrap', // Adjust table width to content
            didDrawPage: function (data) {
                // Center the table horizontally
                const pageWidth = doc.internal.pageSize.width;
                const tableWidth = data.table.width;
                const marginLeft = (pageWidth - tableWidth) / 2;
                data.settings.margin.left = marginLeft;
            }
        });

        doc.save('laporan_panen.pdf');
    }

    function generateExcel(rows) {
        const data = rows.map(row => Array.from(row.cells).slice(1).map(cell => cell.textContent.trim()));
        const ws = XLSX.utils.aoa_to_sheet([
            ['Laporan Panen'],
            ['ID Panen', 'ID Ikan', 'ID Kolam', 'Tanggal Panen', 'Jenis Ikan', 'Usia Pemeliharaan', 'Harga per/kg', 'Berat Total', 'Harga Total'],
            ...data
        ]);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Laporan Panen");
        XLSX.writeFile(wb, 'laporan_panen.xlsx');
    }
});
</script>