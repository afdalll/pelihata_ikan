document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.querySelector(".menu-toggle");
    const closeMenu = document.querySelector(".close-menu");
    const sidebar = document.querySelector(".sidebar");
    const submenuToggles = document.querySelectorAll(".submenu-toggle");

    menuToggle.addEventListener("click", function () {
        sidebar.classList.add("open");
    });

    closeMenu.addEventListener("click", function () {
        sidebar.classList.remove("open");
    });

    submenuToggles.forEach(function (toggle) {
        toggle.addEventListener("click", function (event) {
            event.preventDefault();
            const parent = toggle.parentElement;
            parent.classList.toggle("open");
            const icon = toggle.querySelector('.fa-chevron-down');
            icon.classList.toggle('fa-chevron-up');
        });
    });

    // Tambahkan event listener untuk menutup sidebar saat mengklik di luar
    document.addEventListener("click", function (event) {
        if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
            sidebar.classList.remove("open");
        }
    });

    //===============================================//
    //==================master data==================//

    // Fungsi untuk menangani pencarian data
    function handleSearch() {
        const searchInput = document.getElementById('search-input');
        const filter = searchInput.value.toLowerCase();
        const tableRows = document.querySelectorAll('.data-table tbody tr');

        tableRows.forEach(function(row) {
            const cells = row.querySelectorAll('td');
            const matchFound = Array.from(cells).some(cell => 
                cell.textContent.toLowerCase().includes(filter)
            );
            row.style.display = matchFound ? '' : 'none';
        });
    }

    // Event listener untuk tombol pencarian
    const searchButton = document.getElementById('search-button');
    if (searchButton) {
        searchButton.addEventListener('click', handleSearch);
    }

    // Event listener untuk input pencarian (untuk pencarian real-time)
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', handleSearch);
    }
    
    // Fungsi untuk menangani penghapusan data
    function handleDelete(event) {
        event.preventDefault();
        const deleteId = this.getAttribute('data-id');
        const table = this.getAttribute('data-table');
        const returnPage = this.getAttribute('data-return-page');
        
        console.log("Delete button clicked", deleteId, table, returnPage); // Untuk debugging

        // Cek apakah ada data terkait
        fetch(`includes/check_related_data.php?table=${table}&id=${deleteId}`)
            .then(response => response.json())
            .then(data => {
                let confirmMessage = `Apakah Anda yakin ingin menghapus data ini?`;
                if (data.hasRelatedData) {
                    confirmMessage += `\nPeringatan: Menghapus data ini juga akan menghapus ${data.relatedDataInfo}.`;
                }
                if (confirm(confirmMessage)) {
                    submitDelete(deleteId, table, returnPage);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Terjadi kesalahan saat memeriksa data terkait.");
            });
    }

    function submitDelete(deleteId, table, returnPage) {
        fetch('includes/delete_data.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `delete_id=${deleteId}&table=${table}&return_page=${returnPage}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Data berhasil dihapus');
                location.reload();
            } else {
                alert('Gagal menghapus data: ' + data.message);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus data');
        });
    }

    // Tambahkan event listener untuk tombol delete
    const deleteButtons = document.querySelectorAll('.delete-button');
    deleteButtons.forEach(button => {
        button.addEventListener('click', handleDelete);
    });
   
});
